<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Navigation;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Navigation\CategoriesNavigationTreeBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

/**
 * Class CategoriesNavigationTreeBuilderTest
 * @package Pixelant\PxaProductManager\Tests\Unit\Navigation
 */
class CategoriesNavigationTreeBuilderFunctionalTest extends FunctionalTestCase
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    protected $testExtensionsToLoad = ['typo3conf/ext/pxa_product_manager'];

    protected function setUp()
    {
        parent::setUp();
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_category.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/tx_pxaproductmanager_domain_model_product.xml');
        $this->categoryRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(CategoryRepository::class);
    }

    /**
     * @test
     */
    public function buildTreeWillBuildTreeDataArrayForNavigationNoExpandAll()
    {
        $rootCategoryUid = 1;
        $activeCategoryUid = 3;

        $expectData = [
            'rootCategory' => $this->categoryRepository->findByUid($rootCategoryUid),
            'subItems' => [
                2 => [
                    'category' => $this->categoryRepository->findByUid(2),
                    'subItems' => [
                        3 => [
                            'category' => $this->categoryRepository->findByUid(3),
                            'subItems' => [
                                4 => [
                                    'category' => $this->categoryRepository->findByUid(4),
                                    'subItems' => [],
                                    'isCurrent' => false,
                                    'isActive' => false,
                                    'level' => 4
                                ]
                            ],
                            'isCurrent' => true,
                            'isActive' => true,
                            'level' => 3
                        ]
                    ],
                    'isCurrent' => false,
                    'isActive' => true,
                    'level' => 2
                ],
                5 => [
                    'category' => $this->categoryRepository->findByUid(5),
                    'subItems' => [],
                    'isCurrent' => false,
                    'isActive' => false,
                    'level' => 2
                ],
            ],
            'level' => 1
        ];

        /** @var CategoriesNavigationTreeBuilder $navigationBuilder */
        $navigationBuilder = GeneralUtility::makeInstance(CategoriesNavigationTreeBuilder::class);
        $result = $navigationBuilder->buildTree($rootCategoryUid, $activeCategoryUid);

        $this->dataIsSimilar($expectData, $result);
    }

    /**
     * @test
     */
    public function buildTreeWillBuildTreeDataArrayForNavigationExpandAll()
    {
        $rootCategoryUid = 1;
        $activeCategoryUid = 3;

        $expectData = [
            'rootCategory' => $this->categoryRepository->findByUid($rootCategoryUid),
            'subItems' => [
                2 => [
                    'category' => $this->categoryRepository->findByUid(2),
                    'subItems' => [
                        3 => [
                            'category' => $this->categoryRepository->findByUid(3),
                            'subItems' => [
                                4 => [
                                    'category' => $this->categoryRepository->findByUid(4),
                                    'subItems' => [
                                        6 => [
                                            'category' => $this->categoryRepository->findByUid(6),
                                            'subItems' => [],
                                            'isCurrent' => false,
                                            'isActive' => false,
                                            'level' => 5
                                        ]
                                    ],
                                    'isCurrent' => false,
                                    'isActive' => false,
                                    'level' => 4
                                ]
                            ],
                            'isCurrent' => true,
                            'isActive' => true,
                            'level' => 3
                        ]
                    ],
                    'isCurrent' => false,
                    'isActive' => true,
                    'level' => 2
                ],
                5 => [
                    'category' => $this->categoryRepository->findByUid(5),
                    'subItems' => [],
                    'isCurrent' => false,
                    'isActive' => false,
                    'level' => 2
                ],
            ],
            'level' => 1
        ];

        /** @var CategoriesNavigationTreeBuilder $navigationBuilder */
        $navigationBuilder = GeneralUtility::makeInstance(CategoriesNavigationTreeBuilder::class);
        $navigationBuilder->setExpandAll(true);

        $result = $navigationBuilder->buildTree($rootCategoryUid, $activeCategoryUid);

        $this->dataIsSimilar($expectData, $result);
    }

    /**
     * @test
     */
    public function buildTreeWithExcludeCategoriesWillExcludeTheseFromMenuData()
    {
        $rootCategoryUid = 1;
        $activeCategoryUid = 3;
        $excludeCategories = [5, 6];

        $expectData = [
            'rootCategory' => $this->categoryRepository->findByUid($rootCategoryUid),
            'subItems' => [
                2 => [
                    'category' => $this->categoryRepository->findByUid(2),
                    'subItems' => [
                        3 => [
                            'category' => $this->categoryRepository->findByUid(3),
                            'subItems' => [
                                4 => [
                                    'category' => $this->categoryRepository->findByUid(4),
                                    'subItems' => [],
                                    'isCurrent' => false,
                                    'isActive' => false,
                                    'level' => 4
                                ]
                            ],
                            'isCurrent' => true,
                            'isActive' => true,
                            'level' => 3
                        ]
                    ],
                    'isCurrent' => false,
                    'isActive' => true,
                    'level' => 2
                ]
            ],
            'level' => 1
        ];

        /** @var CategoriesNavigationTreeBuilder $navigationBuilder */
        $navigationBuilder = GeneralUtility::makeInstance(CategoriesNavigationTreeBuilder::class);
        $navigationBuilder
            ->setExpandAll(true)
            ->setExcludeCategories($excludeCategories);

        $result = $navigationBuilder->buildTree($rootCategoryUid, $activeCategoryUid);

        $this->dataIsSimilar($expectData, $result);
    }

    /**
     * @test
     */
    public function buildTreeWithHiddenCategoriesNoProductsWillExcludeCategoriesWithoutProductsFromMenuData()
    {
        $rootCategoryUid = 100;
        $activeCategoryUid = 101;

        $expectData = [
            'rootCategory' => $this->categoryRepository->findByUid($rootCategoryUid),
            'subItems' => [
                101 => [
                    'category' => $this->categoryRepository->findByUid(101),
                    'subItems' => [
                        102 => [
                            'category' => $this->categoryRepository->findByUid(102),
                            'subItems' => [],
                            'isCurrent' => false,
                            'isActive' => false,
                            'level' => 3
                        ]
                    ],
                    'isCurrent' => true,
                    'isActive' => true,
                    'level' => 2
                ]
            ],
            'level' => 1
        ];

        // Repository with custom settings
        /** @var Typo3QuerySettings $querySettings */
        $querySettings = GeneralUtility::makeInstance(ObjectManager::class)->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);

        /** @var ProductRepository $productRepository */
        $productRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(ProductRepository::class);
        $productRepository->setDefaultQuerySettings($querySettings);

        /** @var CategoriesNavigationTreeBuilder $navigationBuilder */
        $navigationBuilder = GeneralUtility::makeInstance(CategoriesNavigationTreeBuilder::class);
        $navigationBuilder
            ->setExpandAll(true)
            ->setHideCategoriesWithoutProducts(true)
            ->injectProductRepository($productRepository);

        $result = $navigationBuilder->buildTree($rootCategoryUid, $activeCategoryUid);

        $this->dataIsSimilar($expectData, $result);
    }

    /**
     * Check if data is similar
     *
     * @param array $expected
     * @param array $data
     */
    protected function dataIsSimilar(array $expected, array $data)
    {
        $this->assertSame(
            $expected['rootCategory'],
            $data['rootCategory']
        );
        $this->assertCount(
            count($expected['subItems']),
            $data['subItems']
        );

        $this->subItemsDataIsSimilar($expected['subItems'], $data['subItems']);
    }

    /**
     * Check subitems data
     * @param $expectedSubItems
     * @param $subItemsData
     */
    protected function subItemsDataIsSimilar($expectedSubItems, $subItemsData)
    {
        foreach ($expectedSubItems as $uid => $subItem) {
            foreach ($subItem as $subItemFieldName => $subItemFieldValue) {
                if ($subItemFieldName === 'category') {
                    $this->assertSame(
                        $subItemFieldValue,
                        $subItemsData[$uid][$subItemFieldName]
                    );
                } elseif ($subItemFieldName === 'subItems') {
                    $this->assertCount(
                        count($subItemFieldValue),
                        $subItemsData[$uid][$subItemFieldName]
                    );
                    if (count($subItemFieldValue) + count($subItemsData[$uid][$subItemFieldName]) > 0) {
                        $this->subItemsDataIsSimilar($subItemFieldValue, $subItemsData[$uid][$subItemFieldName]);
                    }
                } else {
                    $this->assertTrue(
                        $subItemFieldValue === $subItemsData[$uid][$subItemFieldName],
                        'Field "' . $subItemFieldName . '" is not same'
                    );
                }
            }
        }
    }
}
