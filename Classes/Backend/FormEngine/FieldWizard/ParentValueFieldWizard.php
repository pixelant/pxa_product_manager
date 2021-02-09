<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormEngine\FieldWizard;

use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Utility\AttributeUtility;
use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Utility\BackendUtility;
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

        if ($this->data['tableName'] === ProductRepository::TABLE_NAME) {
            $tableName = $this->data['tableName'];
            $fieldName = $this->data['fieldName'];
            $record = $this->data['databaseRow'];
            $parentRecord = $this->data['databaseRow']['parent'][0]['row'];
        } elseif ($this->data['tableName'] === AttributeValueRepository::TABLE_NAME) {
            $tableName = $this->data['tableName'];
            $fieldName = 'value';
            $record = $this->data['databaseRow'];
            $parentRecord = AttributeUtility::findAttributeValue(
                (int)$record['product'],
                (int)$record['attribute'][0]
            );
        } else {
            return $result;
        }

        if (!$parentRecord) {
            return $result;
        }

        $label = LocalizationUtility::translate(
            'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_be.xlf:formengine.parentvalue.label'
        );

        $icon = GeneralUtility::makeInstance(IconFactory::class)->getIconForRecord(
            $tableName,
            $record,
            Icon::SIZE_SMALL
        );

        $processedParentValue = BackendUtility::getProcessedValueExtra(
            $tableName,
            $fieldName,
            $parentRecord[$fieldName]
        );

        $html = '<div class="bg-info" style="padding: .2em" title="' . htmlspecialchars($label) . '">';
        $html .= $icon . ' <strong>' . htmlspecialchars($label) . ':</strong> ';
        $html .= htmlspecialchars((string)$processedParentValue);
        $html .= '</div>';

        $result['html'] = $html;

        return $result;
    }
}
