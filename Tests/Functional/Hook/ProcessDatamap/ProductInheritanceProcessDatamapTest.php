<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Hook\ProcessDatamap;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\Attributes\ConfigurationProvider\ConfigurationProviderFactory;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Model\ProductType;
use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductTypeRepository;
use Pixelant\PxaProductManager\Utility\DataInheritanceUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\WorkspaceAspect;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class ProductInheritanceProcessDatamapTest extends FunctionalTestCase
{
    public const VALUE_BACKENDUSERID = 1;

    /**
     * @var DataHandler
     */
    protected $subject;

    /**
     * @var AttributeValueRepository
     */
    protected $attributeValueRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var ProductTypeRepository
     */
    protected $productTypeRepository;

    /**
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
        $attributes = \Pixelant\PxaProductManager\Utility\AttributeUtility::findAllAttributes();

        foreach ($attributes as $attribute) {
            if ($attribute['uid'] === 0) {
                continue;
            }

            $this->attributes[$attribute['uid']] = $attribute;

            $GLOBALS['TCA']['tx_pxaproductmanager_domain_model_attributevalue']['types'][(string)$attribute['uid']] = [
                'showitem' => 'value',
                'columnsOverrides' => [
                    'value' => ConfigurationProviderFactory::create((int)$attribute['uid'])->get(),
                ],
            ];
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
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_attributeset_record_mm.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_domain_model_attribute.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_domain_model_attributeset.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_domain_model_attributevalue.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_domain_model_option.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_domain_model_product.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_domain_model_producttype.xml');
        $this->importDataSet(__DIR__ . '/../../../Fixtures/tx_pxaproductmanager_product_pages_mm.xml');

        $this->createAttributeValueTypeConfiguration();
    }

    /**
     * @test
     */
    public function saveNewProductWithParentInheritsCorrectFields(): void
    {
        $this->importDataSets();

        // Fretch parent product.
        /** @var Product $parentProduct */
        $parentProduct = $this->productRepository->findByUid(1);

        // Fetch Product Type.
        /** @var ProductType $productType */
        $productType = $this->productTypeRepository->findByUid(1);

        // Create a product that is a child to Product 1.
        /** @var DataHandler $dataHandler */
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $newRecordId = StringUtility::getUniqueId('NEW');
        $cmd = [];
        $data['tx_pxaproductmanager_domain_model_product'][$newRecordId] = [
            'name' => 'Child Product - Child to product 1',
            'pid' => $parentProduct->getPid(),
            'parent' => 'tx_pxaproductmanager_domain_model_product_' . $parentProduct->getUid(),
            'product_type' => $productType->getUid(),
            'singleview_page' => 'pages_85',
        ];

        $dataHandler->start($data, $cmd);
        $dataHandler->process_datamap();
        $dataHandler->process_cmdmap();

        $newChildId = $dataHandler->substNEWwithIDs[$newRecordId];

        /** @var Product $persistedProduct */
        $persistedProduct = $this->productRepository->findByUid($newChildId);

        self::assertInstanceOf(Product::class, $persistedProduct);

        $this->assertInheritedFields($productType, $parentProduct, $persistedProduct);
    }

    /**
     * Asserts inherited fields between parent and child product.
     *
     * @param ProductType $productType
     * @param Product $parentProduct
     * @param Product $childProduct
     * @return void
     */
    protected function assertInheritedFields(
        ProductType $productType,
        Product $parentProduct,
        Product $childProduct
    ): void {
        $inheritedFields = DataInheritanceUtility::getInheritedFieldsForProductType($productType->getUid()) ?? [];

        foreach ($inheritedFields as $inheritedField) {
            if (strpos($inheritedField, 'attribute.') !== false) {
                $uid = (string)array_pop(explode('.', (string)$inheritedField));
                $attributeIdentifier = $this->attributes[$uid]['identifier'];

                self::assertEquals(
                    $parentProduct->getAttributeValue()[$attributeIdentifier]->getRenderValue(),
                    $childProduct->getAttributeValue()[$attributeIdentifier]->getRenderValue()
                );
            } else {
                $methodName = 'get' . GeneralUtility::underscoredToUpperCamelCase($inheritedField);

                if (method_exists($parentProduct, $methodName)) {
                    self::assertEquals(
                        $parentProduct->{$methodName}(),
                        $childProduct->{$methodName}()
                    );
                }
            }
        }
    }
}
