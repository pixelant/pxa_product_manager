<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Utility;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Pixelant\PxaProductManager\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\Exception\MissingArrayPathException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use \TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * Class ConfigurationUtility
 * @package Pixelant\PxaProductManager\Utility
 */
class ConfigurationUtility
{
    /**
     * ts config
     *
     * @var array
     */
    protected static $config;

    /**
     * ConfigurationManager
     *
     * @var ConfigurationManager
     */
    protected static $configurationManager;

    /**
     * Extension manager settings
     *
     * @var array
     */
    protected static $extMgrConfiguration;

    /**
     * Get extension typoscript config from both FE and BE
     *
     * Get CONFIGURATION_TYPE_FULL_TYPOSCRIPT for pxa_product_manager in either FE or BE mode.
     * In BE mode it is possible to also set the current pid where ts should be fetched for.
     *
     * @param int $currentPageId Optional current page id when in BE
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public static function getTSConfig(int $currentPageId = null): array
    {
        if (self::$configurationManager === null) {
            self::$configurationManager = MainUtility::getObjectManager()->get(ConfigurationManager::class);
        }

        if (self::$configurationManager->isEnvironmentInFrontendMode()) {
            $configurationKey = 'FE';
        } else {
            $configurationKey = $currentPageId ?? 'BE';
        }

        if (empty(self::$config[$configurationKey])) {
            if ($currentPageId !== null && !self::$configurationManager->isEnvironmentInFrontendMode()) {
                self::$configurationManager->setCurrentPageId($currentPageId);
            }

            $fullRawTyposcript = self::$configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            );
            if (!empty($fullRawTyposcript['plugin.']['tx_pxaproductmanager.'])) {
                self::$config[$configurationKey] = GeneralUtility::removeDotsFromTS(
                    $fullRawTyposcript['plugin.']['tx_pxaproductmanager.']
                );
            } else {
                self::$config[$configurationKey] = [];
            }
        }

        return self::$config[$configurationKey];
    }

    /**
     * @param int|null $currentPageId
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     * @internal
     * @see getSettingsByPath
     */
    public static function getSettings(int $currentPageId = null): array
    {
        $tsConfig = self::getTSConfig($currentPageId);
        return $tsConfig['settings'] ?: [];
    }

    /**
     * Get extension manager settings
     *
     * @return array
     * @internal
     * @see getExtManagerConfigurationByPath
     */
    public static function getExtMgrConfiguration(): array
    {
        if (self::$extMgrConfiguration === null) {
            $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)
                ->get('pxa_product_manager');

            self::$extMgrConfiguration = $extensionConfiguration ?: [];
        }

        return self::$extMgrConfiguration;
    }

    /**
     * Read value from settings by path
     * @param string $path Path separated by "/"
     * @param int|null $currentPageId
     * @return mixed|null
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public static function getSettingsByPath(string $path, int $currentPageId = null)
    {
        return self::readArrayRecursiveByPath(
            self::getSettings($currentPageId),
            $path
        );
    }

    /**
     * Read value from extension manager configuration
     *
     * @param string $path Path separated by "/"
     * @return mixed|null
     */
    public static function getExtManagerConfigurationByPath(string $path)
    {
        return self::readArrayRecursiveByPath(
            self::getExtMgrConfiguration(),
            $path
        );
    }

    /**
     * @return array
     */
    public static function getCheckoutSystems(): array
    {
        $checkoutSystems = [
            'default' => [
                'type' => 'default'
            ]
        ];

        $signalSlotDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
        $signalSlotDispatcher->dispatch(__CLASS__, 'BeforeReturningCheckoutSystems', [&$checkoutSystems]);

        return $checkoutSystems;
    }

    /**
     * Read recursive from array settings
     * @param array $settings
     * @param string $path
     * @return mixed|null
     */
    private static function readArrayRecursiveByPath(array $settings, string $path)
    {
        try {
            $value = ArrayUtility::getValueByPath($settings, $path, '/');
        } catch (MissingArrayPathException $exception) {
            return null;
        } catch (\RuntimeException $exception) {
            return null;
        }

        return $value;
    }
}
