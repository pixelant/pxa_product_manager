<?php
declare(strict_types=1);
namespace Pixelant\PxaProductManager\Tests\Unit\Utility;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Utility\AttributeTcaNamingUtility;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Utility
 */
class AttributeTcaNamingUtilityTest extends UnitTestCase
{
    /**
     * @test
     */
    public function translateAttributeToTcaFieldNameReturnTcaFieldName()
    {
        $uid = 12;
        $attribute = createEntity(Attribute::class, $uid);

        $expect = 'tx_pxaproductmanager_attribute_12';

        $this->assertEquals($expect, AttributeTcaNamingUtility::translateToTcaFieldName($attribute));
    }

    /**
     * @test
     */
    public function translateFileAttributeToTcaFieldNameReturnTcaFieldNameOfFal()
    {
        $uid = 12;
        $attribute = createEntity(Attribute::class, ['uid' => $uid, 'type' => Attribute::ATTRIBUTE_TYPE_IMAGE]);

        $expect = 'tx_pxaproductmanager_attribute_fal_12';

        $this->assertEquals($expect, AttributeTcaNamingUtility::translateToTcaFieldName($attribute));
    }

    /**
     * @test
     */
    public function isAttributeFieldNameReturnTrueIfAttributeField()
    {
        $field = 'tx_pxaproductmanager_attribute_12';

        $this->assertTrue(AttributeTcaNamingUtility::isAttributeFieldName($field));
    }

    /**
     * @test
     */
    public function isFileAttributeFieldNameReturnTrueIfFalFieldName()
    {
        $field = 'tx_pxaproductmanager_attribute_fal_12';

        $this->assertTrue(AttributeTcaNamingUtility::isFileAttributeFieldName($field));
    }

    /**
     * @test
     */
    public function isFileAttributeFieldNameReturnFalseIfNotFalFieldName()
    {
        $field = 'tx_pxaproductmanager_attribute_12';

        $this->assertFalse(AttributeTcaNamingUtility::isFileAttributeFieldName($field));
    }

    /**
     * @test
     */
    public function extractIdFromFieldNameExtractIntIdFromTcaFieldName()
    {
        $field = 'tx_pxaproductmanager_attribute_12';

        $this->assertEquals(12, AttributeTcaNamingUtility::extractIdFromFieldName($field));
    }

    /**
     * @test
     */
    public function extractIdFromFalFieldNameExtractIntIdFromTcaFieldName()
    {
        $field = 'tx_pxaproductmanager_attribute_fal_1082';

        $this->assertEquals(1082, AttributeTcaNamingUtility::extractIdFromFieldName($field));
    }
}
