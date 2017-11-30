<?php

namespace Pixelant\PxaProductManager\Tests\Unit\ViewHelpers\Solr;

use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\ViewHelpers\Solr\ProductLinkViewHelper;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class ProductLinkViewHelperTest
 * @package Pixelant\PxaProductManager\Tests\Unit\ViewHelpers\Solr
 */
class ProductLinkViewHelperTest extends UnitTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AccessibleMockObjectInterface|ProductLinkViewHelper
     */
    protected $viewHelper;

    protected function setUp()
    {
        $this->viewHelper = $this->getAccessibleMock(
            ProductLinkViewHelper::class,
            ['dummy'],
            [],
            '',
            false
        );

        $settings['solr']['productManagerTypes'] = 'product_search_type';

        $this->viewHelper->_set('settings', $settings);
    }

    /**
     * @test
     */
    public function generateLinkForProductWhenTypeIsAllowed()
    {
        $document = [
            'uid' => 1,
            'type' => 'product_search_type'
        ];

        $product = new Product();
        $product->_setProperty('uid', 1);

        $productRepository = $this->createPartialMock(ProductRepository::class, ['findByUid']);
        $productRepository
            ->expects($this->once())
            ->method('findByUid')
            ->with(1)
            ->willReturn($product);

        $this->viewHelper->_set('productRepository', $productRepository);

        $this->viewHelper->execute([serialize($document)]);
    }

    /**
     * @test
     */
    public function generateLinkWithInvalidTypeWillReturnEmptyString()
    {
        $document = [
            'uid' => 1,
            'type' => 'invalid'
        ];

        $this->assertEmpty($this->viewHelper->execute([serialize($document)]));
    }

    /**
     * @test
     */
    public function generateLinkWhenProductIsNotValidWillReturnEmptyString()
    {
        $document = [
            'uid' => 1,
            'type' => 'product_search_type'
        ];

        $productRepository = $this->createPartialMock(ProductRepository::class, ['findByUid']);
        $productRepository
            ->expects($this->once())
            ->method('findByUid')
            ->with(1)
            ->willReturn(null);

        $this->viewHelper->_set('productRepository', $productRepository);


        $this->assertEmpty($this->viewHelper->execute([serialize($document)]));
    }
}
