<?php

namespace Pixelant\PxaProductManager\Tests\Utility;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Utility\TCAUtility;

/**
 * Class TcaUtility
 * @package Pixelant\PxaProductManager\Tests\Utility
 */
class TcaUtilityTest extends UnitTestCase
{
    /**
     * @test
     */
    public function getAttributeTCAFieldNameForSimpleAttributeReturnNameForTCA()
    {
        $attributeId = 12;
        $expect = Attribute::TCA_ATTRIBUTE_PREFIX . $attributeId;

        $this->assertEquals($expect, TCAUtility::getAttributeTCAFieldName($attributeId, 0));
    }

    /**
     * @test
     */
    public function getAttributeTCAFieldNameForImageAttributeReturnFalNameForTCA()
    {
        $attributeId = 122;
        $expect = Attribute::TCA_ATTRIBUTE_FILE_PREFIX . Attribute::TCA_ATTRIBUTE_PREFIX . $attributeId;

        $this->assertEquals($expect, TCAUtility::getAttributeTCAFieldName($attributeId, Attribute::ATTRIBUTE_TYPE_IMAGE));
    }

    /**
     * @test
     */
    public function getAttributeTCAFieldNameForFileAttributeReturnFalNameForTCA()
    {
        $attributeId = 122;
        $expect = Attribute::TCA_ATTRIBUTE_FILE_PREFIX . Attribute::TCA_ATTRIBUTE_PREFIX . $attributeId;

        $this->assertEquals($expect, TCAUtility::getAttributeTCAFieldName($attributeId, Attribute::ATTRIBUTE_TYPE_FILE));
    }

    /**
     * @test
     */
    public function isAttributeFieldReturnTrueIfItStartWithAttributePrefix()
    {
        $attributeName = Attribute::TCA_ATTRIBUTE_PREFIX . 'test';
        $this->assertTrue(TCAUtility::isAttributeField($attributeName));
    }

    /**
     * @test
     */
    public function isFalAttributeFieldReturnFalseForSimpleAttribute()
    {
        $attributeName = Attribute::TCA_ATTRIBUTE_PREFIX . 'test';
        $this->assertFalse(TCAUtility::isFalAttributeField($attributeName));
    }

    /**
     * @test
     */
    public function isFalAttributeFieldReturnTrueForFalAttribute()
    {
        $attributeName = Attribute::TCA_ATTRIBUTE_FILE_PREFIX . Attribute::TCA_ATTRIBUTE_PREFIX . 'test';
        $this->assertTrue(TCAUtility::isFalAttributeField($attributeName));
    }

    /**
     * @test
     */
    public function determinateFalAttributeUidFromFieldNameReturnAttributeUid()
    {
        $attributeId = 122;
        $attributeName = Attribute::TCA_ATTRIBUTE_FILE_PREFIX . Attribute::TCA_ATTRIBUTE_PREFIX . $attributeId;

        $this->assertEquals($attributeId, TCAUtility::determinateFalAttributeUidFromFieldName($attributeName));
    }

    /**
     * @test
     */
    public function determinateAttributeUidFromFieldNameReturnAttributeUid()
    {
        $attributeId = 22;
        $attributeName = Attribute::TCA_ATTRIBUTE_PREFIX . $attributeId;

        $this->assertEquals($attributeId, TCAUtility::determinateAttributeUidFromFieldName($attributeName));
    }
}
