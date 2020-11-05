<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Utility;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Utility\AttributeTcaNamingUtility;

class AttributeTcaNamingUtilityTest extends UnitTestCase
{
    /**
     * @test
     */
    public function translateAttributeToTcaFieldNameReturnTcaFieldName(): void
    {
        $uid = 12;
        $attribute = createEntity(Attribute::class, $uid);

        $expect = 'tx_pxaproductmanager_attribute_12';

        self::assertEquals($expect, AttributeTcaNamingUtility::translateToTcaFieldName($attribute));
    }

    /**
     * @test
     */
    public function translateFileAttributeToTcaFieldNameReturnTcaFieldNameOfFal(): void
    {
        $uid = 12;
        $attribute = createEntity(Attribute::class, ['uid' => $uid, 'type' => Attribute::ATTRIBUTE_TYPE_IMAGE]);

        $expect = 'tx_pxaproductmanager_attribute_fal_12';

        self::assertEquals($expect, AttributeTcaNamingUtility::translateToTcaFieldName($attribute));
    }

    /**
     * @test
     */
    public function isAttributeFieldNameReturnTrueIfAttributeField(): void
    {
        $field = 'tx_pxaproductmanager_attribute_12';

        self::assertTrue(AttributeTcaNamingUtility::isAttributeFieldName($field));
    }

    /**
     * @test
     */
    public function isFileAttributeFieldNameReturnTrueIfFalFieldName(): void
    {
        $field = 'tx_pxaproductmanager_attribute_fal_12';

        self::assertTrue(AttributeTcaNamingUtility::isFileAttributeFieldName($field));
    }

    /**
     * @test
     */
    public function isFileAttributeFieldNameReturnFalseIfNotFalFieldName(): void
    {
        $field = 'tx_pxaproductmanager_attribute_12';

        self::assertFalse(AttributeTcaNamingUtility::isFileAttributeFieldName($field));
    }

    /**
     * @test
     */
    public function extractIdFromFieldNameExtractIntIdFromTcaFieldName(): void
    {
        $field = 'tx_pxaproductmanager_attribute_12';

        self::assertEquals(12, AttributeTcaNamingUtility::extractIdFromFieldName($field));
    }

    /**
     * @test
     */
    public function extractIdFromFalFieldNameExtractIntIdFromTcaFieldName(): void
    {
        $field = 'tx_pxaproductmanager_attribute_fal_1082';

        self::assertEquals(1082, AttributeTcaNamingUtility::extractIdFromFieldName($field));
    }
}
