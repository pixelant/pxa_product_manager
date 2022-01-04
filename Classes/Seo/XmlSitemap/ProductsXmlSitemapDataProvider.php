<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Pixelant\PxaProductManager\Seo\XmlSitemap;

use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Service\Url\UrlBuilderServiceInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\WorkspaceAspect;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\Query\Restriction\WorkspaceRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Seo\XmlSitemap\AbstractXmlSitemapDataProvider;
use TYPO3\CMS\Seo\XmlSitemap\Exception\MissingConfigurationException;

/**
 * ProductXmlSiteDataProvider will provide information for the XML sitemap for products.
 */
final class ProductsXmlSitemapDataProvider extends AbstractXmlSitemapDataProvider
{
    /**
     * @var UrlBuilderServiceInterface
     */
    protected UrlBuilderServiceInterface $urlBuilder;

    /**
     * @var DataMapper
     */
    public DataMapper $dataMapper;

    /**
     * @param ServerRequestInterface $request
     * @param string $key
     * @param array $config
     * @param ContentObjectRenderer|null $cObj
     * @throws MissingConfigurationException
     */
    public function __construct(
        ServerRequestInterface $request,
        string $key,
        array $config = [],
        ContentObjectRenderer $cObj = null
    ) {
        parent::__construct($request, $key, $config, $cObj);

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->urlBuilder = $objectManager->get(UrlBuilderServiceInterface::class);
        $this->urlBuilder->absolute(true);

        $this->dataMapper = $objectManager->get(DataMapper::class);

        $this->generateItems();
    }

    /**
     * @throws MissingConfigurationException
     */
    public function generateItems(): void
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(ProductRepository::TABLE_NAME);

        $queryBuilder->getRestrictions()->add(
            GeneralUtility::makeInstance(WorkspaceRestriction::class, $this->getCurrentWorkspaceAspect()->getId())
        );

        $queryBuilder
            ->select('*')
            ->addSelect()
            ->from(ProductRepository::TABLE_NAME);

        $this->addLanguageExpression($queryBuilder);

        $this->addPidExpression($queryBuilder);

        $this->addProductTypeExpression($queryBuilder);

        $this->addAdditionalWhereExpression($queryBuilder);

        $sortField = $this->config['sortField'] ?? 'sorting';

        $rows = $queryBuilder->orderBy($sortField)
            ->execute()
            ->fetchAllAssociative();

        $lastModifiedField = $this->config['lastModifiedField'] ?? 'tstamp';

        $this->items = array_map(
            function (array $row) use ($lastModifiedField): array {
                return [
                    'data' => $row,
                    'lastMod' => (int)$row[$lastModifiedField],
                    'priority' => 0.5,
                ];
            },
            $rows
        );
    }

    /**
     * @param array $data
     * @return array
     */
    protected function defineUrl(array $data): array
    {
        /** @var Product $product */
        $product = $this->dataMapper->map(
            \Pixelant\PxaProductManager\Domain\Model\Product::class,
            [$data['data']]
        )[0];

        $data['loc'] = $this->urlBuilder->url($product);

        return $data;
    }

    /**
     * Fetch recursive pids.
     *
     * @param array $pids
     * @param int $recursiveLevel
     * @return array
     */
    protected function fetchRecursivePids(array $pids, int $recursiveLevel): array
    {
        $newList = [];
        foreach ($pids as $pid) {
            $list = $this->cObj->getTreeList($pid, $recursiveLevel);
            if ($list) {
                $newList = array_merge($newList, explode(',', $list));
            }
        }

        return $newList;
    }

    /**
     * Add language expression based on current site language.
     *
     * @param QueryBuilder $queryBuilder
     */
    protected function addLanguageExpression(QueryBuilder $queryBuilder): void
    {
        if (!empty($GLOBALS['TCA'][ProductRepository::TABLE_NAME]['ctrl']['languageField'])) {
            $languageId = GeneralUtility::makeInstance(Context::class)->getAspect('language')->getId();

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(
                    $GLOBALS['TCA'][ProductRepository::TABLE_NAME]['ctrl']['languageField'],
                    [$languageId, -1]
                )
            );
        }
    }

    /**
     * Add pid to querybuilder expression if set.
     *
     * @param QueryBuilder $queryBuilder
     */
    protected function addPidExpression(QueryBuilder $queryBuilder): void
    {
        $pids = !empty($this->config['pid']) ? GeneralUtility::intExplode(',', $this->config['pid']) : [];
        if (!empty($pids)) {
            $recursiveLevel = isset($this->config['recursive']) ? (int)$this->config['recursive'] : 0;
            if ($recursiveLevel) {
                $pids = array_merge($pids, $this->fetchRecursivePids($pids, $recursiveLevel));
            }

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(
                    'pid',
                    $queryBuilder->createNamedParameter(
                        $pids,
                        \TYPO3\CMS\Core\Database\Connection::PARAM_INT_ARRAY
                    )
                )
            );
        }
    }

    /**
     * Add productType to querybuilder expression if set.
     *
     * @param QueryBuilder $queryBuilder
     */
    protected function addProductTypeExpression(QueryBuilder $queryBuilder): void
    {
        if (!empty($this->config['excludedProductTypes'])) {
            $excludedProductTypes = GeneralUtility::trimExplode(',', $this->config['excludedProductTypes'], true);
            if (!empty($excludedProductTypes)) {
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->notIn(
                        'product_type',
                        $queryBuilder->createNamedParameter(
                            $excludedProductTypes,
                            \TYPO3\CMS\Core\Database\Connection::PARAM_INT_ARRAY
                        )
                    )
                );
            }
        }
    }

    /**
     * Add additionalWhere to querybuilder expression if set.
     *
     * @param QueryBuilder $queryBuilder
     */
    protected function addAdditionalWhereExpression(QueryBuilder $queryBuilder): void
    {
        if (!empty($this->config['additionalWhere'])) {
            $queryBuilder->andWhere(
                QueryHelper::stripLogicalOperatorPrefix($this->config['additionalWhere'])
            );
        }
    }

    /**
     * @return int
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    protected function getLanguageId(): int
    {
        $context = GeneralUtility::makeInstance(Context::class);

        return (int)$context->getPropertyFromAspect('language', 'id');
    }

    /**
     * @return WorkspaceAspect
     */
    protected function getCurrentWorkspaceAspect(): WorkspaceAspect
    {
        return GeneralUtility::makeInstance(Context::class)->getAspect('workspace');
    }
}
