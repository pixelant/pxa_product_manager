<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Seo\XmlSitemap;

use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Service\Url\UrlBuilderServiceInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Seo\XmlSitemap\AbstractXmlSitemapDataProvider;

/**
 * Class ProductsXmlSitemapDataProvider.
 */
class ProductsXmlSitemapDataProvider extends AbstractXmlSitemapDataProvider
{
    /**
     * @var ProductRepository
     */
    protected ProductRepository $repository;

    /**
     * @var UrlBuilderServiceInterface
     */
    protected UrlBuilderServiceInterface $urlBuilder;

    /**
     * Exclude categories from url.
     *
     * @var bool
     */
    protected bool $excludeCategories = false;

    /**
     * Target url page ID.
     *
     * @var int
     */
    protected int $pageId = 0;

    /**
     * @param ServerRequestInterface $request
     * @param string $key
     * @param array $config
     * @param ContentObjectRenderer|null $cObj
     */
    public function __construct(
        ServerRequestInterface $request,
        string $key,
        array $config = [],
        ContentObjectRenderer $cObj = null
    ) {
        parent::__construct($request, $key, $config, $cObj);

        $this->excludeCategories = (bool) ($config['url']['excludeCategories'] ?? false);
        $this->pageId = (int) ($config['url']['pageId'] ?? 0);

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->repository = $objectManager->get(ProductRepository::class);

        $this->urlBuilder = $objectManager->get(UrlBuilderServiceInterface::class);
        $this->urlBuilder->absolute(true);

        $this->generateItems();
    }

    /**
     * Generate site map items.
     */
    protected function generateItems(): void
    {
        $demand = $this->createDemand();

        $limit = 1000;
        $offset = 0;
        do {
            $demand->setLimit($limit);
            $demand->setOffSet($offset);

            $products = $this->repository->findDemanded($demand);

            /** @var Product $product */
            foreach ($products as $product) {
                $this->items[] = [
                    'product' => $product,
                    'lastMod' => $product->getTstamp()->getTimestamp(),
                ];
            }

            // Increase offset
            $offset += $limit;
        } while ($products->count() >= $limit);
    }

    /**
     * Storage pids.
     *
     * @return array
     */
    protected function getStorage(): array
    {
        $pids = GeneralUtility::intExplode(',', $this->config['pid'] ?? [], true);

        if (!empty($pids)) {
            $recursiveLevel = (int)($this->config['recursive'] ?? 0);
            if ($recursiveLevel) {
                $newList = [];
                foreach ($pids as $pid) {
                    $list = $this->cObj->getTreeList($pid, $recursiveLevel);
                    if ($list) {
                        $newList = array_merge($newList, explode(',', $list));
                    }
                }
                $pids = array_merge($pids, $newList);
            }
        }

        return $pids;
    }

    /**
     * Build product item URL.
     *
     * @param array $data
     * @return array
     */
    protected function defineUrl(array $data): array
    {
        /** @var Product $product */
        $product = $data['product'];

        $data['loc'] = $this->urlBuilder->url(
            $this->pageId,
            $product->getFirstCategory(),
            $product
        );

        return $data;
    }

    /**
     * @return ProductDemand
     */
    protected function createDemand(): ProductDemand
    {
        return GeneralUtility::makeInstance(ProductDemand::class)
            ->setStoragePid($this->getStorage())
            ->setOrderBy('tstamp')
            ->setOrderByAllowed('tstamp')
            ->setOrderDirection('desc');
    }
}
