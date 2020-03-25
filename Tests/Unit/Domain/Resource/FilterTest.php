<?php

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
        $uid = 10;
        $name = 'test name';
        $label = 'test label';

        $fields = ['uid', 'name', 'label'];
        $filter = createEntity(Filter::class, compact('uid', 'name', 'label'));

        $subject = new FilterResource($filter);
        $subject->injectDispatcher($this->createMock(Dispatcher::class));
        $this->inject($subject, 'extractableProperties', $fields);

        $expect = [
            'uid' => $uid,
            'name' => $name,
            'label' => $label,
        ];

        $this->assertEquals($expect, $subject->toArray());
    }
}
