<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service\Url;

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class LinkBuilderService
 * @package Pixelant\PxaProductManager\Service\Link
 */
class UrlBuilderService implements UrlBuilderServiceInterface
{
    /**
     * Link constants
     */
    const CATEGORY_ARGUMENT_START_WITH = 'category_';
    const NAMESPACES = 'tx_pxaproductmanager_pi1';

    /**
     * @var TypoScriptFrontendController
     */
    protected TypoScriptFrontendController $tsfe;

    /**
     * Flag if should force absolute url
     *
     * @var bool
     */
    protected bool $absolute = false;

    /**
     * Initialize
     *
     * @param TypoScriptFrontendController|null $typoScriptFrontendController
     */
    public function __construct(TypoScriptFrontendController $typoScriptFrontendController = null)
    {
        $this->tsfe = $typoScriptFrontendController ?? $GLOBALS['TSFE'];
    }

    /**
     * URL for product and category
     *
     * @param int $pageUid
     * @param Category $category
     * @param Product|null $product
     * @return string
     */
    public function url(int $pageUid, Category $category, Product $product = null): string
    {
        $params = $this->createParams($category, $product);
        return $this->buildUri($pageUid, $params);
    }

    /**
     * URL only with product parameter
     *
     * @param int $pageUid
     * @param Product $product
     * @return string
     */
    public function productUrl(int $pageUid, Product $product): string
    {
        $category = null;
        $params = $this->createParams($category, $product);

        return $this->buildUri($pageUid, $params);
    }

    /**
     * @param bool $absolute
     */
    public function absolute(bool $absolute): void
    {
        $this->absolute = true;
    }

    /**
     * Generate parameters for URL
     *
     * @param Category|null $category
     * @param Product|null $product
     * @return array
     */
    protected function createParams(?Category $category, ?Product $product): array
    {
        $params = [
            'controller' => 'Product',
            'action' => 'list',
        ];
        if ($category !== null) {
            $params += $this->getCategoriesArguments($category);
        }
        if ($product !== null) {
            $params['product'] = $product->getUid();
            $params['action'] = 'show';
        }

        return $params;
    }

    /**
     * Generate link
     *
     * @param int $pageUid
     * @param array $params
     * @return string
     */
    protected function buildUri(int $pageUid, array $params): string
    {
        $parameters = GeneralUtility::implodeArrayForUrl(
            static::NAMESPACES,
            $params
        );

        $typolink = [
            'parameter' => $pageUid,
            'useCacheHash' => true,
            'additionalParams' => $parameters,
            'forceAbsoluteUrl' => $this->absolute
        ];

        /** @var ContentObjectRenderer $contentObjectRenderer */
        $contentObjectRenderer = GeneralUtility::makeInstance(
            ContentObjectRenderer::class,
            $this->tsfe
        );

        return $contentObjectRenderer->typolink_URL($typolink);
    }

    /**
     * Get category tree arguments
     *
     * @param Category $category
     * @return array
     */
    protected function getCategoriesArguments(Category $category): array
    {
        $arguments = [];
        $i = 0;
        $treeLine = $category->getNavigationRootLine();
        // Last category doesn't have prefix
        $lastCategory = array_pop($treeLine);

        foreach ($treeLine as $categoryItem) {
            $arguments[static::CATEGORY_ARGUMENT_START_WITH . $i++] = $categoryItem->getUid();
        }
        $arguments['category'] = $lastCategory->getUid();

        return $arguments;
    }
}
