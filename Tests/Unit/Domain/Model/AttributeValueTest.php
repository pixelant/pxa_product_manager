<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;

class AttributeValueTest extends UnitTestCase
{
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new AttributeValue();
    }

    /**
     * @test
     */
    public function usingAsStringReturnValue(): void
    {
        $this->subject->setStringValue('value');

        self::assertEquals('value', (string)$this->subject->getStringValue());
    }
}
