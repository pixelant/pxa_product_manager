<?php

namespace Pixelant\PxaProductManager\Tests\Utility;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Attribute;

/**
 * Class TcaUtility
 * @package Pixelant\PxaProductManager\Tests\Utility
 */
class TcaUtility extends UnitTestCase
{
    /**
     * @test
     */
    public function getAttributeTCAFieldNameForSimpleAttributeReturnNameForTCA()
    {
        $attributeId = 12;
        $expect = Attribute::TCA_ATTRIBUTE_PREFIX . $attributeId;

        $this->assertEquals($expect, \Pixelant\PxaProductManager\Utility\TCAUtility::getAttributeTCAFieldName($attributeId, 0));
    }

    /**
     * @test
     */
    public function getAttributeTCAFieldNameForImageAttributeReturnFalNameForTCA()
    {
        $attributeId = 122;
        $expect = Attribute::TCA_ATTRIBUTE_FILE_PREFIX . Attribute::TCA_ATTRIBUTE_PREFIX . $attributeId;

        $this->assertEquals($expect, \Pixelant\PxaProductManager\Utility\TCAUtility::getAttributeTCAFieldName($attributeId, Attribute::ATTRIBUTE_TYPE_IMAGE));
    }

    /**
     * @test
     */
    public function getAttributeTCAFieldNameForFileAttributeReturnFalNameForTCA()
    {
        $attributeId = 122;
        $expect = Attribute::TCA_ATTRIBUTE_FILE_PREFIX . Attribute::TCA_ATTRIBUTE_PREFIX . $attributeId;

        $this->assertEquals($expect, \Pixelant\PxaProductManager\Utility\TCAUtility::getAttributeTCAFieldName($attributeId, Attribute::ATTRIBUTE_TYPE_FILE));
    }
}
