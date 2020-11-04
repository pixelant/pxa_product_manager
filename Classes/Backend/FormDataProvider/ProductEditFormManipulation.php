<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormDataProvider;

use Pixelant\PxaProductManager\Attributes\ConfigurationProvider\ConfigurationProviderFactory;
use Pixelant\PxaProductManager\Attributes\ConfigurationProvider\ProviderInterface;
use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeSet;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\FlashMessage\BackendFlashMessage;
use Pixelant\PxaProductManager\Translate\CanTranslateInBackend;
use Pixelant\PxaProductManager\Utility\AttributeTcaNamingUtility;
use Pixelant\PxaProductManager\Utility\TcaUtility;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * Form data provider hook, add TCA on a fly.
 */
class ProductEditFormManipulation implements FormDataProviderInterface
{
    use CanTranslateInBackend, CanCreateCollection;

    /**
     * @var DataMapper
     */
    protected DataMapper $dataMapper;

    /**
     * @var BackendFlashMessage
     */
    protected BackendFlashMessage $flashMessage;

    /**
     * @param BackendFlashMessage $flashMessage
     */
    public function __construct(BackendFlashMessage $flashMessage = null)
    {
        $this->flashMessage = $flashMessage ?? GeneralUtility::makeInstance(BackendFlashMessage::class);
    }

    /**
     * @param array $result
     * @return array
     */
    public function addData(array $result): array
    {
        if ($result['tableName'] !== 'tx_pxaproductmanager_domain_model_product') {
            return $result;
        }

        $row = $result['databaseRow'];
        $isNew = StringUtility::beginsWith($row['uid'], 'NEW');

        if (!$isNew) {
            $this->init();
            // Fetch product raw data with getRecord instead of using $result['databaseRow']
            // the "rawRow" is different in databaseRow and can throw errors when mapped
            $record = BackendUtility::getRecord($result['tableName'], $row['uid']);
            $product = $this->rawRowToProduct($record);

            // Save result. This is not cached
            $attributesSets = $product->_getAllAttributesSets();

            if (!empty($attributesSets)) {
                $this->populateTCA($attributesSets, $result['processedTca']);
                $this->simulateDataValues($product, $attributesSets, $result['databaseRow']);

                if (is_array($result['defaultLanguageDiffRow'])) {
                    $diffKey = sprintf(
                        '%s:%d',
                        $result['tableName'],
                        $result['databaseRow']['uid']
                    );

                    if (array_key_exists($diffKey, $result['defaultLanguageDiffRow'])) {
                        $this->setDiffData(
                            $result['defaultLanguageDiffRow'][$diffKey],
                            $result['defaultLanguageRow']
                        );
                    }
                }
            } else {
                $this->showNotificationMessage('tca.notification_no_attributes_available');
            }
        } else {
            $this->showNotificationMessage('tca.notification_first_save');
        }

        return $result;
    }

    /**
     * Init.
     */
    protected function init(): void
    {
        $this->dataMapper = GeneralUtility::makeInstance(ObjectManager::class)->get(DataMapper::class);
    }

    /**
     * DB row to model.
     *
     * @param array $row
     * @return Product
     */
    protected function rawRowToProduct(array $row): Product
    {
        return $this->dataMapper->map(Product::class, [$row])[0];
    }

    /**
     * Configuration provider to given attribute.
     *
     * @param Attribute $attribute
     * @return ProviderInterface
     */
    protected function getConfigurationProvider(Attribute $attribute): ProviderInterface
    {
        return ConfigurationProviderFactory::create($attribute);
    }

    /**
     * Add attributes configuration to TCA.
     *
     * @param array $attributesSets
     * @param array &$tca
     */
    protected function populateTCA(array $attributesSets, array &$tca): void
    {
        $dynamicAttributesSets = [];

        // Generate TCA
        /** @var AttributeSet $attributesSet */
        foreach ($attributesSets as $attributesSet) {
            $setFields = [];

            /** @var Attribute $attribute */
            foreach ($attributesSet->getAttributes() as $attribute) {
                $attributeTCA = $this->getConfigurationProvider($attribute)->get();
                $field = AttributeTcaNamingUtility::translateToTcaFieldName($attribute);

                $tca['columns'][$field] = $attributeTCA;

                // Add it also to global array
                // @codingStandardsIgnoreStart
                $GLOBALS['TCA']['tx_pxaproductmanager_domain_model_product']['columns'][$field] = $attributeTCA;
                // @codingStandardsIgnoreEnd

                // Add attribute field to set
                $setFields[] = $field;
            }

            // Add fields set to rest
            if (!empty($setFields)) {
                $dynamicAttributesSets[$attributesSet->getUid()] = [
                    'label' => $attributesSet->getName(),
                    'fields' => $setFields,
                ];
            }
        }

        // Add dynamic fields to products TCA
        if (!empty($dynamicAttributesSets)) {
            $tabsTCA = $this->dynamicTabsTca($dynamicAttributesSets);

            foreach ($tca['types'] as &$type) {
                $type = str_replace(
                    ',--palette--;;paletteAttributes',
                    $tabsTCA,
                    $type
                );
            }
        }
    }

    /**
     * Generate TCA tabs string with dynamic attributes.
     *
     * @param array $dynamicAttributesSets
     * @return string
     */
    protected function dynamicTabsTca(array $dynamicAttributesSets): string
    {
        $tcaTabsString = '';

        foreach ($dynamicAttributesSets as $dynamicAttributesSet) {
            $tcaTabsString .= sprintf(
                ',--div--;%s, %s',
                $dynamicAttributesSet['label'],
                implode(', ', $dynamicAttributesSet['fields'])
            );
        }

        return $tcaTabsString;
    }

    /**
     * Simulate DB data for attributes.
     *
     * @param Product $product
     * @param array $attributesSets
     * @param array $dbRow
     */
    protected function simulateDataValues(Product $product, array $attributesSets, array &$dbRow): void
    {
        // Exclude FAL attributes
        $attributes = $this->collection($attributesSets)
            ->pluck('attributes')
            ->shiftLevel()
            ->filter(fn (Attribute $attribute) => $attribute->isFalType() === false)
            ->toArray();

        /** @var AttributeValue[] $values */
        $values = $this->collection($product->getAttributesValuesWithValidAttributes())
            ->mapWithKeysOfProperty('attribute', fn (Attribute $valueAttribute) => $valueAttribute->getUid())
            ->toArray();

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $field = AttributeTcaNamingUtility::translateToTcaFieldName($attribute);

            if (array_key_exists($attribute->getUid(), $values)) {
                /** @var AttributeValue $value */
                $value = $values[$attribute->getUid()];

                $dbRow[$field] = $attribute->isSelectBoxType()
                    ? GeneralUtility::trimExplode(',', $value->getValue(), true)
                    : $value->getValue();
            } elseif (!$attribute->isSelectBoxType()) {
                $dbRow[$field] = $attribute->getDefaultValue();
            }
        }
    }

    /**
     * Set difference between translated and original product attribute values.
     *
     * @param array $diffRow
     * @param array $defaultLanguageRow
     */
    protected function setDiffData(array &$diffRow, array &$defaultLanguageRow): void
    {
        // TODO implementation
        die(__METHOD__);

        $attributeUidToValues = [];

        if (!empty($diffRow['serialized_attributes_values'])) {
            $attributeUidToValues = unserialize($diffRow['serialized_attributes_values']);
        }

        foreach ($attributeUidToValues as $attributeUid => $attributeValue) {
            $field = TcaUtility::getAttributeTCAFieldName($attributeUid);
            $diffRow[$field] = $attributeValue;
            $defaultLanguageRow[$field] = $attributeValue;
        }
    }

    /**
     * Show notification message for user.
     *
     * @param string $label
     */
    protected function showNotificationMessage(string $label): void
    {
        $this->flashMessage->flash(
            $this->translate($label),
            $this->translate('tca.notification_title')
        );
    }
}
