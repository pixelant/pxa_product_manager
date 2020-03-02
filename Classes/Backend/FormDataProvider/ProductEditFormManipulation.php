<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormDataProvider;

use Pixelant\PxaProductManager\Attributes\ConfigurationProvider\ConfigurationProviderFactory;
use Pixelant\PxaProductManager\Attributes\ConfigurationProvider\ProviderInterface;
use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeSet;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\FlashMessage\BackendFlashMessage;
use Pixelant\PxaProductManager\Translate\CanTranslateInBackend;
use Pixelant\PxaProductManager\Utility\AttributeTcaNamingUtility;
use Pixelant\PxaProductManager\Utility\TcaUtility;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * Form data provider hook, add TCA on a fly
 *
 * @package Pixelant\PxaProductManager\Backend\FormDataProvider
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

            $product = $this->rawRowToProduct($row);

            if (!empty($product->getAllAttributesSets())) {
                $this->populateTCA($product->getAllAttributesSets(), $result['processedTca']);
                $this->simulateDataValues($product->getAllAttributesSets(), $result['databaseRow']);

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
     * Init
     */
    protected function init()
    {
        $this->dataMapper = GeneralUtility::makeInstance(ObjectManager::class)->get(DataMapper::class);
    }

    /**
     * DB row to model
     *
     * @param array $row
     * @return Product
     */
    protected function rawRowToProduct(array $row): Product
    {
        return $this->dataMapper->map(Product::class, [$row])[0];
    }

    /**
     * Configuration provider to given attribute
     *
     * @param Attribute $attribute
     * @return ProviderInterface
     */
    protected function getConfigurationProvider(Attribute $attribute): ProviderInterface
    {
        return ConfigurationProviderFactory::create($attribute);
    }

    /**
     * Add attributes configuration to TCA
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
                $field = AttributeTcaNamingUtility::translateAttributeToTcaFieldName($attribute);

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
     * Generate TCA tabs string with dynamic attributes
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
     * Simulate DB data for attributes
     *
     * @param Product $product
     * @param array $dbRow
     */
    protected function simulateDataValues(Product $product, array &$dbRow): void
    {
        $attributes = $this->collection($attributesSets)->pluck('attributes')->shiftLevel()->toArray();

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $field = TcaUtility::getAttributeTCAFieldName($attribute->getUid());

            if (array_key_exists($attribute->getUid(), $attributeUidToValue)) {
                switch ($attribute->getType()) {
                    case Attribute::ATTRIBUTE_TYPE_DROPDOWN:
                    case Attribute::ATTRIBUTE_TYPE_MULTISELECT:
                        $dbRow[$field] = GeneralUtility::trimExplode(
                            ',',
                            $attributeUidToValue[$attribute->getUid()],
                            true
                        );
                        break;
                    default:
                        $dbRow[$field] = $attributeUidToValue[$attribute->getUid()];
                }
            } elseif ($attribute->getDefaultValue()
                && $attribute->getType() !== Attribute::ATTRIBUTE_TYPE_MULTISELECT
            ) {
                $dbRow[$field] = $attribute->getDefaultValue();
            }
        }
    }

    /**
     * Set difference between translated and original product attribute values
     *
     * @param array $diffRow
     * @param array $defaultLanguageRow
     */
    protected function setDiffData(array &$diffRow, array &$defaultLanguageRow)
    {
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
     * Show notification message for user
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
