<?php

namespace Pixelant\PxaProductManager\Tests\Utility;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Controller\NavigationController;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Utility\MainUtility;

/**
 * Class HelperFunctionsTest
 * @package Pixelant\PxaProductManager\Tests\Utility
 */
class MainUtilityTest extends UnitTestCase
{
    /**
     * @test
     */
    public function snakeCasePhraseToWordsTransferUndeScoreToWords()
    {
        $value = 'string_with_underscore';
        $expect = 'String with underscore';

        self::assertEquals($expect, MainUtility::snakeCasePhraseToWords($value));
    }

    /**
     * Custom categories set
     *
     * @return array
     */
    protected function getCategoriesForTest()
    {
        $category1 = new Category();
        $category1->_setProperty('uid', 123);
        $category1->setTitle('Test123');

        $category2 = new Category();
        $category2->_setProperty('uid', 321);
        $category2->setTitle('Test321');

        $category3 = new Category();
        $category3->_setProperty('uid', 456);
        $category3->setTitle('Test456');

        return [$category1, $category2, $category3];
    }
}
