<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\ViewHelpers;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class MetaTagViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Arguments.
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('type', 'string', 'Type of meta tag', false, 'name');
        $this->registerArgument('content', 'string', 'Content of meta tag', false, null);
        $this->registerArgument('name', 'string', 'Meta tag name', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return void
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): void {
        $type = $arguments['type'];
        $name = $arguments['name'];
        $content = trim(strip_tags($arguments['content'] ?? $renderChildrenClosure()));

        if (!empty($content)) {
            static::getPageRenderer()->setMetaTag(
                $type,
                $name,
                $content,
                [],
                true
            );
        }
    }

    /**
     * @return PageRenderer
     */
    protected static function getPageRenderer(): PageRenderer
    {
        return GeneralUtility::makeInstance(PageRenderer::class);
    }
}
