<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Image;
use TYPO3\CMS\Core\Resource\FileReference;

class ImageTest extends UnitTestCase
{
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getMockBuilder(Image::class)->disableOriginalConstructor()->setMethods(['getOriginalResource'])->getMock();
    }

    /**
     * @test
     */
    public function getTitleWithEmptyTitleReturnTitleOfOriginalResource(): void
    {
        $title = 'test';

        $this->inject($this->subject, 'title', '');

        $fileReference = $this->prophesize(FileReference::class);
        $fileReference->getTitle()->shouldBeCalled()->willReturn($title);

        $this->subject->expects(self::once())->method('getOriginalResource')->willReturn($fileReference->reveal());

        self::assertEquals($title, $this->subject->getTitle());
    }

    /**
     * @test
     */
    public function getDescriptionWithEmptyDescriptionReturnDescriptionOfOriginalResource(): void
    {
        $description = 'testdescription';

        $this->inject($this->subject, 'description', '');

        $fileReference = $this->prophesize(FileReference::class);
        $fileReference->getDescription()->shouldBeCalled()->willReturn($description);

        $this->subject->expects(self::once())->method('getOriginalResource')->willReturn($fileReference->reveal());

        self::assertEquals($description, $this->subject->getDescription());
    }

    /**
     * @test
     */
    public function getAlternativeWithEmptyAlternativeReturnAlternativeOfOriginalResource(): void
    {
        $value = 'test';

        $this->inject($this->subject, 'alternative', '');

        $fileReference = $this->prophesize(FileReference::class);
        $fileReference->getAlternative()->shouldBeCalled()->willReturn($value);

        $this->subject->expects(self::once())->method('getOriginalResource')->willReturn($fileReference->reveal());

        self::assertEquals($value, $this->subject->getAlternative());
    }
}
