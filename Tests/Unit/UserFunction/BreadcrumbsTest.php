<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\UserFunction;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\UserFunction\Breadcrumbs;
use TYPO3\CMS\Core\Http\ServerRequest;

class BreadcrumbsTest extends UnitTestCase
{
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this
            ->getMockBuilder(Breadcrumbs::class)
            ->disableOriginalConstructor()
            ->setMethods(['url'])
            ->getMock();
    }

    /**
     * @test
     */
    public function hasBreadcrumbsReturnTruIfGetParametersExist(): void
    {
        $request = $this->prophesize(ServerRequest::class);
        $request->getQueryParams()->shouldBeCalled()->willReturn(['tx_pxaproductmanager_productshow' => '12']);

        $this->inject($this->subject, 'request', $request->reveal());

        self::assertTrue($this->callInaccessibleMethod($this->subject, 'hasBreadcrumbs'));
    }

    /**
     * @test
     */
    public function hasBreadcrumbsReturnFalseIfNoParams(): void
    {
        $request = $this->prophesize(ServerRequest::class);
        $request->getQueryParams()->shouldBeCalled()->willReturn([]);

        $this->inject($this->subject, 'request', $request->reveal());

        self::assertFalse($this->callInaccessibleMethod($this->subject, 'hasBreadcrumbs'));
    }

    /**
     * @test
     */
    public function getArgumentsReturnArguments(): void
    {
        $arguments = [
            'tx_pxaproductmanager_productshow' => [
                'action' => 'show',
                'controller' => 'ProductShow',
                'product' => '1',
            ],
        ];

        $request = $this->prophesize(ServerRequest::class);
        $request->getQueryParams()->shouldBeCalled()->willReturn($arguments);

        $this->inject($this->subject, 'request', $request->reveal());

        $this->callInaccessibleMethod($this->subject, 'hasBreadcrumbs');

        self::assertEquals(
            $arguments['tx_pxaproductmanager_productshow'],
            $this->callInaccessibleMethod($this->subject, 'getArguments')
        );
    }
}
