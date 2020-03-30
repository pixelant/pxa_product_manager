<?php
declare(strict_types=1);
namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Resource;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Filter;
use Pixelant\PxaProductManager\Domain\Resource\Filter as FilterResource;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * This is test for abstract class
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\Model
 */
class FilterTest extends UnitTestCase
{
    /**
     * @test
     */
    public function toArrayWillReturnEntityAsArray()
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

        $filter = new class extends Filter {
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
        $subject->injectDispatcher($this->createMock(Dispatcher::class));

        $this->assertEquals($expect, $subject->toArray());
    }
}
