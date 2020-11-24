<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Service\Url\UrlBuilderService;
use Pixelant\PxaProductManager\Service\Url\UrlBuilderServiceInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Create breadcrumbs array for UserFunc.
 */
class Breadcrumbs
{
    use CanCreateCollection;

    /**
     * @var ServerRequestInterface
     */
    protected ServerRequestInterface $request;

    /**
     * @var ProductRepository
     */
    protected ProductRepository $productRepository;

    /**
     * @var UrlBuilderServiceInterface
     */
    protected UrlBuilderServiceInterface $urlBuilderService;

    /**
     * Initialize repositories.
     *
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        ProductRepository $productRepository = null,
        CategoryRepository $categoryRepository = null
    ) {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->request = $GLOBALS['TYPO3_REQUEST'];

        $this->productRepository = $productRepository ?? $objectManager->get(ProductRepository::class);
    }

    /**
     * @param UrlBuilderServiceInterface $urlBuilderServiceInterface
     */
    public function injectUrlBuilderServiceInterface(UrlBuilderServiceInterface $urlBuilderServiceInterface): void
    {
        $this->urlBuilderService = $urlBuilderServiceInterface;
    }

    /**
     * @var array
     */
    protected array $items = [];

    /**
     * @var string
     */
    protected string $namespace = '';

    /**
     * Build array of breadcrumbs for TypoScript user function.
     *
     * @return array
     */
    public function build(): array
    {
        if ($this->hasBreadcrumbs()) {
            $this->addProduct();
        }

        return array_reverse($this->items);
    }

    /**
     * Add product breadcrumbs if exist in arguments.
     */
    protected function addProduct(): void
    {
        $arguments = $this->getArguments();
        if (!isset($arguments['product'])) {
            return;
        }

        $uid = (int)$arguments['product'];

        /** @var Product $product */
        $product = $this->productRepository->findByUid($uid);
        if ($product) {
            $this->urlBuilderService->absolute(true);

            $this->items[] = [
                'title' => $product->getNavigationTitle(),
                'sys_language_uid' => $product->_getProperty('_languageUid'),
                '_OVERRIDE_HREF' => $this->urlBuilderService->url($product),
                'ITEM_STATE' => 'CUR',
            ];
        }
    }

    /**
     * Return arguments of product manager.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        if (!empty($this->namespace)) {
            return $this->request->getQueryParams()[$this->namespace];
        }

        return [];
    }

    /**
     * Check if product manager get parameters exist.
     *
     * @return bool
     */
    protected function hasBreadcrumbs(): bool
    {
        $queryParams = $this->request->getQueryParams();

        foreach (UrlBuilderService::NAMESPACES as $value) {
            if (array_key_exists($value, $queryParams)) {
                $this->namespace = $value;

                return true;
            }
        }

        return false;
    }
}
