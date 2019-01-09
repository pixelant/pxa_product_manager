<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormEngine\FieldControl;

use Pixelant\PxaProductManager\Traits\TranslateBeTrait;
use TYPO3\CMS\Backend\Form\AbstractNode;

/**
 * Class AttributeIdentifierControl
 * @package Pixelant\PxaProductManager\Backend\FormEngine\FieldControl
 */
class AttributeIdentifierControl extends AbstractNode
{
    use TranslateBeTrait;

    /**
     * Render field control
     * @return array
     */
    public function render(): array
    {
        $result = [
            'iconIdentifier' => 'actions-synchronize',
            'title' => $this->translate('tca.sync_attribute_identifier'),
            'linkAttributes' => [
                'class' => 'attributeIdentifier '
            ],
            'requireJsModules' => ['TYPO3/CMS/PxaProductManager/Backend/AttributeIdentifierControl'],
        ];

        return $result;
    }
}
