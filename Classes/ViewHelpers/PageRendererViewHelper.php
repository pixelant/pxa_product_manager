<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\ViewHelpers;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class PageRendererViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * View helper arguments.
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('cssLibs', 'array', 'Array of css libs');
        $this->registerArgument('includeJSFooterlibs', 'array', 'Array of custom JavaScript file to be loaded');
        $this->registerArgument('includeJSFooter', 'array', 'Array of custom JavaScript file to be loaded');
        $this->registerArgument('inlineLanguageLabelFiles', 'array', 'Array of labels files');
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
        $cssLibs = $arguments['cssLibs'] ?? [];
        $inlineLanguageLabelFiles = $arguments['inlineLanguageLabelFiles'] ?? [];

        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

        foreach ($includeJSFooterlibs as $footerlib) {
            $pageRenderer->addJsFooterLibrary($footerlib, $footerlib, '', true, false, '', true);
        }
        foreach ($includeJSFooter as $footerlib) {
            $pageRenderer->addJsFooterFile($footerlib, '', true, false, '', true);
        }
        foreach ($cssLibs as $cssLib) {
            $pageRenderer->addCssLibrary($cssLib, 'stylesheet', 'all', '', true, false, '', true);
        }
        foreach ($inlineLanguageLabelFiles as $inlineLanguageLabelFile) {
            $pageRenderer->addInlineLanguageLabelFile($inlineLanguageLabelFile, 'js.');
        }
    }
}
