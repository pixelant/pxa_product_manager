<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Configuration\Flexform;

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class StructureLoader
{
    /**
     * Default flexform loaded for all actions.
     *
     * @var string
     */
    public static string $defaultFlexform = 'EXT:pxa_product_manager/Configuration/FlexForms/Parts/flexform_common.xml';

    /**
     * Merge default flexform with action specific.
     *
     * @param array $dataStructure
     * @param array|null $actionConfiguration
     * @return array
     */
    public function defaultWithActionStructure(array $dataStructure, ?array $actionConfiguration): array
    {
        // Add default config
        $dataStructure = $this->loadDefaultDataStructure($dataStructure);

        // Load action structure
        $dataStructure = $this->loadActionDataStructure(
            $dataStructure,
            $actionConfiguration
        );

        return $dataStructure;
    }

    /**
     * Load all actions default data structure.
     *
     * @param array $dataStructure
     * @return array
     */
    public function loadDefaultDataStructure(array $dataStructure): array
    {
        return $this->updateDataStructureWithFlexform($dataStructure, static::$defaultFlexform);
    }

    /**
     * Load flexforms data structure from flexforms subparts.
     *
     * @param array $dataStructure
     * @param array|null $actionConfiguration
     * @return array
     */
    public function loadActionDataStructure(array $dataStructure, ?array $actionConfiguration): array
    {
        if ($actionConfiguration !== null) {
            // Load sub-form
            foreach ($actionConfiguration['flexforms'] as $flexform) {
                $dataStructure = $this->updateDataStructureWithFlexform($dataStructure, $flexform);
            }

            // Exclude fields
            foreach ($actionConfiguration['excludeFields'] as $excludeField) {
                foreach ($dataStructure['sheets'] as $sheet => $sheetConf) {
                    foreach ($sheetConf['ROOT']['el'] as $field => $fieldConf) {
                        if ($field === $excludeField) {
                            unset($dataStructure['sheets'][$sheet]['ROOT']['el'][$field]);
                        }
                    }
                }
            }
        }

        return $dataStructure;
    }

    /**
     * Update data structure.
     *
     * @param array $dataStructure
     * @param string $flexformPath
     * @return array
     */
    protected function updateDataStructureWithFlexform(array $dataStructure, string $flexformPath): array
    {
        $fullPath = GeneralUtility::getFileAbsFileName($flexformPath);
        if (!file_exists($fullPath)) {
            throw new \RuntimeException(
                "Could not find flexform with path '${fullPath}'(given path '${flexformPath}')",
                1570185225935
            );
        }

        $xml = file_get_contents($fullPath);
        ArrayUtility::mergeRecursiveWithOverrule($dataStructure, GeneralUtility::xml2array($xml));

        return $dataStructure;
    }
}
