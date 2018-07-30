<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\ExtendedTca;

use Pixelant\PxaProductManager\Utility\MainUtility;

class ExtendedTca
{
    /**
     * @param $data
     * @return string
     */
    public function renderMultirowDataField($data) : string
    {
        $fieldConfig = $data['parameters']['fieldConfig'];

        $output = '<div class="col-md-8"><table class="table table-bordered table-condensed">';

        foreach ($fieldConfig['data'] as $name => $value) {
            $label = MainUtility::snakeCasePhraseToWords($name);
            $output .= "<tr><th>{$label}</th><td>{$value}</td></tr>";
        }

        $output .= '</table></div>';

        return $output;
    }
}
