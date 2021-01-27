<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\ViewHelpers;

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Service\Url\UrlBuilderServiceInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

class LinkViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'a';

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
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();

        $this->registerArgument('product', Product::class, 'Product', true, null);
        $this->registerArgument('category', Category::class, 'Category', false, null);
        $this->registerArgument('returnUrl', 'bool', 'Just return url', false, false);
        $this->registerArgument('target', 'string', 'Link target', false, null);
        $this->registerArgument('absolute', 'string', 'Force absolute link', false, false);
        $this->registerArgument('page', 'int', 'Target page uid', false);
    }

    /**
     * Render link tag.
     *
     * @return string
     */
    public function render()
    {
        $page = (int) ($this->arguments['page']);
        $product = $this->arguments['product'];
        $category = $this->arguments['category'];
        $target = $this->arguments['target'];
        $returnUrl = (bool)$this->arguments['returnUrl'];
        $absolute = (bool)$this->arguments['absolute'];

        $content = (string)$this->renderChildren();

        if ($product !== null) {
            $this->urlBuilder->absolute($absolute);

            $url = $this->urlBuilder->url($product);

            if (!$returnUrl) {
                if (!empty($target)) {
                    $this->tag->addAttribute('target', $target);
                }
                $this->tag->addAttribute('href', $url);
                $this->tag->setContent($content);
                $this->tag->forceClosingTag(true);

                return $this->tag->render();
            }

            return $url;
        }

        return $content;
    }
}
