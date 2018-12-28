<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Controller;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\Controller\AbstractController;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Utility\TCAUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class AbsctactControllerTest extends FunctionalTestCase
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    protected $testExtensionsToLoad = ['typo3conf/ext/pxa_product_manager'];

    protected function setUp()
    {
        parent::setUp();
        $this->importDataSet(__DIR__ . '/../Fixtures/tx_pxaproductmanager_domain_model_attribute.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/sys_category.xml');

        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->categoryRepository = $objectManager->get(CategoryRepository::class);
        $this->productRepository = $objectManager->get(ProductRepository::class);
    }

    /**
     * @test
     */
    public function getAvailableFilteringOptionsForProductsGenerateValidDataForFilter()
    {
        $json = json_encode($this->getAttributesToValues());
        $products = [
            [
                'uid' => 1,
                TCAUtility::ATTRIBUTES_VALUES_FIELD_NAME => $json
            ],
            [
                'uid' => 2,
                TCAUtility::ATTRIBUTES_VALUES_FIELD_NAME => $json
            ],
            [
                'uid' => 3,
                TCAUtility::ATTRIBUTES_VALUES_FIELD_NAME => $json
            ],
        ];

        $mockedController = $this->getAccessibleMock(
            AbstractController::class,
            ['dummy'],
            [],
            '',
            true
        );
        $mockedController->_set('productRepository', $this->productRepository);
        $mockedController->_set('categoryRepository', $this->categoryRepository);

        list($availableOptions, $availableCategories) = $mockedController->_call(
            'getAvailableFilteringOptionsForProducts',
            $products
        );

        $expectCategories = [4, 5, 6];
        $expectOptions = [556, 889, 336];

        $this->asserEqualsCustom($expectCategories, $availableCategories);
        $this->asserEqualsCustom($expectOptions, $availableOptions);
    }

    /**
     * Custom compare
     * @param array $expect
     * @param array $result
     */
    protected function asserEqualsCustom(array $expect, array $result)
    {
        $count = count($expect);
        $this->assertCount(
            $count,
            $result
        );

        for ($i = 0; $i < $count; $i++) {
            $this->assertTrue($expect[$i] === $result[$i]);
        }
    }

    /**
     * Dummy for serialization
     *
     * @return array
     */
    protected function getAttributesToValues()
    {
        return [
            1 => '456,789',
            2 => '556,889',
            3 => '336,556'
        ];
    }
}
