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
    public function sortRawResultByUidListWillSortResultsAccordingToUidList($array, $expectResult, $uidList, $order)
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

    /**
     * @test
     */
    public function removeDuplicationFromQueryResultWillRemoveDuplicatedEntries()
    {
        $mockedQueryResult = $this->getAccessibleMock(
            QueryResult::class,
            ['initialize'],
            [],
            '',
            false
        );
        $product1 = (new Product());
        $product1->_setProperty('uid', 1);

        $product2 = (new Product());
        $product2->_setProperty('uid', 2);

        $product3 = (new Product());
        $product3->_setProperty('uid', 1);

        $product4 = (new Product());
        $product4->_setProperty('uid', 4);

        $queryResult = [
            $product1, $product2, $product3, $product4
        ];
        $expect = [$product1, $product2, $product4];

        $mockedQueryResult->_set('queryResult', $queryResult);

        $this->assertEquals(
            $expect,
            array_values($this->removeDuplicatedEntries($mockedQueryResult)->toArray()) // Reset keys
        );
    }

    /**
     * @test
     */
    public function removeDuplicationFromRawResultWillRemoveDuplicatedEntries()
    {
        $product1 = ['uid' => 1];
        $product2 = ['uid' => 5];
        $product3 = ['uid' => 2];
        $product4 = ['uid' => 2];
        $product5 = ['uid' => 5];
        $product6 = ['uid' => 6];

        $rawResult = [
            $product1, $product2, $product3, $product4, $product5, $product6
        ];
        $expect = [$product1, $product2, $product3, $product6];

        $this->assertEquals(
            $expect,
            array_values($this->removeDuplicatedEntries($rawResult)) // Reset keys
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
