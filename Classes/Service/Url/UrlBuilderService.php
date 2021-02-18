<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service\Url;

use Pixelant\PxaProductManager\Domain\Model\Product;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class LinkBuilderService.
 */
class UrlBuilderService implements UrlBuilderServiceInterface
{
    /**
     * Link constants.
     */
    public const CATEGORY_ARGUMENT_START_WITH = 'category_';

    public const NAMESPACES = [
        1 => 'tx_pxaproductmanager_productshow',
        9 => 'tx_pxaproductmanager_productrender',
    ];

    public const NAMESPACE_PARAMS = [
        'tx_pxaproductmanager_productshow' => [
            'controller' => 'ProductShow',
            'action' => 'show',
            'pluginName' => 'ProductShow',
            'extensionName' => 'PxaProductManager',
        ],
        'tx_pxaproductmanager_productrender' => [
            'controller' => 'ProductRender',
            'action' => 'init'
        ],
    ];

    /**
     * @var TypoScriptFrontendController
     */
    protected TypoScriptFrontendController $tsfe;

    /**
     * Flag if should force absolute url.
     *
     * @var bool
     */
    protected bool $absolute = false;

    /**
     * Initialize.
     *
     * @param TypoScriptFrontendController|null $typoScriptFrontendController
     */
    public function __construct(TypoScriptFrontendController $typoScriptFrontendController = null)
    {
        $this->tsfe = $typoScriptFrontendController ?? $GLOBALS['TSFE'];
    }

    /**
     * URL for product.
     *
     * @param Product $product
     * @return string
     */
    public function url(Product $product): string
    {
        $pageUid = 0;
        $params = [];

        if (!empty($product)) {
            $dokType = $product->getFirstSingleviewPage()->getDoktype() ?? 1;
            $namespace = static::NAMESPACES[$dokType];
            $params = static::NAMESPACE_PARAMS[$namespace];
            $params['product'] = $product->getUid();
            $params = GeneralUtility::implodeArrayForUrl(
                $namespace,
                $params
            );
            $pageUid = $product->getFirstSingleviewPage()->getUid() ?? 0;
        }

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
     * Generate link.
     *
     * @param int $pageUid
     * @param int $dokType
     * @param string $additionalParams
     * @return string
     */
    protected function buildUri(int $pageUid, string $additionalParams): string
    {
        $typolink = [
            'parameter' => $pageUid,
            'useCacheHash' => true,
            'additionalParams' => $additionalParams,
            'forceAbsoluteUrl' => $this->absolute,
        ];

        /** @var ContentObjectRenderer $contentObjectRenderer */
        $contentObjectRenderer = GeneralUtility::makeInstance(
            ContentObjectRenderer::class,
            $this->tsfe
        );

        return $contentObjectRenderer->typolink_URL($typolink);
    }
}
