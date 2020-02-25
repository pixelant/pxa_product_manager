<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Translate;

/**
 * @package Pixelant\PxaProductManager\Translate
 */
trait CanTranslateInBackend
{
    /**
     * Translate by key
     *
     * @param string $key
     * @param array $arguments
     * @return string
     */
    protected function translate(string $key, array $arguments = []): string
    {
        $ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_be.xlf:';
        $label = $GLOBALS['LANG']->sL($ll . $key) ?? '';

        if (!empty($arguments)) {
            $label = vsprintf($label, $arguments);
        }

        return $label;
    }
}
