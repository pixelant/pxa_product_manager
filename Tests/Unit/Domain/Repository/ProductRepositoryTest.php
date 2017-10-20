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
    public function createConstraintsWithEmptyDemandWontCallMatching()
    {
        $mockedQuery = $this->createMock(QueryInterface::class);
        $mockedRepository = $this->getAccessibleMock(
            ProductRepository::class,
            ['createConstraintFromConstraintsArray'],
            [],
            '',
            false
        );

        $mockedRepository
            ->expects($this->never())
            ->method('createConstraintFromConstraintsArray');

        $demand = new Demand();

        $mockedRepository->_call('createConstraints', $mockedQuery, $demand);
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
            ['createConstraintFromConstraintsArray', 'createFilteringConstraints', 'createCategoryConstraints'],
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
            ->method('createConstraintFromConstraintsArray');


        $mockedRepository->_call('createConstraints', $mockedQuery, $demand);
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
        $mockedAttributeValueRepository = $this->createPartialMock(
            AttributeValueRepository::class,
            ['findAttributeValuesByAttributeAndValue']
        );

        $mockedRepository = $this->getAccessibleMock(
            ProductRepository::class,
            ['createConstraintFromConstraintsArray'],
            [],
            '',
            false
        );
        $mockedRepository->_set('attributeValueRepository', $mockedAttributeValueRepository);

        $mockedAttributeValueRepository
            ->expects($this->once())
            ->method('findAttributeValuesByAttributeAndValue')
            ->with(
                5, // uid of attribute filter
                2, // value of filter
                true // raw result
            )
            ->willReturn([['uid' => 123]]);

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
            ->setMethods(['setOrderings'])
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

        $mockedQuery
            ->expects($this->never())
            ->method('setOrderings');

        $mockedRepository->_call('setOrderings', $mockedQuery, $demand);
    }

    /**
     * @test
     */
    public function canSetOrderByByAllowedFields()
    {
        $mockedQuery = $this->getMockBuilder(Query::class)
            ->setMethods(['setOrderings'])
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

        $mockedQuery
            ->expects($this->once())
            ->method('setOrderings')
            ->with(['title' => $orderDirection]);

        $mockedRepository->_call('setOrderings', $mockedQuery, $demand);
    }
}
