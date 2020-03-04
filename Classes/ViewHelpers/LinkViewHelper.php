<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\ViewHelpers;

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Service\Url\UrlBuilderServiceInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * @package Pixelant\PxaProductManager\ViewHelpers
 */
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
    public function injectUrlBuilderServiceInterface(UrlBuilderServiceInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Register arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();

        $this->registerArgument('page', 'int', 'Target page uid', true);
        $this->registerArgument('product', Product::class, 'Product', false, null);
        $this->registerArgument('category', Category::class, 'Category', false, null);
        $this->registerArgument('excludeCategories', 'bool', 'Exclude categories from path', false, false);
        $this->registerArgument('target', 'string', 'Link target', false, null);
        $this->registerArgument('absolute', 'string', 'Force absolute link', false, false);
    }

    /**
     * Render link tag
     *
     * @return string
     */
    public function render()
    {
        $page = intval($this->arguments['page']);
        $product = $this->arguments['product'];
        $category = $this->arguments['category'];
        $target = $this->arguments['target'];
        $excludeCategories = (bool)$this->arguments['excludeCategories'];
        $absolute = (bool)$this->arguments['absolute'];

        $content = (string)$this->renderChildren();

        if ($page && ($product !== null || $category !== null)) {
            $this->urlBuilder->absolute($absolute);

            $url = $excludeCategories || is_null($category)
                ? $this->urlBuilder->productUrl($page, $product)
                : $this->urlBuilder->url($page, $category, $product);

            if (!empty($target)) {
                $this->tag->addAttribute('target', $target);
            }
            $this->tag->addAttribute('href', $url);
            $this->tag->setContent($content);
            $this->tag->forceClosingTag(true);

            return $this->tag->render();
        }

        return $content;
    }
}
