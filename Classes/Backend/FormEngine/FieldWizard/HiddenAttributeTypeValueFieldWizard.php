<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormEngine\FieldWizard;

use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Implements a field wizard that displays the parent product's field values.
 */
class HiddenAttributeTypeValueFieldWizard extends AbstractNode
{
    /**
     * @return array
     */
    public function render(): array
    {
        $result = $this->initializeResultArray();

        // We only do this on NEW records
        if (MathUtility::canBeInterpretedAsInteger($this->data['databaseRow']['uid'])) {
            return $result;
        }

        $html = '<input type="hidden" ';
        $html .= 'name="data[' . AttributeValueRepository::TABLE_NAME . ']';
        $html .= '[' . $this->data['databaseRow']['uid'] . '][attribute]" ';
        $html .= 'value="' . htmlspecialchars((string)$this->data['databaseRow']['attribute'][0]) . '" ';
        $html .= '/>';

        $result['html'] = $html;

        return $result;
    }
}
