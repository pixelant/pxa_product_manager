<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Image;
use TYPO3\CMS\Core\Resource\FileReference;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\Model
 */
class ImageTest extends UnitTestCase
{
    protected $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->subject = $this->getMockBuilder(Image::class)->disableOriginalConstructor()->setMethods(['getOriginalResource'])->getMock();
    }

    /**
     * @test
     */
    public function getTitleWithEmptyTitleReturnTitleOfOriginalResource()
    {
        $title = 'test';

        $this->inject($this->subject, 'title', '');

        $fileReference = $this->prophesize(FileReference::class);
        $fileReference->getTitle()->shouldBeCalled()->willReturn($title);

        $this->subject->expects($this->once())->method('getOriginalResource')->willReturn($fileReference->reveal());

        $this->assertEquals($title, $this->subject->getTitle());
    }

    /**
     * @test
     */
    public function getDescriptionWithEmptyDescriptionReturnDescriptionOfOriginalResource()
    {
        $description = 'testdescription';

        $this->inject($this->subject, 'description', '');

        $fileReference = $this->prophesize(FileReference::class);
        $fileReference->getDescription()->shouldBeCalled()->willReturn($description);

        $this->subject->expects($this->once())->method('getOriginalResource')->willReturn($fileReference->reveal());

        $this->assertEquals($description, $this->subject->getDescription());
    }
}
