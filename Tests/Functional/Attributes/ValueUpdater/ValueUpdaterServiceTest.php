<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Attributes\ValueUpdater;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\Attributes\ValueUpdater\ValueUpdaterService;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package Pixelant\PxaProductManager\Tests\Functional\Attributes\ValueUpdater
 */
class ValueUpdaterServiceTest extends FunctionalTestCase
{
    /**
     * @var ValueUpdaterService
     */
    protected $subject;

    protected $testExtensionsToLoad = [
        'typo3conf/ext/pxa_product_manager'
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->subject = GeneralUtility::makeInstance(ObjectManager::class)->get(ValueUpdaterService::class);
        $this->importDataSet(__DIR__ . '/../../../Fixtures/attributevalue.xml');
    }

    /**
     * @test
     */
    public function updateWillUpdateExistingValue()
    {
        $product = createEntity(Product::class, ['_localizedUid' => 10]);
        $attribute = createEntity(Attribute::class, ['_localizedUid' => 101]);

        $newValue = 'here new value';

        $this->subject->update($product, $attribute, $newValue);

        $attributeValue = GeneralUtility::makeInstance(ObjectManager::class)->get(AttributeValueRepository::class)->findByUid(20);

        $this->assertEquals($newValue, $attributeValue->getValue());
    }

    /**
     * @test
     */
    public function updateWillCreateAttributeValueIfDoesnotExist()
    {
        $productUid = 200;
        $attributeUid = 202;

        $product = createEntity(Product::class, ['_localizedUid' => $productUid]);
        $attribute = createEntity(Attribute::class, ['_localizedUid' => $attributeUid]);

        $newValue = 'new created value';

        $this->subject->update($product, $attribute, $newValue);

        $queryBuilder =  GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_pxaproductmanager_domain_model_attributevalue');

        $attributeValueRow = $queryBuilder
            ->select('*')
            ->from('tx_pxaproductmanager_domain_model_attributevalue')
            ->setMaxResults(1)
            ->orderBy('uid', 'desc')
            ->execute()
            ->fetch();

        $this->assertEquals($newValue, $attributeValueRow['value']);
        $this->assertEquals($productUid, $attributeValueRow['product']);
        $this->assertEquals($attributeUid, $attributeValueRow['attribute']);
    }
}
