<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormEngine\FieldInformation;

use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Utility\DataInheritanceUtility;
use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Implements a field wizard that displays the parent product's field values.
 */
class InheritedProductFieldInformation extends AbstractNode
{
    /**
     * @return array
     */
    public function render(): array
    {
        $result = $this->initializeResultArray();

        if ($this->data['tableName'] === ProductRepository::TABLE_NAME) {
            $fieldName = $this->data['fieldName'];
            $productType = $this->data['databaseRow']['product_type'];
        } elseif ($this->data['tableName'] === AttributeValueRepository::TABLE_NAME) {
            $attributeValue = $this->data['databaseRow'];

            $product = BackendUtility::getRecord(
                ProductRepository::TABLE_NAME,
                $attributeValue['product']
            );

            $fieldName = 'attribute.' . $attributeValue['attribute'][0];
            $productType = $product['product_type'];
        } else {
            return $result;
        }

        if (
            !in_array(
                $fieldName,
                DataInheritanceUtility::getInheritedFieldsForProductType((int)$productType),
                true
            )
        ) {
            return $result;
        }

        $result['html'] = htmlspecialchars(LocalizationUtility::translate(
            'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_be.xlf:formengine.inheritedfield'
        ));

        return $result;
    }
}
