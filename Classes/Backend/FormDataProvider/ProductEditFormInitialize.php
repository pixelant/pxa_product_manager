<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormDataProvider;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeSet;
use Pixelant\PxaProductManager\Utility\AttributeHolderUtility;
use Pixelant\PxaProductManager\Utility\ProductUtility;
use Pixelant\PxaProductManager\Utility\TCAUtility;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Form data provider hook, add TCA on a fly
 *
 * @package Pixelant\PxaProductManager\Backend\FormDataProvider
 */
class ProductEditFormInitialize implements FormDataProviderInterface
{
    /**
     * Hold static configuration for attributes fields
     *
     * @var array
     */
    protected $attributeTCAConfiguration = [];

    /**
     * Initialize some vars
     */
    public function __construct()
    {
        $this->attributeTCAConfiguration = TCAUtility::getDefaultAttributesTCAConfiguration();
    }

    /**
     * @param array $result
     * @return array
     */
    public function addData(array $result): array
    {
        if ($result['tableName'] === 'tx_pxaproductmanager_domain_model_product'
            && !StringUtility::beginsWith($result['databaseRow']['uid'], 'NEW')
        ) {
            /** @var AttributeHolderUtility $attributeHolder */
            $attributeHolder = GeneralUtility::makeInstance(AttributeHolderUtility::class);
            $attributeHolder->start(ProductUtility::getProductCategoriesUids($result['databaseRow']['uid']));

            if ($attributeHolder->getAttributes()->count()) {
                $this->populateTCA($attributeHolder->getAttributeSets()->toArray(), $result['processedTca']);
                $this->simulateDataValues($attributeHolder->getAttributes()->toArray(), $result['databaseRow']);

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
            }
        }

        return $result;
    }

    /**
     * Add attributes configuration to TCA
     *
     * @param array $attributesSets
     * @param array &$tca
     */
    protected function populateTCA(array $attributesSets, array &$tca)
    {
        $productAttributes = [];

        /** @var AttributeSet $attributesSet */
        foreach ($attributesSets as $attributesSet) {
            // Populate TCA
            /** @var Attribute $attribute */
            foreach ($attributesSet->getAttributes() as $attribute) {
                $attributeType = $attribute->getType();
                $attributeUid = $attribute->getUid();

                // @codingStandardsIgnoreStart
                $field = ($attributeType === Attribute::ATTRIBUTE_TYPE_IMAGE ? Attribute::TCA_ATTRIBUTE_IMAGE_PREFIX : '')
                    . Attribute::TCA_ATTRIBUTE_PREFIX . $attributeUid; // Unique for each field
                // @codingStandardsIgnoreEnd
                // Get TCA from TCAConf array defined in TCADefinitions.php
                if ($attributeType == Attribute::ATTRIBUTE_TYPE_IMAGE) {
                    $tcaConfigurationField = TCAUtility::getImageFieldTCAConfiguration(
                        $field,
                        $attributeUid,
                        $attribute->getName()
                    );
                } else {
                    $tcaConfigurationField = $this->attributeTCAConfiguration[$attributeType];
                }
                $tca['columns'][$field] = $tcaConfigurationField;
                // Add it also to global array
                // @codingStandardsIgnoreStart
                $GLOBALS['TCA']['tx_pxaproductmanager_domain_model_product']['columns'][$field] = $tcaConfigurationField;
                // @codingStandardsIgnoreEnd

                // Make changes to the default TCA according to the attribute object
                $tca['columns'][$field]['label'] = $attribute->getName();

                // Set default value
                if ($defaultValue = $attribute->getDefaultValue()) {
                    $tca['columns'][$field]['config']['default'] = $defaultValue;
                }

                if ($attribute->getRequired()) {
                    switch ($attributeType) {
                        case Attribute::ATTRIBUTE_TYPE_LINK:
                        case Attribute::ATTRIBUTE_TYPE_IMAGE:
                        case Attribute::ATTRIBUTE_TYPE_MULTISELECT:
                            $tca['columns'][$field]['config']['minitems'] = 1;
                            break;
                        default:
                            $tca['columns'][$field]['config']['eval'] =
                                $tca['columns'][$field]['config']['eval'] ?
                                    $tca['columns'][$field]['config']['eval'] . ', required' : 'required';
                    }
                }

                // Additional TCA modifications depending on Attribute Type
                switch ((int)$attributeType) {
                    case Attribute::ATTRIBUTE_TYPE_DROPDOWN:
                    case Attribute::ATTRIBUTE_TYPE_MULTISELECT:
                        /** @var QueryBuilder $queryBuilder */
                        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
                            'tx_pxaproductmanager_domain_model_option'
                        );

                        $statement = $queryBuilder
                            ->select('uid', 'value')
                            ->from('tx_pxaproductmanager_domain_model_option')
                            ->where(
                                $queryBuilder->expr()->eq(
                                    'attribute',
                                    $queryBuilder->createNamedParameter($attributeUid)
                                )
                            )
                            ->execute();

                        $options = [];
                        while ($row = $statement->fetch()) {
                            $options[] = [$row['value'], $row['uid']];
                        }

                        // @codingStandardsIgnoreStart
                        if (empty($options)) {
                            $tca['columns'][$field]['label'] .= ' (This attribute has no options. Please configure the attribute and add some options to it.)';
                        }
                        // @codingStandardsIgnoreEnd
                        $tca['columns'][$field]['config']['items'] = $options;
                        // Add it also to global array
                        // @codingStandardsIgnoreStart
                        $GLOBALS['TCA']['tx_pxaproductmanager_domain_model_product']['columns'][$field]['config']['items'] = $options;
                        // @codingStandardsIgnoreEnd
                        break;
                    default:
                        break;
                }

                // Array with all additional attributes
                $productAttributes[$attributesSet->getUid()]['fields'][] = $field;
            }

            $productAttributes[$attributesSet->getUid()]['label'] = $attributesSet->getName();
        }

        if (!empty($productAttributes)) {
            $productAttributesShow = '';
            // @codingStandardsIgnoreStart
            $defaultLabel = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:tx_pxaproductmanager_domain_model_product.tab.attributes';
            // @codingStandardsIgnoreEnd
            foreach ($productAttributes as $productAttribute) {
                if (!empty($productAttribute['fields'])) {
                    $fieldsList = implode(', ', $productAttribute['fields']);
                    $tca['interface']['showRecordFieldList'] .= ', ' . $fieldsList;
                    $productAttributesShow .= ',--div--;' . ($productAttribute['label'] ?: $defaultLabel) . ',';
                    $productAttributesShow .= $fieldsList;
                }
            }

            foreach ($tca['types'] as &$type) {
                $type = str_replace(
                    ',--palette--;;paletteAttributes',
                    $productAttributesShow,
                    $type
                );
            }
        }
    }

    /**
     * Simulate DB data for attributes
     *
     * @param array $attributes
     * @param array $dbRow
     */
    protected function simulateDataValues(array $attributes, array &$dbRow)
    {
        $attributeUidToValue = [];

        if (!empty($dbRow['serialized_attributes_values'])) {
            $attributeUidToValue = unserialize($dbRow['serialized_attributes_values']);
        }

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $field = Attribute::TCA_ATTRIBUTE_PREFIX . $attribute->getUid();

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
                && $attribute->getType() !== Attribute::ATTRIBUTE_TYPE_MULTISELECT) {
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
            $field = Attribute::TCA_ATTRIBUTE_PREFIX . $attributeUid;
            $diffRow[$field] = $attributeValue;
            $defaultLanguageRow[$field] = $attributeValue;
        }
    }
}
