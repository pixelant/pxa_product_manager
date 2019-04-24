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

use Pixelant\PxaProductManager\Service\Link\LinkBuilderService;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Charset\CharsetConverter;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class HelperFunctions
 * @package Pixelant\PxaProductManager\Utility
 */
class MainUtility
{
    /**
     * Array of normalize characters
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    protected static $normalizeChars = [
        'Š' => 'S', 'š' => 's', 'Ð' => 'Dj', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
        'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
        'Ï' => 'I', 'Ñ' => 'N', 'Ń' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
        'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
        'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
        'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ń' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
        'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f',
        'ă' => 'a', 'î' => 'i', 'â' => 'a', 'ș' => 's', 'ț' => 't', 'Ă' => 'A', 'Î' => 'I', 'Â' => 'A', 'Ș' => 'S', 'Ț' => 'T',
    ];// @codingStandardsIgnoreEnd

    /**
     * Check if prices enabled
     *
     * @return bool
     */
    public static function isPricingEnabled(): bool
    {
        static $pricingEnabled;

        if ($pricingEnabled === null) {
            $pricingEnabled = (int)ConfigurationUtility::getSettingsByPath('showPricesInTemplate') === 1;
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
                if (StringUtility::beginsWith($argKey, LinkBuilderService::CATEGORY_ARGUMENT_START_WITH)) {
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
            '/',
            GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY')
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
            '/',
            GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY')
        );
    }

    /**
     * Clean cookie value
     * @param string $name
     */
    public static function cleanCookieValue(string $name)
    {
        setcookie($name, '', time() - 3600, '/', GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY'));
    }

    /**
     * Parse fluid string
     *
     * @param string $string
     * @param array $variables
     * @return string
     */
    public static function parseFluidString(string $string, array $variables): string
    {
        if (empty($string)) {
            return '';
        }

        /** @var StandaloneView $standAloneView */
        $standAloneView = GeneralUtility::makeInstance(StandaloneView::class);

        $standAloneView->setTemplateSource($string);
        $standAloneView->assignMultiple($variables);

        return $standAloneView->render();
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
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @return CacheManager
     */
    public static function getCacheManager(): CacheManager
    {
        return GeneralUtility::makeInstance(CacheManager::class);
    }

    /**
     * @param string $value
     * @return string
     */
    public static function snakeCasePhraseToWords(string $value): string
    {
        return ucfirst(str_replace('_', ' ', $value));
    }

    /**
     * Normalize string removing special characters
     *
     * @param string $string
     * @return  string Processed string
     */
    public static function normalizeString(string $string): string
    {
        $chConverter = GeneralUtility::makeInstance(CharsetConverter::class);

        return $chConverter->specCharsToASCII(
            'utf-8',
            strtr($string, self::$normalizeChars)
        );
    }

    /**
     * Check if FE user is logged in
     *
     * @return bool
     */
    public static function isFrontendLogin(): bool
    {
        $context = GeneralUtility::makeInstance(Context::class);
        return $context->getPropertyFromAspect('frontend.user', 'isLoggedIn', false);
    }

    /**
     * Check if BE user is logged in
     *
     * @return bool
     */
    public static function isBackendLogin(): bool
    {
        $context = GeneralUtility::makeInstance(Context::class);
        return $context->getPropertyFromAspect('backend.user', 'isLoggedIn', false);
    }
}
