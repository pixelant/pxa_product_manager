<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Resource;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Filter;
use Pixelant\PxaProductManager\Domain\Resource\Filter as FilterResource;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

/**
 * This is test for abstract class.
 */
class FilterTest extends UnitTestCase
{
    /**
     * @test
     */
    public function toArrayWillReturnEntityAsArray(): void
    {
        $expect = [
            'uid' => 15,
            'name' => 'name',
            'label' => 'label',
            'type' => 1,
            'options' => ['123'],
            'attributeUid' => 1,
            'conjunction' => 'or',
        ];

        /** @codingStandardsIgnoreStart */
        $filter = new class() extends Filter {
            /** @codingStandardsIgnoreEnd */
            public function getOptions(): array
            {
                return ['123'];
            }

            public function getAttributeUid(): int
            {
                return 1;
            }
        };

        $filter->_setProperty('uid', 15);
        $filter
            ->setType(1)
            ->setConjunction('or')
            ->setName('name')
            ->setLabel('label');

        $subject = new FilterResource($filter);
        $subject->injectDispatcher($this->createMock(EventDispatcher::class));

        self::assertEquals($expect, $subject->toArray());
    }

    /**
     * @test
     */
    public function convertPropertyValueReturnArrayIfObjectStorage(): void
    {
        $storage = TestsUtility::createObjectStorage(...TestsUtility::createMultipleEntities(Filter::class, 3));

        $subject = $this
            ->getMockBuilder(FilterResource::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        self::assertIsArray($this->callInaccessibleMethod($subject, 'convertPropertyValue', $storage));
    }
}
