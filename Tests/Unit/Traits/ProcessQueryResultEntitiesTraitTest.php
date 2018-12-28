<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Controller;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Controller\AbstractController;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Traits\ProcessQueryResultEntitiesTrait;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;

/**
 * Class AbstractControllerTest
 * @package Pixelant\PxaProductManager\Tests\Unit
 */
class ProcessQueryResultEntitiesTraitTest extends UnitTestCase
{
    use ProcessQueryResultEntitiesTrait;

    /**
     * @test
     * @dataProvider dataForSortQueryByUids
     */
    public function sortQueryResultsByUidListWillSortResultsAccordingToUidList($queryResult, $expectResult, $uidList, $order)
    {
        $mockedQueryResult = $this->getAccessibleMock(
            QueryResult::class,
            ['initialize'],
            [],
            '',
            false
        );
        $mockedQueryResult->_set('queryResult', $queryResult);

        $result = $this->sortEntitiesAccordingToList($mockedQueryResult, $uidList, 'uid', $order);

        $this->assertSame(
            $mockedQueryResult,
            $result
        );

        $resultOrder = [];
        foreach ($result as $product) {
            $resultOrder[] = $product->getUid();
        }

        $this->assertEquals(
            $expectResult,
            $resultOrder
        );
    }

    /**
     * @test
     * @dataProvider dataForSortArrayByUids
     */
    public function sortArrayByUidListWillSortResultsAccordingToUidList($array, $expectResult, $uidList, $order)
    {
        $result = $this->sortEntitiesAccordingToList($array, $uidList, 'uid', $order);

        $resultOrder = [];
        foreach ($result as $product) {
            $resultOrder[] = $product['uid'];
        }

        $this->assertEquals(
            $expectResult,
            $resultOrder
        );
    }

    public function dataForSortArrayByUids()
    {
        for ($i = 1; $i <= 4; $i++) {
            ${'product' . ($i)} = ['uid' => $i];
        }

        $uidListAll = [3, 4, 1, 2];
        $uidListSelected = [3, 4, 2, 1];

        /** @noinspection PhpUndefinedVariableInspection */
        return [
            'all_product_sorted_by_special_order' => [
                [
                    $product1, $product2, $product3, $product4
                ],
                [
                    3,
                    4,
                    1,
                    2 // according to list
                ],
                $uidListAll,
                false
            ],
            'selected_product_sorted_by_special_order' => [
                [
                    $product1, $product2, $product4
                ],
                [
                    4,
                    2,
                    1 // according to list
                ],
                $uidListSelected,
                false
            ],
            'product_sorted_by_special_order_desc' => [
                [
                    $product1, $product2, $product3, $product4
                ],
                [
                    2,
                    1,
                    4,
                    3 // according to list
                ],
                $uidListAll,
                true
            ],
        ];
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
                    2,
                    4,
                    1,
                    3 // according to list
                ],
                $uidListAll,
                false
            ],
            'selected_product_sorted_by_special_order' => [
                [
                    $product1, $product2, $product4
                ],
                [
                    2,
                    4,
                    1 // according to list
                ],
                $uidListSelected,
                false
            ],
            'product_sorted_by_special_order_desc' => [
                [
                    $product1, $product2, $product3, $product4
                ],
                [
                    3,
                    1,
                    4,
                    2// according to list
                ],
                $uidListAll,
                true
            ],
        ];
    }
}
