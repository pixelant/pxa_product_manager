<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Utility;

class ExtensionUtility extends \TYPO3\CMS\Extbase\Utility\ExtensionUtility
{
    /**
     * Adding allowed controller actions to existing one without overriding.
     *
     * @param string $extensionName
     * @param string $pluginName
     * @param string $controllerClassName
     * @param array $actions
     * @throws \UnexpectedValueException
     */
    public static function addControllerAction(
        string $extensionName,
        string $pluginName,
        string $controllerClassName,
        array $actions
    ): void {
        /** @codingStandardsIgnoreStart */
        $config = &$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions'][$extensionName]['plugins'][$pluginName]['controllers'][$controllerClassName];
        // @codingStandardsIgnoreEnd

        foreach ($actions as $action) {
            if (gettype($action) === 'string') {
                $config['actions'][] = $action;
            } else {
                throw new \UnexpectedValueException(
                    'Actions value must be type of string, but type of ' . gettype($action) . ' is given'
                );
            }
        }

        $classNameArray = explode('\\', $controllerClassName);
        $controller = end($classNameArray);
        $controllerAlias = current(explode('Controller', $controller));

        $config['alias'] = $controllerAlias;
        $config['className'] = $controllerClassName;
    }
}
