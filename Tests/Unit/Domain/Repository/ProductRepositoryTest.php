<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Repository;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\DTO\Demand;
use Pixelant\PxaProductManager\Domain\Model\Filter;
use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
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
        $filters = [
            [
                'value' => ['test', 'test2'],
                'type' => Filter::TYPE_CATEGORIES
            ],
            [
                'value' => [2],
                'attributeUid' => 5,
                'type' => Filter::TYPE_ATTRIBUTES
            ]
        ];

        $mockedQuery = $this->createMock(QueryInterface::class);

        $mockedRepository = $this->getAccessibleMock(
            ProductRepository::class,
            ['createConstraintFromConstraintsArray'],
            [],
            '',
            false
        );

        $mockedRepository
            ->expects($this->exactly(3))
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
