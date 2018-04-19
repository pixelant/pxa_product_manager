<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MailUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class AbstractMailService
 * @package Pixelant\PxaProductManager\Service
 */
class AbstractMailService
{
    /**
     * @var MailUtility
     */
    protected $mailUtility = null;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var string
     */
    protected $senderName = '';

    /**
     * @var string
     */
    protected $senderEmail = '';

    /**
     * @var array
     */
    protected $receivers = [];

    /**
     * Plugin full settings
     *
     * @var array
     */
    protected $pluginSettings = [];

    /**
     * Initalize constructor
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->mailUtility = GeneralUtility::makeInstance(MailUtility::class);

        $this->initPluginSettings();
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->pluginSettings,'Debug',16);
        die;
    }

    /**
     * Init settings
     */
    protected function initPluginSettings()
    {
        /** @var ConfigurationManagerInterface $configurationManager */
        $configurationManager = $this->objectManager->get(ConfigurationManagerInterface::class);
        $this->pluginSettings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
    }
}
