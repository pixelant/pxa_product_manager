<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Utility;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Pavlo Zaporozkyi <pavlo@pixelant.se>, Pixelant
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Pixelant\PxaProductManager\Controller\NavigationController;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Form\Mvc\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class HelperFunctions
 * @package Pixelant\PxaProductManager\Utility
 */
class MainUtility
{
    /**
     * Extension manager settings
     *
     * @var array
     */
    protected static $extMgrConfiguration;

    /**
     * Plugin settings
     *
     * @var array
     */
    protected static $settings;

    /**
     * Get extension manager settings
     *
     * @return array
     */
    public static function getExtMgrConfiguration(): array
    {
        if (self::$extMgrConfiguration === null) {
            $configuration = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['pxa_product_manager'] ?? '';

            if ((self::$extMgrConfiguration = unserialize($configuration)) === false) {
                self::$extMgrConfiguration = [];
            }
        }

        return self::$extMgrConfiguration;
    }

    /**
     * Check if prices enabled
     *
     * @return bool
     */
    public static function isPricingEnabled(): bool
    {
        static $pricingEnabled;

        if ($pricingEnabled === null) {
            $configuration = self::getExtMgrConfiguration();
            $pricingEnabled = isset($configuration['enablePrices']) && (int)$configuration['enablePrices'] === 1;
        }

        return $pricingEnabled;
    }

    /**
     * Get uid of active category
     *
     * @return int
     */
    public static function getActiveCategoryFromRequest(): int
    {
        $args = GeneralUtility::_GET('tx_pxaproductmanager_pi1');
        static $activeCategoryUid;

        if ($activeCategoryUid === null && is_array($args)) {
            // Find latest category argument
            foreach (array_reverse($args) as $argKey => $argValue) {
                if (StringUtility::beginsWith($argKey, NavigationController::CATEGORY_ARG_START_WITH)) {
                    $activeCategoryUid = (int)$argValue;
                    break;
                }
            }
        }

        return $activeCategoryUid ?? 0;
    }

    /**
     * Remove value from cookie list
     *
     * @param string $name
     * @param int $value
     * @param int $maxValues
     */
    public static function removeValueFromListCookie(string $name, int $value, int $maxValues = 20)
    {
        // Can't be 0
        $maxValues = $maxValues === 0 ? 20 : $maxValues;

        $cookie = array_key_exists($name, $_COOKIE)
            ? GeneralUtility::intExplode(',', $_COOKIE[$name], true)
            : [];

        // If already in array - remove
        if (in_array($value, $cookie)) {
            $keys = array_keys($cookie, $value);
            foreach ($keys as $key) {
                array_splice($cookie, $key, 1);
            }
        }

        // If is over limit
        if (count($cookie) > $maxValues) {
            $cookie = array_splice($cookie, 0, $maxValues);
        }

        setcookie(
            $name,
            implode(',', $cookie),
            0,
            '/'
        );
    }

    /**
     * Add value to cookie list
     *
     * @param string $name
     * @param int $value
     * @param int $maxValues
     */
    public static function addValueToListCookie(string $name, int $value, int $maxValues = 20)
    {
        // Can't be 0
        $maxValues = $maxValues === 0 ? 20 : $maxValues;

        $cookie = array_key_exists($name, $_COOKIE)
            ? GeneralUtility::intExplode(',', $_COOKIE[$name], true)
            : [];

        // If already in array - remove old entry
        if (in_array($value, $cookie)) {
            $keys = array_keys($cookie, $value);
            foreach ($keys as $key) {
                array_splice($cookie, $key, 1);
            }
        }

        // Add at the beginning of the array
        array_unshift($cookie, $value);

        // If is over limit
        if (count($cookie) > $maxValues) {
            $cookie = array_splice($cookie, 0, $maxValues);
        }

        setcookie(
            $name,
            implode(',', $cookie),
            0,
            '/'
        );
    }

    /**
     * Clean cookie value
     * @param string $name
     */
    public static function cleanCookieValue(string $name)
    {
        setcookie($name, '', time() - 3600, '/');
    }

    /**
     * Build parameters for product link
     *
     * @param Product|int|null $product
     * @param Category|null $category
     * @return array
     */
    public static function buildLinksArguments($product = null, Category $category = null): array
    {
        $arguments = [];
        if ($product !== null && !is_object($product)) {
            /** @var ProductRepository $productRepository */
            $productRepository = self::getObjectManager()->get(ProductRepository::class);
            $product = $productRepository->findByUid((int)$product);
        }

        // If no category, try to get it from product
        if ($category === null
            && is_object($product)
            && $product->getCategories()->count() > 0
        ) {
            $category = $product->getFirstCategory();
        }

        if ($category !== null) {
            // Get tree, don't use root category in url
            /**
             * @TODO always remove first category ?
             */
            $categories = array_slice(
                // use descending order
                array_reverse(
                    CategoryUtility::getParentCategories($category)
                ),
                1
            );
            // add current category
            $categories[] = $category;

            $i = 0;
            /** @var Category $category */
            foreach ($categories as $category) {
                $arguments[NavigationController::CATEGORY_ARG_START_WITH . $i++] = $category->getUid();
            }
        }
        // add product
        if ($product !== null) {
            $arguments['product'] = is_object($product) ? $product->getUid() : $product;
        }

        return !empty($arguments) ? ['tx_pxaproductmanager_pi1' => $arguments] : [];
    }

    /**
     * @return TypoScriptFrontendController
     */
    public static function getTSFE(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * @return ObjectManager|object
     */
    public static function getObjectManager(): ObjectManager
    {
        static $objectManager;

        if ($objectManager === null) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        }

        return $objectManager;
    }

    /**
     * @return CacheManager
     */
    public static function getCacheManager(): CacheManager
    {
        static $cacheManager;

        if ($cacheManager === null) {
            /** @var CacheManager $cacheManager */
            $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        }

        return $cacheManager;
    }

    /**
     * @return array
     */
    public static function getTsSettings(): array
    {
        $objectManager = self::getObjectManager();

        $tsSettings = $objectManager->get(ConfigurationManagerInterface::class)->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );

        $tsSettings = $objectManager->get(TypoScriptService::class)
            ->convertTypoScriptArrayToPlainArray($tsSettings);

        return $tsSettings['plugin']['tx_pxaproductmanager']['settings'];
    }
}
