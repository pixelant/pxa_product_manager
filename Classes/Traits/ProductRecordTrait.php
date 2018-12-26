<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Traits;

use Pixelant\PxaProductManager\Utility\TCAUtility;

/**
 * Class ProductRecordTrait
 * @package Pixelant\PxaProductManager\Traits
 */
trait ProductRecordTrait
{
    /**
     * Convert json data to array from product DB row
     *
     * @param array $row
     * @return array
     */
    protected function getAttributesValuesFromRow(array $row): array
    {
        if (!empty($row[TCAUtility::ATTRIBUTES_VALUES_FIELD_NAME])) {
            return json_decode($row[TCAUtility::ATTRIBUTES_VALUES_FIELD_NAME], true);
        }

        return [];
    }
}
