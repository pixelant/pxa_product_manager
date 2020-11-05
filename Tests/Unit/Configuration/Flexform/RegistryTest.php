<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Configuration\Flexform;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Configuration\Flexform\Registry;

class RegistryTest extends UnitTestCase
{
    /**
     * @var Registry
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['TYPO3_CONF_VARS']['EXT']['pxa_product_manager']['switchableControllerActions']['items'] = [];
        $this->subject = new Registry();
    }

    /**
     * @test
     */
    public function registerDefaultActionWillRegisterDefaultActions(): void
    {
        $this->subject->registerDefaultActions();

        self::assertCount(count(getProtectedVarValue($this->subject, 'defaultSwitchableActions')), $GLOBALS['TYPO3_CONF_VARS']['EXT']['pxa_product_manager']['switchableControllerActions']['items']);
    }

    /**
     * @test
     */
    public function addSwitchableControllerActionAddsConfigurationtoGlobalArray(): void
    {
        $action = 'Product->test';
        $label = 'Test';
        $flexforms = ['EXT:test.xml'];
        $exclude = ['test'];

        $this->subject->addSwitchableControllerAction($action, $label, $flexforms, $exclude);

        $expect = [
            'action' => $action,
            'label' => $label,
            'flexforms' => $flexforms,
            'excludeFields' => $exclude,
        ];

        $result = $GLOBALS['TYPO3_CONF_VARS']['EXT']['pxa_product_manager']['switchableControllerActions']['items'][$action];

        self::assertEquals($expect, $result);
    }

    /**
     * @test
     */
    public function removeSwitchableControllerActionRemoveByActionName(): void
    {
        $action = 'Product->test';
        $label = 'Test';
        $flexforms = ['EXT:test.xml'];
        $exclude = ['test'];

        $GLOBALS['TYPO3_CONF_VARS']['EXT']['pxa_product_manager']['switchable_controller_actions']['items'][$action] = [
            'action' => $action,
            'label' => $label,
            'flexforms' => $flexforms,
            'excludeFields' => $exclude,
        ];

        $this->subject->removeSwitchableControllerAction($action);

        self::assertEmpty($GLOBALS['TYPO3_CONF_VARS']['EXT']['pxa_product_manager']['switchableControllerActions']['items']);
    }

    /**
     * @test
     */
    public function getAllRegisteredActionsReturnAllItems(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['pxa_product_manager']['switchableControllerActions']['items']['test1'] = ['action' => 'test1'];
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['pxa_product_manager']['switchableControllerActions']['items']['test2'] = ['action' => 'test2'];

        $expect = [
            'test1' => [
                'action' => 'test1',
            ],
            'test2' => [
                'action' => 'test2',
            ],
        ];

        self::assertEquals($expect, $this->subject->getAllRegisteredActions());
    }

    /**
     * @test
     */
    public function getSwitchableControllerActionConfigurationReturnNullIfConfigurationDoesnotExist(): void
    {
        self::assertNull($this->subject->getSwitchableControllerActionConfiguration('test'));
    }

    /**
     * @test
     */
    public function getSwitchableControllerActionConfigurationReturnConfigurationIfExist(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['pxa_product_manager']['switchableControllerActions']['items']['test1'] = ['action' => 'test1'];

        $expect = [
            'action' => 'test1',
        ];

        self::assertEquals($expect, $this->subject->getSwitchableControllerActionConfiguration('test1'));
    }
}
