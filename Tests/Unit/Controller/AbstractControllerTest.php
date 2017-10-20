<?php

namespace Pixelant\PxaProductManager\Tests\Unit;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Controller\AbstractController;
use Pixelant\PxaProductManager\Domain\Model\Product;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;

/**
 * Class AbstractControllerTest
 * @package Pixelant\PxaProductManager\Tests\Unit
 */
class AbstractControllerTest extends UnitTestCase
{

    /**
     * @test
     * @dataProvider dataForSortQueryByUids
     */
    public function sortQueryResultsByUidListWillSortResultsAccordingToUidList($queryResult, $expectResult, $uidList)
    {
        $mockedController = $this->getAccessibleMock(
            AbstractController::class,
            ['dummy']
        );

        $mockedQueryResult = $this->getAccessibleMock(
            QueryResult::class,
            ['dummy'],
            [],
            '',
            false
        );
        $mockedQueryResult->_set('queryResult', $queryResult);

        $result = $mockedController->_call('sortQueryResultsByUidList', $mockedQueryResult, $uidList);

        $this->assertEquals(
            $expectResult,
            $result
        );
    }

    public function dataForSortQueryByUids()
    {
        for ($i = 1; $i <= 4; $i++) {
            ${'product' . ($i)} = new Product();
            ${'product' . ($i)}->_setProperty('uid', $i);
        }

        $uidListAll = [2, 4, 1, 3];
        $uidListSelected = [3, 2, 4, 1];

        /** @noinspection PhpUndefinedVariableInspection */
        return [
            'all_product_sorted_by_special_order' => [
                [
                    $product1, $product2, $product3, $product4
                ],
                [
                    2 => $product2,
                    4 => $product4,
                    1 => $product1,
                    3 => $product3 // according to list
                ],
                $uidListAll
            ],
            'selected_product_sorted_by_special_order' => [
                [
                    $product1, $product2, $product4
                ],
                [
                    2 => $product2,
                    4 => $product4,
                    1 => $product1 // according to list
                ],

                $uidListSelected
            ]
        ];
    }
}
