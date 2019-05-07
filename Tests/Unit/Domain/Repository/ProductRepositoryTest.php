<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Repository;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\DTO\Demand;
use Pixelant\PxaProductManager\Domain\Model\Filter;
use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use Pixelant\PxaProductManager\Domain\Repository\FilterRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Class ProductRepositoryTest
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\Repository
 */
class ProductRepositoryTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createConstraintsWithEmptyDemandIncludingDiscontinuedProductsWontCallMatching()
    {
        $mockedQuery = $this->createMock(QueryInterface::class);
        $mockedRepository = $this->getAccessibleMock(
            ProductRepository::class,
            ['createDiscontinuedConstraints'],
            [],
            '',
            false
        );

        $mockedRepository
        ->expects($this->never())
        ->method('createDiscontinuedConstraints')
        ->with($mockedQuery);

        $demand = new Demand();
        $demand->setIncludeDiscontinued(true);

        $mockedRepository->_call('createConstraints', $mockedQuery, $demand);
    }

    /**
     * @test
     */
    public function createConstraintsWithEmptyDemandWithoutDiscontinuedProductsWillReturnConstraints()
    {
        $mockedQuery = $this->createMock(QueryInterface::class);
        $mockedRepository = $this->getAccessibleMock(
            ProductRepository::class,
            ['createDiscontinuedConstraints'],
            [],
            '',
            false
        );

        $mockedRepository
            ->expects($this->once())
            ->method('createDiscontinuedConstraints')
            ->with($mockedQuery);

        $demand = new Demand();

        $constraints = $mockedRepository->_call('createConstraints', $mockedQuery, $demand);
        $this->assertTrue(array_key_exists('discontinued', $constraints));
    }

    /**
     * @test
     */
    public function createConstraintsWithDemandIncludingDiscontinuedProductsWillReturnConstraints()
    {
        $categories = [123];
        $filters = [
            [
                'value' => 123,
                'type' => Filter::TYPE_ATTRIBUTES
            ]
        ];
        $demand = new Demand();
        $demand->setCategories($categories);
        $demand->setFilters($filters);
        $demand->setIncludeDiscontinued(true);

        $mockedQuery = $this->getMockBuilder(QueryInterface::class)->getMock();
        $mockedRepository = $this->getAccessibleMock(
            ProductRepository::class,
            [
                'createFilteringConstraints',
                'createCategoryConstraints',
                'createDiscontinuedConstraints'
            ],
            [],
            '',
            false
        );

        $mockedRepository
            ->expects($this->once())
            ->method('createFilteringConstraints')
            ->with($mockedQuery, $filters, $demand->getFiltersConjunction())
            ->willReturn(1);

        $mockedRepository
            ->expects($this->once())
            ->method('createCategoryConstraints')
            ->with($mockedQuery, $categories, $demand->getCategoryConjunction());

        $mockedRepository
            ->expects($this->never())
            ->method('createDiscontinuedConstraints')
            ->with($mockedQuery);


        $constraints = $mockedRepository->_call('createConstraints', $mockedQuery, $demand);

        $expectConstraints = ['categories', 'filters'];
        $this->assertEquals($expectConstraints, array_keys($constraints));
    }

    /**
     * @test
     */
    public function createConstraintsWithDemandWillReturnConstraints()
    {
        $categories = [123];
        $filters = [
            [
                'value' => 123,
                'type' => Filter::TYPE_ATTRIBUTES
            ]
        ];
        $demand = new Demand();
        $demand->setCategories($categories);
        $demand->setFilters($filters);

        $mockedQuery = $this->getMockBuilder(QueryInterface::class)->getMock();
        $mockedRepository = $this->getAccessibleMock(
            ProductRepository::class,
            [
                'createFilteringConstraints',
                'createCategoryConstraints',
                'createDiscontinuedConstraints'
            ],
            [],
            '',
            false
        );

        $mockedRepository
            ->expects($this->once())
            ->method('createFilteringConstraints')
            ->with($mockedQuery, $filters, $demand->getFiltersConjunction())
            ->willReturn(1);

        $mockedRepository
            ->expects($this->once())
            ->method('createCategoryConstraints')
            ->with($mockedQuery, $categories, $demand->getCategoryConjunction());

        $mockedRepository
            ->expects($this->once())
            ->method('createDiscontinuedConstraints')
            ->with($mockedQuery);

        $constraints = $mockedRepository->_call('createConstraints', $mockedQuery, $demand);

        $expectConstraints = ['discontinued', 'categories', 'filters'];
        $this->assertEquals($expectConstraints, array_keys($constraints));
    }

    /**
     * @test
     */
    public function constraintsAreCreatedForFilters()
    {
        $fakeFilter1 = new Filter();
        $fakeFilter1->_setProperty('uid', 22);
        $fakeFilter1->setType(Filter::TYPE_ATTRIBUTES);

        $fakeFilter2 = new Filter();
        $fakeFilter2->_setProperty('uid', 33);
        $fakeFilter2->setType(Filter::TYPE_ATTRIBUTES);
        $fakeFilter2->setInverseConjunction(true);

        $fakeFilter3 = new Filter();
        $fakeFilter3->_setProperty('uid', 44);
        $fakeFilter3->setType(Filter::TYPE_CATEGORIES);

        $filters = [
            [
                'uid' => 22,
                'value' => [4, 5],
                'attributeUid' => 6,
            ],
            [
                'uid' => 33,
                'value' => [2],
                'attributeUid' => 5,
            ],
            [
                'uid' => 44,
                'value' => ['cat1', 'cat2'],
                'attributeUid' => 88,
            ]
        ];

        $mockedFilterRepository = $this->createPartialMock(FilterRepository::class, ['findByUid']);
        $mockedFilterRepository
            ->expects($this->at(0))
            ->method('findByUid')
            ->with(22)
            ->willReturn($fakeFilter1);

        $mockedFilterRepository
            ->expects($this->at(1))
            ->method('findByUid')
            ->with(33)
            ->willReturn($fakeFilter2);

        $mockedFilterRepository
            ->expects($this->at(2))
            ->method('findByUid')
            ->with(44)
            ->willReturn($fakeFilter3);

        $mockedQuery = $this->createMock(QueryInterface::class);
        $mockedAttributeValueRepository = $this->createPartialMock(
            AttributeValueRepository::class,
            ['findAttributeValuesByAttributeAndValues']
        );
        $mockedAttributeValueRepository
            ->expects($this->at(0))
            ->method('findAttributeValuesByAttributeAndValues')
            ->with(
                6,
                [4, 5],
                'or', // default conjunction
                true
            )
            ->willReturn([['uid' => 123]]);

        $mockedAttributeValueRepository
            ->expects($this->at(1))
            ->method('findAttributeValuesByAttributeAndValues')
            ->with(
                5,
                [2],
                'and', // invert conjunction
                true
            )
            ->willReturn([['uid' => 123]]);

        $mockedRepository = $this->getAccessibleMock(
            ProductRepository::class,
            ['createConstraintFromConstraintsArray'],
            [],
            '',
            false
        );

        $mockedRepository->_set('attributeValueRepository', $mockedAttributeValueRepository);
        $mockedRepository->_set('filterRepository', $mockedFilterRepository);


        $mockedRepository
            ->expects($this->exactly(count($filters) + 1))
            ->method('createConstraintFromConstraintsArray');

        $mockedRepository->_call('createFilteringConstraints', $mockedQuery, $filters);
    }

    /**
     * @test
     */
    public function constraintsAreCreatedForCategories()
    {
        $mockedQuery = $this->createMock(QueryInterface::class);
        $categories = [123, 321];

        $mockedRepository = $this->getAccessibleMock(
            ProductRepository::class,
            ['createConstraintFromConstraintsArray'],
            [],
            '',
            false
        );

        $mockedRepository
            ->expects($this->once())
            ->method('createConstraintFromConstraintsArray');

        $mockedRepository->_call('createCategoryConstraints', $mockedQuery, $categories);
    }

    /**
     * @test
     * @expectedException \UnexpectedValueException
     */
    public function createConstraintFromEmptyConstraintsArrayThrownException()
    {
        $mockedQuery = $this->createMock(QueryInterface::class);
        $mockedRepository = $this->getAccessibleMock(
            ProductRepository::class,
            ['dummy'],
            [],
            '',
            false
        );

        $mockedRepository->_call('createConstraintFromConstraintsArray', $mockedQuery, [], 'or');
    }

    /**
     * @test
     */
    public function createConstraintForOneConstraintWillReturnThisConstraint()
    {
        $mockedQuery = $this->createMock(QueryInterface::class);
        $mockedRepository = $this->getAccessibleMock(
            ProductRepository::class,
            ['dummy'],
            [],
            '',
            false
        );
        $constraints = [
            $this->createMock(ConstraintInterface::class)
        ];

        $this->assertSame(
            $constraints[0],
            $mockedRepository->_call('createConstraintFromConstraintsArray', $mockedQuery, $constraints, 'or')
        );
    }

    /**
     * @test
     */
    public function canSetOrderOnlyIfAllowed()
    {
        $mockedQuery = $this->getMockBuilder(Query::class)
            ->setMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockedRepository = $this->getAccessibleMock(
            ProductRepository::class,
            ['dummy'],
            [],
            '',
            false
        );

        $demand = new Demand();
        $demand->setOrderByAllowed('name,title');
        $demand->setOrderBy('test');

        $mockedRepository->_call('setOrderings', $mockedQuery, $demand);

        $this->assertEquals(
            [
                'name' => QueryInterface::ORDER_ASCENDING
            ],
            $mockedQuery->getOrderings()
        );
    }

    /**
     * @test
     */
    public function canSetOrderByByAllowedFields()
    {
        $mockedQuery = $this->getMockBuilder(Query::class)
            ->setMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockedRepository = $this->getAccessibleMock(
            ProductRepository::class,
            ['dummy'],
            [],
            '',
            false
        );

        $orderDirection = QueryInterface::ORDER_DESCENDING;

        $demand = new Demand();
        $demand->setOrderByAllowed('name,title');
        $demand->setOrderBy('title');
        $demand->setOrderDirection($orderDirection);

        $mockedRepository->_call('setOrderings', $mockedQuery, $demand);
        $this->assertEquals(
            [
                'title' => QueryInterface::ORDER_DESCENDING,
                'name' => QueryInterface::ORDER_ASCENDING
            ],
            $mockedQuery->getOrderings()
        );
    }

    /**
     * @test
     */
    public function canSetOrderByByAllowedFieldsAddsNameWhenNotName()
    {
        $mockedQuery = $this->getMockBuilder(Query::class)
            ->setMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockedRepository = $this->getAccessibleMock(
            ProductRepository::class,
            ['dummy'],
            [],
            '',
            false
        );

        $orderDirection = QueryInterface::ORDER_DESCENDING;

        $demand = new Demand();
        $demand->setOrderByAllowed('custom_sorting,title');
        $demand->setOrderBy('custom_sorting');
        $demand->setOrderDirection($orderDirection);

        $mockedRepository->_call('setOrderings', $mockedQuery, $demand);

        $this->assertEquals(
            [
                'custom_sorting' => QueryInterface::ORDER_DESCENDING,
                'name' => QueryInterface::ORDER_ASCENDING
            ],
            $mockedQuery->getOrderings()
        );
    }

    /**
     * @test
     */
    public function setOrderChangesTheCategoryOrderingsNameInCaseOfCategoryOrdering()
    {
        $mockedQuery = $this->getMockBuilder(Query::class)
            ->setMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockedRepository = $this->getAccessibleMock(
            ProductRepository::class,
            ['dummy'],
            [],
            '',
            false
        );

        $orderDirection = QueryInterface::ORDER_DESCENDING;

        $demand = new Demand();
        $demand->setOrderByAllowed('categories.sorting');
        $demand->setOrderBy('categories');
        $demand->setOrderDirection($orderDirection);

        $mockedRepository->_call('setOrderings', $mockedQuery, $demand);

        $this->assertEquals(
            [
                'categories.sorting' => QueryInterface::ORDER_DESCENDING
            ],
            $mockedQuery->getOrderings()
        );
    }
}
