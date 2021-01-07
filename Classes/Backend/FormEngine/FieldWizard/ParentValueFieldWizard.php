<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormEngine\FieldWizard;

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

        $label = LocalizationUtility::translate(
            'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_be.xlf:formengine.parentvalue.label'
        );

        $icon = GeneralUtility::makeInstance(IconFactory::class)->getIconForRecord(
            $this->data['tableName'],
            $this->data['databaseRow'],
            Icon::SIZE_SMALL
        );

        $parentRecord = $this->data['databaseRow']['parent'][0]['row'];

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
}
