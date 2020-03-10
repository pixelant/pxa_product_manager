<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Service;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\DTO\CategoryDemand;
use Pixelant\PxaProductManager\Service\NavigationService;
use TYPO3\CMS\Core\Http\ServerRequest;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Service
 */
class NavigationServiceTest extends UnitTestCase
{
    protected $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->subject = $this->getMockBuilder(NavigationService::class)->disableOriginalConstructor()->setMethods(null)->getMock();
    }

    /**
     * @test
     */
    public function getDemandWithParentCloneOriginalDemandAndSetParentCatgory()
    {
        $parent = createEntity(Category::class, 1);
        $demand = new CategoryDemand();
        $demand->setLimit(200);
        $demand->setParent(createEntity(Category::class, 199));

        $this->inject($this->subject, 'demandPrototype', $demand);
        $newDemand = $this->callInaccessibleMethod($this->subject, 'getDemandWithParent', $parent);

        $this->assertSame($parent, $newDemand->getParent());
        $this->assertEquals(200, $newDemand->getLimit());
    }

    /**
     * @test
     */
    public function setActiveFromRequestWillSetActiveCategoriesAndCurrentFromRequest()
    {
        $params = [
            'category' => '14',
            'category_0' => '10',
            'category_1' => '100',
        ];

        $request = $this->prophesize(ServerRequest::class);
        $request->getQueryParams()->shouldBeCalled()->willReturn(['tx_pxaproductmanager_pi1' => $params]);

        $this->inject($this->subject, 'request', $request->reveal());

        $this->callInaccessibleMethod($this->subject, 'setActiveFromRequest');

        $this->assertTrue(14 === getProtectedVarValue($this->subject, 'current'));
        $this->assertEquals(['category_0' => 10, 'category_1' => 100], getProtectedVarValue($this->subject, 'active'));
    }
}
