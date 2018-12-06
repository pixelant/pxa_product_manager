<?php


namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Order;
use Pixelant\PxaProductManager\Domain\Model\Product;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class OrderTest
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\Model
 */
class OrderTest extends UnitTestCase
{
    /**
     * @var Order
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();

        $this->subject = new Order();
    }

    /**
     * @test
     */
    public function defaultHiddenIsFalse()
    {
        $this->assertFalse($this->subject->isHidden());
    }

    /**
     * @test
     */
    public function productsCanBeSet()
    {
        $objectStorage = new ObjectStorage();

        $this->subject->setProducts($objectStorage);

        $this->assertSame($objectStorage, $this->subject->getProducts());
    }

    /**
     * @test
     */
    public function productCanBeAddedToProducts()
    {
        $objectStorage = new ObjectStorage();
        $product = new Product();

        $objectStorage->attach($product);
        $this->subject->addProduct($product);

        $this->assertEquals($objectStorage, $this->subject->getProducts());
    }

    /**
     * @test
     */
    public function productCanBeRemovedFromProducts()
    {
        $objectStorage = new ObjectStorage();
        $product = new Product();

        $objectStorage->attach($product);
        $objectStorage->detach($product);

        $this->subject->addProduct($product);
        $this->subject->removeProduct($product);

        $this->assertEquals($objectStorage, $this->subject->getProducts());
    }

    /**
     * @test
     */
    public function completeCanBeSetAndDefaultFalse()
    {
        $this->assertFalse($this->subject->isComplete());

        $this->subject->setComplete(true);

        $this->assertTrue($this->subject->isComplete());
    }

    /**
     * @test
     */
    public function feUserCanBeSet()
    {
        $user = new FrontendUser();

        $this->subject->setFeUser($user);

        $this->assertSame($user, $this->subject->getFeUser());
    }

    /**
     * @test
     */
    public function serializedOrderFieldsCanBeSet()
    {
        $value = 'test value';

        $this->subject->setSerializedOrderFields($value);

        $this->assertEquals($value, $this->subject->getSerializedOrderFields());
    }

    /**
     * @test
     */
    public function serializedProductsQuantityCanBeSet()
    {
        $value = 'value';

        $this->subject->setSerializedProductsQuantity($value);

        $this->assertEquals($value, $this->subject->getSerializedProductsQuantity());
    }

    /**
     * @test
     */
    public function externalIdCanBeSet()
    {
        $value = '123321';

        $this->subject->setExternalId($value);

        $this->assertEquals($value, $this->subject->getExternalId());
    }

    /**
     * @test
     */
    public function canGetCrdate()
    {
        $dateTime = new \DateTime();
        $this->subject->_setProperty('crdate', $dateTime);

        $this->assertSame($dateTime, $this->subject->getCrdate());
    }

    /**
     * @test
     */
    public function getProductsQuantityReturnUnserializedProductsQuantity()
    {
        $value = [
            12 => 33,
            21 => 44
        ];

        $this->subject->setSerializedProductsQuantity(serialize($value));

        $this->assertEquals($value, $this->subject->getProductsQuantity());
    }

    /**
     * @test
     */
    public function setProductsQuantityWillSetSerializeProductsQuantity()
    {
        $value = [
            1 => 99,
            2 => 88
        ];

        $this->subject->setProductsQuantity($value);

        $this->assertEquals(serialize($value), $this->subject->getSerializedProductsQuantity());
    }

    /**
     * @test
     */
    public function getOrderFieldsReturnUnserializedOrderFields()
    {
        $value = [
            'name' => [
                'value' => 'test'
            ],
            'email' => [
                'value' => 'email@com'
            ]
        ];

        $this->subject->setSerializedOrderFields(serialize($value));

        $this->assertEquals($value, $this->subject->getOrderFields());
    }

    /**
     * @test
     */
    public function setOrderFieldsWillSetSerializeOrderFields()
    {
        $value = [
            'name' => [
                'value' => 'Name'
            ],
            'email' => [
                'value' => 'email@site.com'
            ]
        ];

        $this->subject->setOrderFields($value);

        $this->assertEquals(serialize($value), $this->subject->getSerializedOrderFields());
    }

    /**
     * @test
     */
    public function setOrderFieldValueWillSetNewOrderFieldValue()
    {
        $orderFields = [
            'name' => [
                'value' => '123'
            ]
        ];
        $this->subject->setOrderFields($orderFields);

        $newValue = 'new field value';

        $this->subject->setOrderField('name', $newValue);

        $this->assertEquals($newValue, $this->subject->getOrderField('name'));
    }

    /**
     * @test
     */
    public function setOrderFieldValueWithTypeWillSetNewOrderFieldValue()
    {
        $orderFields = [
            'test' => [
                'value' => 'test value',
                'type' => 'type1'
            ]
        ];
        $this->subject->setOrderFields($orderFields);

        $newValue = 'new value 123';

        $this->subject->setOrderField('test', $newValue, 'type2');
        $orderFields['test']['value'] = $newValue;
        $orderFields['test']['type'] = 'type2';

        $this->assertEquals($orderFields, $this->subject->getOrderFields());
    }

    /**
     * @test
     */
    public function getOrderFieldValueWillReturnOrderFieldValueIfExist()
    {
        $orderFields = [
            'name' => [
                'value' => '123'
            ],
            'email' => [
                'value' => 'email@site.com'
            ]
        ];
        $this->subject->setOrderFields($orderFields);

        $expect = 'email@site.com';

        $this->assertEquals($expect, $this->subject->getOrderField('email'));
    }

    /**
     * @test
     */
    public function getOrderFieldWillReturnNullIfDoesNotExist()
    {
        $orderFields = [
            'name' => [
                'value' => '123'
            ],
            'email' => [
                'value' => 'email@site.com'
            ]
        ];
        $this->subject->setOrderFields($orderFields);

        $this->assertNull($this->subject->getOrderField('test'));
    }

    /**
     * @test
     */
    public function removeOrderFieldWillRemoveOrderFieldIfExist()
    {
        $orderFields = [
            'name' => [
                'value' => '123'
            ],
            'email' => [
                'value' => 'email@site.com'
            ]
        ];
        $this->subject->setOrderFields($orderFields);
        $expect = [
            'name' => [
                'value' => '123'
            ]
        ];

        $this->subject->removeOrderField('email');

        $this->assertEquals($expect, $this->subject->getOrderFields());
    }

    /**
     * @test
     */
    public function removeOrderFieldWillDoNothingIfFieldDoesNotExist()
    {
        $orderFields = [
            'name' => [
                'value' => '123'
            ],
            'email' => [
                'value' => 'email@site.com'
            ]
        ];
        $this->subject->setOrderFields($orderFields);

        $this->subject->removeOrderField('test');

        $this->assertEquals($orderFields, $this->subject->getOrderFields());
    }

    /**
     * @test
     */
    public function canGetAndSetCheckoutType()
    {
        $value = 'test';

        $this->subject->setCheckoutType($value);

        $this->assertEquals($value, $this->subject->getCheckoutType());
    }

    /**
     * @test
     */
    public function defaultCheckoutTypeIsDefault()
    {
        $value = 'default';

        $this->assertEquals($value, $this->subject->getCheckoutType());
    }
}
