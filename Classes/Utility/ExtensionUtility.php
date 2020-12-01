<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionUtility extends \TYPO3\CMS\Extbase\Utility\ExtensionUtility
{
    /**
     * Adding allowed controller actions to existing one without overriding.
     *
     * @param string $extensionName
     * @param string $pluginName
     * @param string $controllerClassName
     * @param array $actions
     */
    public static function addControllerAction(string $extensionName, string $pluginName, string $controllerClassName, array $actions): void
    {
        foreach ($actions as $action) {
            if (gettype($action) === 'string') {
                $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions'][$extensionName]['plugins'][$pluginName]['controllers'][$controllerClassName]['actions'][] = $action;
            } else {
                throw new \UnexpectedValueException('Actions value must be type of string, but type of ' . gettype($action) . ' is given');
            }
        }
    }
}
