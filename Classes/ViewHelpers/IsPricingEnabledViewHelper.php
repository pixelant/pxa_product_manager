<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\ViewHelpers;

use Pixelant\PxaProductManager\Utility\MainUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Class IsPricingEnabledViewHelper
 * @package Pixelant\PxaProductManager\ViewHelpers
 */
class IsPricingEnabledViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Check if prices are enabled
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return bool
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): bool {
        return MainUtility::isPricingEnabled();
    }
}
