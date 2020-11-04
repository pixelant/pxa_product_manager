<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Hook;

use Pixelant\PxaProductManager\Configuration\Flexform\Registry;
use Pixelant\PxaProductManager\Configuration\Flexform\StructureLoader;
use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Translate\CanTranslateInBackend;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\Exception\MissingArrayPathException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook to display verbose information about pi1 plugin in Web>Page module.
 */
class PageLayoutView
{
    use CanTranslateInBackend, CanCreateCollection;

    /**
     * HR tag.
     *
     * @var string
     */
    protected static string $hrMarkup = '<hr style="margin: 5px 0;background: #ccc">';

    /**
     * @var StructureLoader
     */
    protected StructureLoader $loader;

    /**
     * @var Registry
     */
    protected Registry $registry;

    /**
     * @param StructureLoader $loader
     * @param Registry $registry
     */
    public function __construct(StructureLoader $loader = null, Registry $registry = null)
    {
        $this->loader = $loader ?? GeneralUtility::makeInstance(StructureLoader::class);
        $this->registry = $registry ?? GeneralUtility::makeInstance(Registry::class);
    }

    /**
     * Returns information about this extension's pi1 plugin.
     *
     * @param array $params Parameters to the hook
     * @return string Information about pi1 plugin
     */
    public function getExtensionSummary(array $params): string
    {
        $result = sprintf(
            '<strong>%s</strong><br>',
            $this->translate('be.extension_info.name')
        );

        $additionalInfo = '';

        if ($params['row']['list_type'] === 'pxaproductmanager_pi1') {
            $flexFormSettings = GeneralUtility::makeInstance(FlexFormService::class)->convertFlexFormContentToArray(
                $params['row']['pi_flexform']
            );

            $switchableControllerActions = $flexFormSettings['switchableControllerActions'];
            if (!empty($switchableControllerActions)) {
                $additionalInfo .= $this->modeLabel($switchableControllerActions);

                $fieldsGroups = $this->getFlexformGroupedFields($switchableControllerActions);

                foreach ($fieldsGroups as $fieldsGroup) {
                    $additionalInfo .= '<b>' . $this->translate($fieldsGroup['label']) . '</b><br>';

                    // Get value for each field
                    foreach ($fieldsGroup['el'] as $fieldName => $fieldConfiguration) {
                        $additionalInfo .= $this->getFieldInformation(
                            $fieldName,
                            $fieldConfiguration,
                            $flexFormSettings
                        );
                    }

                    $additionalInfo .= static::$hrMarkup;
                }
            } else {
                $additionalInfo .= $this->translate('be.extension_info.mode.not_configured');
            }
        }

        return $result . ($additionalInfo ? '<hr><pre>' . $additionalInfo . '</pre>' : '');
    }

    /**
     * Information about plugin mode.
     *
     * @param string $switchableControllerActions
     * @return string
     */
    protected function modeLabel(string $switchableControllerActions): string
    {
        $actions = $this->registry->getAllRegisteredActions();
        if (isset($actions[$switchableControllerActions])) {
            return sprintf(
                '<b>%s</b>: %s<br>%s',
                $this->translate('flexform.mode'),
                $this->translate($actions[$switchableControllerActions]['label']),
                static::$hrMarkup
            );
        }

        return '';
    }

    /**
     * Parse single field information.
     *
     * @param string $name
     * @param array $config
     * @param array $values
     * @return string
     */
    protected function getFieldInformation(string $name, array $config, array $values): string
    {
        $info = '<b>' . $this->translate($config['label']) . '</b>: %s<br>';

        try {
            $value = ArrayUtility::getValueByPath($values, $name, '.');
        } catch (MissingArrayPathException $exception) {
            return sprintf($info, $this->translate('be.extension_info.none'));
        }
        $config = $config['config'];

        switch ($config['type']) {
            case 'group':
                $value = $this->getValueFromDB($config['allowed'], (int)$value);

                break;
            case 'check':
                $value = $this->getCheckboxValue((bool)$value);

                break;
            case 'select':
                $value = $this->getSelectBoxValue($config, $value);

                break;
            case 'input':
                $value = (string)$value;

                break;
            default:
                $value = "Unsupported type '{$config['type']}'";
        }

        return sprintf($info, $value ?: $this->translate('be.extension_info.none'));
    }

    /**
     * Parse value for select box.
     *
     * @param array $config
     * @param $value
     * @return string
     */
    protected function getSelectBoxValue(array $config, $value): string
    {
        if (!empty($config['foreign_table'])) {
            $value = $this->getValueFromDB($config['foreign_table'], ...GeneralUtility::intExplode(',', $value));
        } elseif (!empty($config['items'])) {
            // Search select item and use it label
            $item = $this->collection($config['items'])->searchByProperty('1', $value)->first();
            if (is_array($item)) {
                $value = $this->translate($item[0]);
            }
        }

        return (string)$value;
    }

    /**
     * Value for checkbox.
     *
     * @param bool $value
     * @return string
     */
    protected function getCheckboxValue(bool $value): string
    {
        return $this->translate('be.extension_info.checkbox_' . ($value ? 'yes' : 'no'));
    }

    /**
     * Get value from DB for group or selectbox.
     *
     * @param string $table
     * @param array $uids
     * @return string
     */
    protected function getValueFromDB(string $table, ...$uids): string
    {
        $titleField = $GLOBALS['TCA'][$table]['ctrl']['label'];
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();

        $rows = $queryBuilder
            ->select($titleField)
            ->from($table)
            ->where($queryBuilder->expr()->in(
                'uid',
                $queryBuilder->createNamedParameter($uids, Connection::PARAM_INT_ARRAY)
            ))
            ->execute()
            ->fetchAll(\PDO::FETCH_COLUMN);

        return is_array($rows) ? implode(', ', $rows) : '';
    }

    /**
     * Read flexform structure and return its fields grouped by sheets.
     *
     * @param string $action
     * @return array
     */
    protected function getFlexformGroupedFields(string $action)
    {
        // Load action data structure
        $dataStructure = $this->loader->defaultWithActionStructure(
            [],
            $this->registry->getSwitchableControllerActionConfiguration($action)
        );

        $fields = [];
        foreach ($dataStructure['sheets'] as $sheetName => $sheet) {
            $fields[$sheetName] = [
                'label' => $sheet['ROOT']['TCEforms']['sheetTitle']
                    ?? 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_be.xlf:flexform.sheet_title',
                'el' => array_map(fn (array $fieldConfig) => $fieldConfig['TCEforms'], $sheet['ROOT']['el']),
            ];
        }

        return $fields;
    }
}
