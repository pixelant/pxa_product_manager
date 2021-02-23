<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Attributes\ValueUpdater;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\Attributes\ValueUpdater\ValueUpdaterService;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class ValueUpdaterServiceTest extends FunctionalTestCase
{
    /**
     * @var ValueUpdaterService
     */
    protected $subject;

    protected $testExtensionsToLoad = [
        'typo3conf/ext/pxa_product_manager',
        'typo3conf/ext/demander',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = GeneralUtility::makeInstance(ObjectManager::class)->get(ValueUpdaterService::class);
        $this->importDataSet(__DIR__ . '/../../../Fixtures/attributevalue.xml');
    }

    /**
     * @test
     */
    public function updateWillUpdateExistingValue(): void
    {
        $product = TestsUtility::createEntity(Product::class, ['_localizedUid' => 10]);
        $attribute = TestsUtility::createEntity(Attribute::class, ['_localizedUid' => 101]);

        $newValue = 'here new value';

        $this->subject->update($product, $attribute, $newValue);

        $attributeValue = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(AttributeValueRepository::class)
            ->findByUid(20);

        self::assertEquals($newValue, $attributeValue->getValue());
    }

    /**
     * @test
     */
    public function updateWillCreateAttributeValueIfDoesnotExist(): void
    {
        $productUid = 200;
        $attributeUid = 202;

        $product = TestsUtility::createEntity(Product::class, ['_localizedUid' => $productUid]);
        $attribute = TestsUtility::createEntity(Attribute::class, ['_localizedUid' => $attributeUid]);

        $newValue = 'new created value';

        $this->subject->update($product, $attribute, $newValue);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_pxaproductmanager_domain_model_attributevalue');

        $attributeValueRow = $queryBuilder
            ->select('*')
            ->from('tx_pxaproductmanager_domain_model_attributevalue')
            ->setMaxResults(1)
            ->orderBy('uid', 'desc')
            ->execute()
            ->fetch();

        self::assertEquals($newValue, $attributeValueRow['value']);
        self::assertEquals($productUid, $attributeValueRow['product']);
        self::assertEquals($attributeUid, $attributeValueRow['attribute']);
    }

    /**
     * @test
     */
    public function updateWillCreateAttributeValueWithCommaWrapIfIsSelectBox(): void
    {
        $productUid = 1;
        $attributeUid = 2;

        $product = TestsUtility::createEntity(Product::class, ['_localizedUid' => $productUid]);
        $attribute = TestsUtility::createEntity(
            Attribute::class,
            [
                '_localizedUid' => $attributeUid,
                'type' => Attribute::ATTRIBUTE_TYPE_DROPDOWN,
            ]
        );

        $newValue = '2';
        $expectValue = ",${newValue},";

        $this->subject->update($product, $attribute, $newValue);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_pxaproductmanager_domain_model_attributevalue');

        $attributeValueRow = $queryBuilder
            ->select('*')
            ->from('tx_pxaproductmanager_domain_model_attributevalue')
            ->setMaxResults(1)
            ->orderBy('uid', 'desc')
            ->execute()
            ->fetch();

        self::assertEquals($expectValue, $attributeValueRow['value'], 'given value: ' . $attributeValueRow['value']);
        self::assertEquals($productUid, $attributeValueRow['product']);
        self::assertEquals($attributeUid, $attributeValueRow['attribute']);
    }
}
