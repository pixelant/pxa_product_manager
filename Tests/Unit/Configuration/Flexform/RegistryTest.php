<?php
declare(strict_types=1);
namespace Pixelant\PxaProductManager\Tests\Unit\Configuration\Flexform;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Configuration\Flexform\Registry;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Service\Flexform
 */
class RegistryTest extends UnitTestCase
{
    /**
     * @var Registry
     */
    protected $subject;

    protected function setUp()
    {
        parent::setUp();

        $GLOBALS['TYPO3_CONF_VARS']['EXT']['pxa_product_manager']['switchableControllerActions']['items'] = [];
        $this->subject = new Registry();
    }

    /**
     * @test
     */
    public function registerDefaultActionWillRegisterDefaultActions()
    {
        $this->subject->registerDefaultActions();

        $this->assertCount(count(getProtectedVarValue($this->subject, 'defaultSwitchableActions')), $GLOBALS['TYPO3_CONF_VARS']['EXT']['pxa_product_manager']['switchableControllerActions']['items']);
    }

    /**
     * @test
     */
    public function addSwitchableControllerActionAddsConfigurationtoGlobalArray()
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

        $this->assertEquals($expect, $result);
    }

    /**
     * @test
     */
    public function removeSwitchableControllerActionRemoveByActionName()
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

        $this->assertEmpty($GLOBALS['TYPO3_CONF_VARS']['EXT']['pxa_product_manager']['switchableControllerActions']['items']);
    }

    /**
     * @test
     */
    public function getAllRegisteredActionsReturnAllItems()
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

        $this->assertEquals($expect, $this->subject->getAllRegisteredActions());
    }

    /**
     * @test
     */
    public function getSwitchableControllerActionConfigurationReturnNullIfConfigurationDoesnotExist()
    {
        $this->assertNull($this->subject->getSwitchableControllerActionConfiguration('test'));
    }

    /**
     * @test
     */
    public function getSwitchableControllerActionConfigurationReturnConfigurationIfExist()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['pxa_product_manager']['switchableControllerActions']['items']['test1'] = ['action' => 'test1'];

        $expect = [
            'action' => 'test1',
        ];

        $this->assertEquals($expect, $this->subject->getSwitchableControllerActionConfiguration('test1'));
    }
}
