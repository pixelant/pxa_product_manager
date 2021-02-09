<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormEngine\FieldWizard;

use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Implements a field wizard that displays the parent product's field values.
 */
class ParentValueFieldWizard extends AbstractNode
{
    /**
     * @var array[] '<productId>' => ['<attributeId>' => [<attributeValueRecord>]]
     */
    protected static array $attributeValueCache = [];

    /**
     * @return array
     */
    public function render(): array
    {
        $result = $this->initializeResultArray();

        if (!$this->data['databaseRow']['parent'][0]) {
            return $result;
        }

        $fieldName = $this->data['fieldName'];
        $fieldConfig = $this->data['processedTca']['columns'][$fieldName];
        $parentRecord = $this->data['databaseRow']['parent'][0]['row'];

        $label = LocalizationUtility::translate(
            'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_be.xlf:formengine.parentvalue.label'
        );

        $icon = GeneralUtility::makeInstance(IconFactory::class)->getIconForRecord(
            $this->data['tableName'],
            $this->data['databaseRow'],
            Icon::SIZE_SMALL
        );

        $processedParentValue = BackendUtility::getProcessedValueExtra(
            $this->data['tableName'],
            $this->data['fieldName'],
            $parentRecord[$fieldName]
        );

        $html = '<div class="bg-info" style="padding: .2em" title="' . htmlspecialchars($label) . '">';
        $html .= $icon . ' <strong>' . htmlspecialchars($label) . ':</strong> ';
        $html .= htmlspecialchars((string)$processedParentValue);
        $html .= '</div>';

        $result['html'] = $html;

        return $result;
    }

    /**
     * Return an attribute value record.
     *
     * @param int $attributeId The attribute ID
     * @param int $productId The product ID
     * @return array|null
     */
    protected function getAttributeValueOfAttributeForProduct(int $attributeId, int $productId): ?array
    {
        return $this->getAttributeValuesForProduct($productId)[$attributeId];
    }

    /**
     * Return an array of attribute values for product. Attribute type as key.
     *
     * @param int $productId
     * @return array of attribute value records (type as key)
     */
    protected function getAttributeValuesForProduct(int $productId): array
    {
        if (isset(self::$attributeValueCache[$productId])) {
            return self::$attributeValueCache[$productId];
        }

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(AttributeValueRepository::TABLE_NAME);

        $result = $queryBuilder
            ->select('*')
            ->from(AttributeValueRepository::TABLE_NAME)
            ->where($queryBuilder->expr()->eq('product', $productId))
            ->execute()
            ->fetchAllAssociative();

        if (!is_array($result)) {
            $result = [];
        }

        $attributeValues = [];
        foreach ($result as $attributeValue) {
            $attributeValues[$attributeValue['attribute']] = $attributeValue;
        }

        self::$attributeValueCache[$productId] = $attributeValues;

        return self::$attributeValueCache[$productId];
    }
}
