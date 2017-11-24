<?php

namespace Pixelant\PxaProductManager\Traits;

/**
 * Use if you need to translate in BE
 * @package Pixelant\PxaProductManager\Traits
 */
trait TranslateBeTrait
{
    /**
     * Path to the locallang file
     *
     * @var string
     */
    protected static $LLPATH = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_be.xlf:';

    /**
     * Translate by key
     *
     * @param string $key
     * @param array $arguments
     * @return string
     */
    protected function translate(string $key, array $arguments = []): string
    {
        if (TYPO3_MODE === 'BE') {
            $label = $this->getLanguageService()->sL(self::$LLPATH . $key) ?? '';

            if (!empty($arguments)) {
                $label = vsprintf($label, $arguments);
            }
        }

        return $label ?? '';
    }

    /**
     * Return language service instance
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
