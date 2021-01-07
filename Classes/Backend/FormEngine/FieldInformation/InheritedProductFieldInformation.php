<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormEngine\FieldInformation;

use Pixelant\PxaProductManager\Utility\DataInheritanceUtility;
use TYPO3\CMS\Backend\Form\AbstractNode;
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

        if (!in_array(
            $this->data['fieldName'],
            DataInheritanceUtility::getInheritedFieldsForProductType((int)$this->data['databaseRow']['product_type']),
            true
        )) {
            return $result;
        }

        $result['html'] = htmlspecialchars(LocalizationUtility::translate(
            'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_be.xlf:formengine.inheritedfield'
        ));

        $result['html'] = '<div>' . $result['html'] . '</div>';

        return $result;
    }
}
