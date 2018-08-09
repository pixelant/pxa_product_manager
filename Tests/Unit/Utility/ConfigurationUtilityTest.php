<?php


namespace Pixelant\PxaProductManager\Tests\Utility;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Configuration\ConfigurationManager;
use Pixelant\PxaProductManager\Utility\ConfigurationUtility;

/**
 * Class ConfigurationUtilityTest
 * @package Pixelant\PxaProductManager\Tests\Utility
 */
class ConfigurationUtilityTest extends UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        $reflectionClass = new \ReflectionClass(ConfigurationUtility::class);

        $reflectionProperty = $reflectionClass->getProperty('config');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue([
            'FE' => $this->getExtensionSettings()
        ]);

        $reflectionProperty = $reflectionClass->getProperty('extMgrConfiguration');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->getExtensionConfiguration());
    }

    /**
     * @test
     */
    public function getExtMgrConfigurationReturnConfigration()
    {
        $this->assertEquals(
            $this->getExtensionConfiguration(),
            ConfigurationUtility::getExtMgrConfiguration()
        );
    }

    /**
     * @test
     */
    public function getExtSettingsReturnSettings()
    {
        $reflectionClass = new \ReflectionClass(ConfigurationUtility::class);

        $mockedConfigurationManager = $this->createPartialMock(ConfigurationManager::class, ['isEnvironmentInFrontendMode']);
        $mockedConfigurationManager
            ->expects($this->atLeastOnce())
            ->method('isEnvironmentInFrontendMode')
            ->willReturn(true);

        $reflectionProperty = $reflectionClass->getProperty('configurationManager');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($mockedConfigurationManager);

        $this->assertEquals(
            $this->getExtensionSettings()['settings'],
            ConfigurationUtility::getSettings()
        );
    }

    /**
     * @test
     */
    public function readFromExtConfigurationByPathReturnValueOfSetting()
    {
        $path = 'enablePrices';

        $this->assertEquals(
            1,
            ConfigurationUtility::getExtManagerConfigurationByPath($path)
        );
    }

    /**
     * @test
     */
    public function readFromExtConfigurationByPathRecursiveReturnValueOfSetting()
    {
        $path = 'deepSettings/test';

        $this->assertEquals(
            123,
            ConfigurationUtility::getExtManagerConfigurationByPath($path)
        );
    }

    /**
     * @test
     */
    public function readFromSettingsByPathReturnValueOfSetting()
    {
        $reflectionClass = new \ReflectionClass(ConfigurationUtility::class);

        $mockedConfigurationManager = $this->createPartialMock(ConfigurationManager::class, ['isEnvironmentInFrontendMode']);
        $mockedConfigurationManager
            ->expects($this->atLeastOnce())
            ->method('isEnvironmentInFrontendMode')
            ->willReturn(true);

        $reflectionProperty = $reflectionClass->getProperty('configurationManager');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($mockedConfigurationManager);

        $path = 'test';

        $this->assertEquals(
            123,
            ConfigurationUtility::getSettingsByPath($path)
        );
    }

    /**
     * @test
     */
    public function readFromSettingsByPathNonExistingValueReturnNull()
    {
        $reflectionClass = new \ReflectionClass(ConfigurationUtility::class);

        $mockedConfigurationManager = $this->createPartialMock(ConfigurationManager::class, ['isEnvironmentInFrontendMode']);
        $mockedConfigurationManager
            ->expects($this->atLeastOnce())
            ->method('isEnvironmentInFrontendMode')
            ->willReturn(true);

        $reflectionProperty = $reflectionClass->getProperty('configurationManager');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($mockedConfigurationManager);

        $path = 'blabla';

        $this->assertNull(
            ConfigurationUtility::getSettingsByPath($path)
        );
    }

    /**
     * @test
     */
    public function readFromSettingsByPathRecursiveReturnValueOfSetting()
    {
        $reflectionClass = new \ReflectionClass(ConfigurationUtility::class);

        $mockedConfigurationManager = $this->createPartialMock(ConfigurationManager::class, ['isEnvironmentInFrontendMode']);
        $mockedConfigurationManager
            ->expects($this->atLeastOnce())
            ->method('isEnvironmentInFrontendMode')
            ->willReturn(true);

        $reflectionProperty = $reflectionClass->getProperty('configurationManager');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($mockedConfigurationManager);

        $path = 'level1/test';

        $this->assertEquals(
            'testing',
            ConfigurationUtility::getSettingsByPath($path)
        );

        $path = 'level1/levle2/setting';

        $this->assertEquals(
            'value',
            ConfigurationUtility::getSettingsByPath($path)
        );
    }

    /**
     * Test settings
     *
     * @return array
     */
    protected function getExtensionSettings()
    {
        return [
            'settings' => [
                'test' => 123,
                'level1' => [
                    'test' => 'testing',
                    'levle2' => [
                        'setting' => 'value'
                    ]
                ]
            ]
        ];
    }

    /**
     * Test configuration
     *
     * @return array
     */
    protected function getExtensionConfiguration()
    {
        return [
            'enablePrices' => 1,
            'test' => 'value',
            'deepSettings' => [
                'test' => 123
            ]
        ];
    }
}
