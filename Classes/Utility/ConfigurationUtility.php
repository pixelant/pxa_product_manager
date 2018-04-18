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

use Pixelant\PxaProductManager\Controller\NavigationController;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use \TYPO3\CMS\Extbase\Service\EnvironmentService;

/**
 * Class ConfigurationUtility
 * @package Pixelant\PxaProductManager\Utility
 */
class ConfigurationUtility
{
    /**
     * Settings
     *
     * @var array
     */
    protected static $settings;

    /**
     * ConfigurationManager
     *
     * @var \Pixelant\PxaProductManager\Configuration\ConfigurationManager
     */
    protected static $configurationManager;

    /**
     * Get extension typoscript settings from both FE and BE
     *
     * Get CONFIGURATION_TYPE_FULL_TYPOSCRIPT for pxa_product_manager in either FE or BE mode.
     * In BE mode it is possible to also set the current pid where ts should be fetched for.
     *
     * @param int $currentPageId Optional current page id when in BE
     *
     * @return array
     */
    public static function getSettings(int $currentPageId = null): array
    {
        if (self::$configurationManager === null) {
            self::$configurationManager = GeneralUtility::makeInstance(ObjectManager::class)
                ->get(ConfigurationManager::class);
        }

        if (self::$configurationManager->isEnvironmentInFrontendMode()) {
            $configurationKey = 'FE';
        } else {
            $configurationKey = $currentPageId !== null ?  $currentPageId : 'BE';
        }

        if (empty(self::$settings[$configurationKey])) {
            if ($currentPageId !== null && !self::$configurationManager->isEnvironmentInFrontendMode()) {
                self::$configurationManager->setCurrentPageId($currentPageId);
            }

            $fullRawTyposcript = self::$configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            );
            if (!empty($fullRawTyposcript['plugin.']['tx_pxaproductmanager.']['settings.'])) {
                self::$settings[$configurationKey] = GeneralUtility::removeDotsFromTS(
                    $fullRawTyposcript['plugin.']['tx_pxaproductmanager.']['settings.']
                );
            } else {
                self::$settings[$configurationKey] = [];
            }
        }
        return self::$settings[$configurationKey];
    }
}
