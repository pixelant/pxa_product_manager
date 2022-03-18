<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Hook\ItemsProcFunc;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Miscellaneous methods to provide data to the TCA.
 */
class GeneralItemsProcFunc
{
    /**
     * Returns an array of ["field_name", "LLL:field label"] pairs from a table.
     *
     * $configuration[itemsProcConfig] contains configuration:
     *
     *     `table` (required) Name of the table to return fields for
     *     `exclude` comma separated list of fields to exclude
     *
     * @param array $configuration
     * @return array
     */
    public function getFieldsForTable(array $configuration): array
    {
        $tableName = $configuration['config']['itemsProcConfig']['table'];

        $columns = $GLOBALS['TCA'][$tableName]['columns'];

        $columns = array_filter(
            $columns,
            fn ($column) => $column['config']['type'] !== 'passthrough'
        );

        $excludeFields = [
            $GLOBALS['TCA'][$tableName]['ctrl']['languageField'],
            $GLOBALS['TCA'][$tableName]['ctrl']['transOrigPointerField'],
            $GLOBALS['TCA'][$tableName]['ctrl']['type'] ?? [],
        ];

        if ($configuration['config']['itemsProcConfig']['exclude'] ?? '' !== '') {
            $excludeFields = array_merge($excludeFields, GeneralUtility::trimExplode(
                ',',
                $configuration['config']['itemsProcConfig']['exclude'],
                true
            ));
        }

        $columns = array_filter(
            $columns,
            fn ($key) => !in_array($key, $excludeFields, true),
            ARRAY_FILTER_USE_KEY
        );

        $configuration['items'] = array_map(
            fn ($label, $fieldName) => [$label, $fieldName, $GLOBALS['TCA'][$tableName]['ctrl']['iconfile']],
            array_column($columns, 'label'),
            array_keys($columns),
        );

        return $configuration;
    }
}
