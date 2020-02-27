<?php

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
        $attribute = makeDomainInstanceWithProperties(Attribute::class, $uid);

        $expect = 'tx_pxaproductmanager_attribute_12';

        $this->assertEquals($expect, AttributeTcaNamingUtility::translateAttributeToTcaFieldName($attribute));
    }
}
