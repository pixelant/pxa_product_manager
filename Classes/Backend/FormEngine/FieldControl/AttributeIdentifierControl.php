<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormEngine\FieldControl;

use Pixelant\PxaProductManager\Translate\CanTranslateInBackend;
use TYPO3\CMS\Backend\Form\AbstractNode;

/**
 * @package Pixelant\PxaProductManager\Backend\FormEngine\FieldControl
 */
class AttributeIdentifierControl extends AbstractNode
{
    use CanTranslateInBackend;

    /**
     * Render field control
     * @return array
     */
    public function render(): array
    {
        return [
            'iconIdentifier' => 'actions-synchronize',
            'title' => $this->translate('tca.sync_attribute_identifier'),
            'linkAttributes' => [
                'class' => 'attributeIdentifier '
            ],
            'requireJsModules' => ['TYPO3/CMS/PxaProductManager/Backend/AttributeIdentifierControl'],
        ];
    }
}
