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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

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
     */
    public static function getSettings(int $currentPageId = null): array
    {
        $tsConfig = self::getTSConfig($currentPageId);
        return $tsConfig['settings'] ?: [];
    }
}
