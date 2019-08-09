<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Seo\XmlSitemap;

use Pixelant\PxaProductManager\Service\Link\LinkBuilderService;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Seo\XmlSitemap\AbstractXmlSitemapDataProvider;

/**
 * Class ProductsXmlSitemapDataProvider
 * @package Pixelant\PxaProductManager\Seo\XmlSitemap
 */
class ProductsXmlSitemapDataProvider extends AbstractXmlSitemapDataProvider
{
    /**
     * Products table
     *
     * @var string
     */
    protected $table = 'tx_pxaproductmanager_domain_model_product';

    /**
     * Exclude categories from url
     *
     * @var bool
     */
    protected $excludeCategories = false;

    /**
     * Target url page ID
     *
     * @var int
     */
    protected $pageId = null;

    /**
     * @param ServerRequestInterface $request
     * @param string $key
     * @param array $config
     * @param ContentObjectRenderer|null $cObj
     */
    public function __construct(ServerRequestInterface $request, string $key, array $config = [], ContentObjectRenderer $cObj = null)
    {
        parent::__construct($request, $key, $config, $cObj);

        $this->excludeCategories = boolval($config['url']['excludeCategories'] ?? false);
        $this->pageId = intval($config['url']['pageId'] ?? $GLOBALS['TSFE']->id);

        $this->generateItems();
    }

    /**
     * Generate site map items
     */
    protected function generateItems()
    {
        list($pids, $lastModifiedField, $sortField) = $this->getConfigFields();

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->table);

        $constraints = [];

        // Site language
        $this->addLanguageConstraint($constraints, $queryBuilder);

        // Storage
        $this->addPidsConstraint($pids, $constraints, $queryBuilder);

        // Additional where
        $this->addAdditionalWhereConstraint($constraints);

        // Do query
        $queryBuilder->select('*')
            ->from($this->table);

        if (!empty($constraints)) {
            $queryBuilder->where(
                ...$constraints
            );
        }

        $rows = $queryBuilder->orderBy($sortField)
            ->execute()
            ->fetchAll();

        foreach ($rows as $row) {
            $this->items[] = [
                'data' => $row,
                'lastMod' => (int)$row[$lastModifiedField]
            ];
        }
    }

    /**
     * Build product item URL
     *
     * @param array $data
     * @return array
     */
    protected function defineUrl(array $data): array
    {
        $linkService = $this->getLinkBuilderService();
        $url = $linkService->buildForProduct($this->pageId, $data['data']['uid'], null, $this->excludeCategories, true);

        if (!empty($url)) {
            $data['loc'] = $url;
        }

        return $data;
    }

    /**
     * Additional where
     *
     * @param array $constraints
     */
    protected function addAdditionalWhereConstraint(array &$constraints): void
    {
        if (!empty($this->config['additionalWhere'])) {
            $constraints[] = $this->config['additionalWhere'];
        }
    }

    /**
     * Language field constraint
     *
     * @param array $constraints
     * @param QueryBuilder $queryBuilder
     */
    protected function addLanguageConstraint(array &$constraints, QueryBuilder $queryBuilder): void
    {
        if (!empty($GLOBALS['TCA'][$this->table]['ctrl']['languageField'])) {
            $constraints[] = $queryBuilder->expr()->in(
                $GLOBALS['TCA'][$this->table]['ctrl']['languageField'],
                [
                    -1, // All languages
                    $this->getLanguageId()  // Current language
                ]
            );
        }
    }

    /**
     * Add storage constraint
     *
     * @param array $pids
     * @param array $constraints
     * @param QueryBuilder $queryBuilder
     */
    protected function addPidsConstraint(array $pids, array &$constraints, QueryBuilder $queryBuilder): void
    {
        if (!empty($pids)) {
            $recursiveLevel = intval($this->config['recursive'] ?? 0);
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

            $constraints[] = $queryBuilder->expr()->in('pid', $pids);
        }
    }

    /**
     * Return configuration fields
     *
     * @return array
     */
    protected function getConfigFields(): array
    {
        return [
            GeneralUtility::intExplode(',', $this->config['pid'] ?? '', true),
            $this->config['lastModifiedField'] ?? 'tstamp',
            $this->config['sortField'] ?? 'sorting'
        ];
    }

    /**
     * @return int
     */
    protected function getLanguageId(): int
    {
        $context = GeneralUtility::makeInstance(Context::class);
        return (int)$context->getPropertyFromAspect('language', 'id');
    }

    /**
     * @return LinkBuilderService
     */
    protected function getLinkBuilderService(): LinkBuilderService
    {
        return GeneralUtility::makeInstance(LinkBuilderService::class);
    }
}
