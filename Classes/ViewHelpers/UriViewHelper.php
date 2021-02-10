<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\ViewHelpers;

use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Service\Url\UrlBuilderServiceInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class UriViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var UrlBuilderServiceInterface
     */
    protected UrlBuilderServiceInterface $urlBuilder;

    /**
     * @param UrlBuilderServiceInterface $urlBuilder
     */
    public function injectUrlBuilderServiceInterface(UrlBuilderServiceInterface $urlBuilder): void
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Register arguments.
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('product', Product::class, 'Product', true, null);
        $this->registerArgument('absolute', 'bool', 'If set, an absolute URI is rendered', false, false);
    }

    /**
     * Render link tag.
     *
     * @return string
     */
    public function render()
    {
        $url = '';

        /** @var Product $product */
        $product = $this->arguments['product'];
        /** @var bool $absolute */
        $absolute = $arguments['absolute'] ?? false;

        if ($product !== null) {
            $this->urlBuilder->absolute($absolute);
            $url = $this->urlBuilder->url($product);
        }

        return $url;
    }
}
