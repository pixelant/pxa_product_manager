<?php

declare(strict_types=1);


namespace Pixelant\PxaProductManager\Backend\FormEngine\FieldWizard;


use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Implements a field wizard that displays the parent product's field values
 */
class ParentValueFieldWizard extends AbstractNode
{
    /**
     *
     *
     * @return array
     */
    public function render(): array
    {
        $fieldName = $this->data['fieldName'];
        $fieldConfig = $this->data['processedTca']['columns'][$fieldName];

        $result = $this->initializeResultArray();

        if ($fieldConfig['config']['type'] === 'inline'
            || $fieldConfig['config']['type'] === 'flex'
        ) {
            // TODO: Don't return, but render items as record labels using BackendUtility::getRecordTitle
            // TODO: and record icon.
            return $result;
        }

        $label = LocalizationUtility::translate(
            'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_be.xlf:formengine.parentvalue.label'
        );

        $icon = GeneralUtility::makeInstance(IconFactory::class)->getIconForRecord(
            $this->data['tableName'],
            $this->data['databaseRow'],
            Icon::SIZE_SMALL
        );

        // TODO: Get parent record
        $parentRecord = $this->data['databaseRow'];
        $parentValue = $parentRecord[$fieldName];

        $html = '<div class="bg-info" style="padding: .2em" title="' . htmlspecialchars($label) . '">';
        $html .= $icon . ' ' . htmlspecialchars(strip_tags($parentValue));
        $html .= '</div>';

        $result['html'] = $html;

        return $result;
    }

}
