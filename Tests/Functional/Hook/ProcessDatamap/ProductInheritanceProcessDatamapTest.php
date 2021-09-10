<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Hook\ProcessDatamap;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\Attributes\ConfigurationProvider\ConfigurationProviderFactory;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Model\ProductType;
use Pixelant\PxaProductManager\Domain\Repository\AttributeRepository;
use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductTypeRepository;
use Pixelant\PxaProductManager\Utility\AttributeUtility;
use Pixelant\PxaProductManager\Utility\DataInheritanceUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\WorkspaceAspect;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class ProductInheritanceProcessDatamapTest extends FunctionalTestCase
{
    public const VALUE_BACKENDUSERID = 1;

    public const PRODUCT_WITH_PT_COMBINED = 1;

    public const PRODUCT_WITH_PT_NO_ATTRIBUTES = 2;

    public const PRODUCT_WITH_PT_ONLY_ATTRIBUTES = 3;

    public const PRODUCT_WITH_PT_ALL_ATTRIBUTE_TYPES = 4;

    public const SINGLE_VIEW_PAGE = 'pages_85';

    /**
     * @var DataHandler
     */
    protected $subject;

    /**
     * @var AttributeValueRepository
     */
    protected $attributeValueRepository;

    /**
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var ProductTypeRepository
     */
    protected $productTypeRepository;

    /**
     * Array to store all attributes so we don't need to fetch them when we only have uid.
     * All attributes are loaded when.
     * @var array
     */
    protected $attributes = [];

    protected $coreExtensionsToLoad = [
        'seo',
    ];

    protected $testExtensionsToLoad = [
        'typo3conf/ext/pxa_product_manager',
        'typo3conf/ext/demander',
    ];

    /**
     * Default Site Configuration.
     * @var array
     */
    protected $siteLanguageConfiguration = [
        1 => [
            'title' => 'Dansk',
            'enabled' => true,
            'languageId' => 1,
            'base' => '/dk/',
            'typo3Language' => 'dk',
            'locale' => 'da_DK.UTF-8',
            'iso-639-1' => 'da',
            'flag' => 'dk',
            'fallbackType' => 'fallback',
            'fallbacks' => '0',
        ],
        2 => [
            'title' => 'Deutsch',
            'enabled' => true,
            'languageId' => 2,
            'base' => '/de/',
            'typo3Language' => 'de',
            'locale' => 'de_DE.UTF-8',
            'iso-639-1' => 'de',
            'flag' => 'de',
            'fallbackType' => 'fallback',
            'fallbacks' => '1,0',
        ],
    ];

    /**
     * @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected $backendUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->backendUser = $this->setUpBackendUserFromFixture(self::VALUE_BACKENDUSERID);

        $this->setWorkspaceId(0);

        Bootstrap::initializeLanguageObject();

        $this->subject = GeneralUtility::makeInstance(ObjectManager::class)->get(DataHandler::class);

        $this->attributeRepository
            = GeneralUtility::makeInstance(ObjectManager::class)->get(AttributeRepository::class);

        $this->attributeValueRepository
            = GeneralUtility::makeInstance(ObjectManager::class)->get(AttributeValueRepository::class);

        $this->productRepository
            = GeneralUtility::makeInstance(ObjectManager::class)->get(ProductRepository::class);

        $this->productTypeRepository
            = GeneralUtility::makeInstance(ObjectManager::class)->get(ProductTypeRepository::class);
    }

    /**
     * @param int $workspaceId
     */
    protected function setWorkspaceId(int $workspaceId): void
    {
        $this->backendUser->workspace = $workspaceId;
        GeneralUtility::makeInstance(Context::class)->setAspect('workspace', new WorkspaceAspect($workspaceId));
    }

    /**
     * Generate tx_pxaproductmanager_domain_model_attributevalue TCA types.
     * Needs to be done after attributes dataset are imported.
     *
     * @return void
     */
    protected function createAttributeValueTypeConfiguration(): void
    {
        // Only load all attributes if they aren't already loaded.
        if (empty($this->attributes)) {
            $attributes = \Pixelant\PxaProductManager\Utility\AttributeUtility::findAllAttributes();

            foreach ($attributes as $attribute) {
                if ($attribute['uid'] === 0) {
                    continue;
                }

                $this->attributes[$attribute['uid']] = $this->attributeRepository->findByUid($attribute['uid']);

                // phpcs:ignore
                $GLOBALS['TCA']['tx_pxaproductmanager_domain_model_attributevalue']['types'][(string)$attribute['uid']] = [
                    'showitem' => 'value',
                    'columnsOverrides' => [
                        'value' => ConfigurationProviderFactory::create((int)$attribute['uid'])->get(),
                    ],
                ];
            }
        }
    }

    /**
     * ImportDataSets.
     *
     * @return void
     */
    protected function importDataSets(): void
    {
        $this->importDataSet(__DIR__ . '/../../../Fixtures/pages.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/sys_category_record_mm.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/sys_category.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/sys_file_reference.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/sys_file_storage.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/sys_file.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_attributeset_record_mm.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_domain_model_attribute.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_domain_model_attributeset.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_domain_model_attributevalue.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_domain_model_link.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_domain_model_option.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_domain_model_product.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_domain_model_producttype.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_product_pages_mm.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_product_product_mm.xml');

        $this->createAttributeValueTypeConfiguration();

        // ADD ASSERTION
        /*
        SELECT attribute, count(*)
        FROM tx_pxaproductmanager_domain_model_attributevalue tpdma
        WHERE product = 1 GROUP BY attribute;
        */
    }

    /**
     * @test
     */
    public function saveNewProductWithParentAndNoAttributesIheritedInheritsCorrectFields(): void
    {
        $this->importDataSets();

        // Fretch parent product.
        /** @var Product $parentProduct */
        $parentProduct = $this->productRepository->findByUid(self::PRODUCT_WITH_PT_NO_ATTRIBUTES);

        /** @var Product $childProduct */
        $childProduct = $this->createAndFetchNewProduct(
            [
                'name' => 'Child Product - Child to ' . $parentProduct->getName(),
                'pid' => $parentProduct->getPid(),
                'parent' => ProductRepository::TABLE_NAME . '_' . $parentProduct->getUid(),
                'product_type' => $parentProduct->getProductType()->getUid(),
                'singleview_page' => self::SINGLE_VIEW_PAGE,
                'tax_rate' => 10,
            ],
            [],
            []
        );

        self::assertInstanceOf(Product::class, $childProduct);

        $this->assertProductHasCorrectAttributeValues($parentProduct);

        $this->assertChildInheritedFieldsAreEqualToParent($parentProduct, $childProduct);
    }

    /**
     * @test
     */
    public function saveNewProductWithParentAndMixedInheritedFieldsInheritsCorrectFields(): void
    {
        /*
         - One inherited image is wrong on create.
         - The order of inherited images is wrong on create.
         - One inherited atrribute is missing on create.
         - One inherited atrribute has wrong values on create.
        */
        $this->importDataSets();

        // Fretch parent product.
        /** @var Product $parentProduct */
        $parentProduct = $this->productRepository->findByUid(self::PRODUCT_WITH_PT_COMBINED);

        /** @var Product $childProduct */
        $childProduct = $this->createAndFetchNewProduct(
            [
                'name' => 'Child Product - Child to ' . $parentProduct->getName(),
                'pid' => $parentProduct->getPid(),
                'parent' => ProductRepository::TABLE_NAME . '_' . $parentProduct->getUid(),
                'product_type' => $parentProduct->getProductType()->getUid(),
                'singleview_page' => self::SINGLE_VIEW_PAGE,
            ],
            [
                0 => [
                    'value' => '5',
                    'attribute' => 5,
                ],
                1 => [
                    'value' => '2',
                    'attribute' => 2,
                ],
                2 => [
                    // Wrong attributes by purpose.
                    'value' => '11,12,13,15',
                    'attribute' => 3,
                ],
                3 => [
                    'value' => '',
                    'attribute' => 4,
                ],
            ],
            [
                0 => 31,
                1 => 30,
            ],
        );

        self::assertInstanceOf(Product::class, $childProduct);

        $this->assertProductHasCorrectAttributeValues($parentProduct);

        $this->assertChildInheritedFieldsAreEqualToParent($parentProduct, $childProduct);
    }

    /**
     * @test
     */
    public function saveNewProductWithParentAndAllAttributeTypesIheritedInheritsCorrectFields(): void
    {
        $this->importDataSets();

        // Fretch parent product.
        /** @var Product $parentProduct */
        $parentProduct = $this->productRepository->findByUid(self::PRODUCT_WITH_PT_ALL_ATTRIBUTE_TYPES);

        /** @var Product $childProduct */
        $childProduct = $this->createAndFetchNewProduct(
            [
                'name' => 'Child Product - Child to ' . $parentProduct->getName(),
                'pid' => $parentProduct->getPid(),
                'parent' => ProductRepository::TABLE_NAME . '_' . $parentProduct->getUid(),
                'product_type' => $parentProduct->getProductType()->getUid(),
                'singleview_page' => self::SINGLE_VIEW_PAGE,
                'tax_rate' => 20,
            ],
            [],
            []
        );

        self::assertInstanceOf(Product::class, $childProduct);

        $this->assertChildInheritedFieldsAreEqualToParent($parentProduct, $childProduct);
    }

    /**
     * @test
     */
    public function copyChildProductInheritsCorrectFieldsFromParent(): void
    {
        // NOTE, copy a child product produces duplicate attribute values.
        // Could be that the inheritance doesn't check if child product already have an attribute,
        // and it only checks tx_pxaproductmanager_relation_inheritance_index for mappings.
        // How do we validate data in tx_pxaproductmanager_relation_inheritance_index....
        // NOTE, Also seems like also inline fields e.g. images are duplicated during copy.
        // NOTE, Also need to test to save a parent and check child product attribute values.
        $this->importDataSets();

        // Fretch parent product.
        /** @var Product $parentProduct */
        $parentProduct = $this->productRepository->findByUid(self::PRODUCT_WITH_PT_COMBINED);

        /** @var Product $childProduct */
        $childProduct = $this->createAndFetchNewProduct(
            [
                'name' => 'Child Product - Child to ' . $parentProduct->getName(),
                'pid' => $parentProduct->getPid(),
                'parent' => ProductRepository::TABLE_NAME . '_' . $parentProduct->getUid(),
                'product_type' => $parentProduct->getProductType()->getUid(),
                'singleview_page' => self::SINGLE_VIEW_PAGE,
                'tax_rate' => 25,
            ],
            [
                0 => [
                    'value' => '1',
                    'attribute' => 1,
                ],
                1 => [
                    'value' => '2',
                    'attribute' => 2,
                ],
                2 => [
                    'value' => '11,13,15',
                    'attribute' => 3,
                ],
                3 => [
                    'value' => '',
                    'attribute' => 4,
                ],
            ],
            [
                0 => 30,
            ]
        );

        self::assertInstanceOf(Product::class, $childProduct);

        $this->assertProductHasCorrectAttributeValues($parentProduct);

        $this->assertChildInheritedFieldsAreEqualToParent($parentProduct, $childProduct);

        // Create a copy of the new child product
        $copiedChildProduct = $this->createAndFetchCopyOfProduct($childProduct);

        self::assertInstanceOf(Product::class, $copiedChildProduct);

        $this->assertChildInheritedFieldsAreEqualToParent($parentProduct, $copiedChildProduct);

        // product -> hasAttributeValueForAttribute()
        // product -> getAttributesValues
        // inheritance table check tx_pxaproductmanager_relation_inheritance_index
    }

    /**
     * @test
     */
    public function localizeChildProductInheritsCorrectFieldsFromParent(): void
    {
        // NOTE, copy a child product produces duplicate attribute values.
        // Could be that the inheritance doesn't check if child product already have an attribute,
        // and it only checks tx_pxaproductmanager_relation_inheritance_index for mappings.
        // How do we validate data in tx_pxaproductmanager_relation_inheritance_index....
        // NOTE, Also seems like also inline fields e.g. images are duplicated during copy.
        // NOTE, Also need to test to save a parent and check child product attribute values.
        $this->importDataSets();

        // Fretch parent product.
        /** @var Product $parentProduct */
        $parentProduct = $this->productRepository->findByUid(self::PRODUCT_WITH_PT_COMBINED);

        /** @var Product $childProduct */
        $childProduct = $this->createAndFetchNewProduct(
            [
                'name' => 'Child Product - Child to ' . $parentProduct->getName(),
                'pid' => $parentProduct->getPid(),
                'parent' => ProductRepository::TABLE_NAME . '_' . $parentProduct->getUid(),
                'product_type' => $parentProduct->getProductType()->getUid(),
                'singleview_page' => self::SINGLE_VIEW_PAGE,
                'tax_rate' => 25,
            ],
            [
                0 => [
                    'value' => '1',
                    'attribute' => 1,
                ],
                1 => [
                    'value' => '2',
                    'attribute' => 2,
                ],
                2 => [
                    'value' => '11,13,15',
                    'attribute' => 3,
                ],
                3 => [
                    'value' => '',
                    'attribute' => 4,
                ],
            ],
            [
                0 => 30,
            ]
        );

        self::assertInstanceOf(Product::class, $childProduct);

        $this->assertProductHasCorrectAttributeValues($parentProduct);

        $this->assertChildInheritedFieldsAreEqualToParent($parentProduct, $childProduct);

        // Create a copy of the new child product
        $copiedChildProduct = $this->createAndFetchCopyOfProduct($childProduct);

        self::assertInstanceOf(Product::class, $copiedChildProduct);

        $this->assertChildInheritedFieldsAreEqualToParent($parentProduct, $copiedChildProduct);

        // product -> hasAttributeValueForAttribute()
        // product -> getAttributesValues
        // inheritance table check tx_pxaproductmanager_relation_inheritance_index
    }

    /**
     * Create new product using DataHandler.
     *
     * @param array $row Product data
     * @return int
     */
    protected function createNewProduct(array $row, array $attributeValues = [], array $images = []): int
    {
        $newRecordId = StringUtility::getUniqueId('NEW');
        $data[ProductRepository::TABLE_NAME][$newRecordId] = $row;

        if (!empty($attributeValues)) {
            foreach ($attributeValues as $index => $value) {
                $newAttributeRecordId[$index] = StringUtility::getUniqueId('NEW');
                $data[AttributeValueRepository::TABLE_NAME][$newAttributeRecordId[$index]] = $value;
                $data[AttributeValueRepository::TABLE_NAME][$newAttributeRecordId[$index]]['pid'] = 0;
            }
            $data[ProductRepository::TABLE_NAME][$newRecordId]['attributes_values']
                = implode(',', $newAttributeRecordId);
        }

        if (!empty($images)) {
            foreach ($images as $index => $value) {
                $newImageRecordId[$index] = StringUtility::getUniqueId('NEW');
                $data['sys_file_reference'][$newImageRecordId[$index]] = [
                    'uid_local' => 'sys_file_' . $value,
                    'hidden' => 0,
                    'pid' => 0,
                ];
            }
            $data[ProductRepository::TABLE_NAME][$newRecordId]['images']
                = implode(',', $newImageRecordId);
        }

        /** @var DataHandler $dataHandler */
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start($data, []);
        $dataHandler->process_datamap();
        $dataHandler->process_cmdmap();

        return $dataHandler->substNEWwithIDs[$newRecordId];
    }

    /**
     * Creates a new product using DataHandler and fetches it.
     *
     * @param array $row Product data
     * @return Product
     */
    protected function createAndFetchNewProduct(array $row, array $attributeValues = [], array $images = []): Product
    {
        return $this->productRepository->findByUid($this->createNewProduct($row, $attributeValues, $images));
    }

    /**
     * Copy a product using DataHandler.
     *
     * @param Product $product Product
     * @return int
     */
    protected function createCopyOfProduct(Product $product): int
    {
        $cmd[ProductRepository::TABLE_NAME][$product->getUid()]['copy'] = $product->getPid();

        /** @var DataHandler $dataHandler */
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start([], $cmd);
        $dataHandler->process_datamap();
        $dataHandler->process_cmdmap();

        return $dataHandler->copyMappingArray[ProductRepository::TABLE_NAME][$product->getUid()];
    }

    /**
     * Copy a product using DataHandler and fetch it.
     *
     * @param Product $product Product
     * @return Product
     */
    protected function createAndFetchCopyOfProduct(Product $product): Product
    {
        return $this->productRepository->findByUid($this->createCopyOfProduct($product));
    }

    /**
     * Localize a product using DataHandler.
     *
     * @param Product $product Product
     * @return int
     */
    protected function createLocalizationOfProduct(Product $product): int
    {
        $cmd[ProductRepository::TABLE_NAME][$product->getUid()]['localize'] = 2;

        /** @var DataHandler $dataHandler */
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start([], $cmd);
        $dataHandler->process_datamap();
        $dataHandler->process_cmdmap();

        return $dataHandler->copyMappingArray[ProductRepository::TABLE_NAME][$product->getUid()];
    }

    /**
     * Copy a product using DataHandler and fetch it.
     *
     * @param Product $product Product
     * @return Product
     */
    protected function createAndFetchLocalizedProduct(Product $product): Product
    {
        return $this->productRepository->findByUid($this->createLocalizationOfProduct($product));
    }

    /**
     * Asserts inherited fields between parent and child product.
     *
     * @param Product $parentProduct
     * @param Product $childProduct
     * @return void
     * @throws \Exception
     */
    protected function assertChildInheritedFieldsAreEqualToParent(
        Product $parentProduct,
        Product $childProduct
    ): void {
        /** @var ProductType $productType */
        $productType = $this->productTypeRepository->findByUid($parentProduct->getProductType()->getUid());

        $inheritedFields = DataInheritanceUtility::getInheritedFieldsForProductType($productType->getUid()) ?? [];

        foreach ($inheritedFields as $inheritedField) {
            if (strpos($inheritedField, 'attribute.') !== false) {
                $uid = (string)array_pop(explode('.', (string)$inheritedField));
                $attributeIdentifier = $this->attributes[$uid]->getIdentifier();

                self::assertInstanceOf(
                    \Pixelant\PxaProductManager\Domain\Model\AttributeValue::class,
                    $childProduct->getAttributeValue()[$attributeIdentifier],
                );

                self::assertEquals(
                    $parentProduct->getAttributeValue()[$attributeIdentifier]->getValue(),
                    $childProduct->getAttributeValue()[$attributeIdentifier]->getValue()
                );
            } else {
                $methodName = 'get' . GeneralUtility::underscoredToUpperCamelCase($inheritedField);

                if (method_exists($parentProduct, $methodName)) {
                    $parentData = $parentProduct->{$methodName}();
                    $childData = $childProduct->{$methodName}();

                    if ($parentData instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage) {
                        self::assertEquals(
                            get_class($parentData),
                            get_class($childData)
                        );
                        $parentValues = DataInheritanceUtility::getObjectStorageIdList($parentData);
                        $childValues = DataInheritanceUtility::getObjectStorageIdList($childData);

                        self::assertEquals(
                            $parentValues,
                            $childValues
                        );
                    } else {
                        if (is_object($parentData)) {
                            self::assertEquals(
                                $parentData->getUid(),
                                $childData->getUid()
                            );
                        } else {
                            self::assertEquals(
                                $parentData,
                                $childData
                            );
                        }
                    }
                } else {
                    throw new \Exception('Product model is missing method : ' . $methodName, 1);
                }
            }
        }
    }

    /**
     * Assert product has correct attribute values.
     *
     * @param Product $product
     * @return void
     */
    protected function assertProductHasCorrectAttributeValues(Product $product): void
    {
        /** @var ProductType $productType */
        $productType = $this->productTypeRepository->findByUid($product->getProductType()->getUid());

        $productTypeAttributes = AttributeUtility::findAttributesForProductType($productType->getUid());
        // Check that product have same number of attribute values as product type have attributes.
        self::assertEquals(
            count($productTypeAttributes),
            count($product->getAttributesValues())
        );

        foreach ($productTypeAttributes as $productTypeAttribute) {
            self::assertTrue(
                $product->hasAttributeValueForAttribute($this->attributes[$productTypeAttribute['uid']])
            );
        }
    }

    /**
     * Generate list of uids for object storage data.
     *
     * @param ObjectStorage $objectStorage
     * @return array
     */
    protected function generateListOfIds(ObjectStorage $objectStorage): array
    {
        $values = [];
        if (count($objectStorage) > 0) {
            foreach ($objectStorage as $item) {
                if (method_exists($item, 'getOriginalResource')) {
                    $values[] = $item->getOriginalResource()->getOriginalFile()->getUid();
                } else {
                    $values[] = $item->getUid();
                }
            }
        }

        return $values;
    }
}
