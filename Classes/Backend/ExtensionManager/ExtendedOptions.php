<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\ExtensionManager;

use Pixelant\PxaProductManager\Utility\ConfigurationUtility;

class ExtendedOptions
{
    /**
     * @param $data
     * @return string
     */
    public function checkoutSystemsSelector($args) : string
    {
        $checkoutSystems = ConfigurationUtility::getCheckoutSystems();

        $output = '';
        $output .= "<div class='form-group'><select class='form-control' name='{$args['fieldName']}'>";

        foreach ($checkoutSystems as $checkoutSystemName => $checkoutSystem) {
            $selected = $args['fieldValue'] === $checkoutSystemName ? 'selected="selected"' : '';
            $output .= "<option {$selected} value='{$checkoutSystemName}'>{$checkoutSystemName}</option>";
        }

        $output .= '</select></div>';

        return $output;
    }
}
