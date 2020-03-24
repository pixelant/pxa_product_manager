<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\ViewHelpers;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * @package Pixelant\PxaProductManager\ViewHelpers
 */
class PageRendererViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * View helper arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('includeJSFooterlibs', 'array', 'List of custom JavaScript file to be loaded');
        $this->registerArgument('includeJSFooter', 'array', 'List of custom JavaScript file to be loaded');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed|void
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): void {
        $includeJSFooterlibs = $arguments['includeJSFooterlibs'] ?? [];
        $includeJSFooter = $arguments['includeJSFooter'] ?? [];

        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

        foreach ($includeJSFooterlibs as $footerlib) {
            $pageRenderer->addJsFooterLibrary($footerlib, $footerlib, '', true, false, '', true);
        }
        foreach ($includeJSFooter as $footerlib) {
            $pageRenderer->addJsFooterFile($footerlib, '', true, false, '', true);
        }
    }
}
