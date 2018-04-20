<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service;

use Pixelant\PxaProductManager\Exception\OrderEmailException;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class AbstractMailService
 * @package Pixelant\PxaProductManager\Service
 */
class AbstractMailService
{
    const FORMAT_PLAINTEXT = 'plaintext';

    const FORMAT_HTML = 'html';

    /**
     * @var MailMessage
     */
    protected $mailMessage = null;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * Plugin full settings
     *
     * @var array
     */
    protected $pluginSettings = [];

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
     * @var string
     */
    protected $subject = '';

    /**
     * Message
     *
     * @var string
     */
    protected $message = '';

    /**
     * Initialize constructor
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->mailMessage = GeneralUtility::makeInstance(MailMessage::class);

        $this->initPluginSettings();
    }

    /**
     * Send email
     *
     * @param string $format
     * @return bool
     * @throws OrderEmailException
     */
    public function send(string $format = 'html'): bool
    {
        if (empty($this->subject)) {
            throw new OrderEmailException('The option "subject" must be set for the order email.', 1524228991718);
        }
        if (empty($this->receivers)) {
            throw new OrderEmailException('The option "receivers" must be set for the order email.', 1524229123854);
        }
        if (empty($this->senderEmail)) {
            throw new OrderEmailException('The option "senderEmail" must be set for the order email.', 1524229127393);
        }

        $this->mailMessage
            ->setSubject($this->subject)
            ->setFrom([$this->senderEmail => $this->senderName])
            ->setTo($this->receivers);

        if ($format === self::FORMAT_PLAINTEXT) {
            $this->mailMessage->setBody($this->message, 'text/plain');
        } else {
            $this->mailMessage->setBody($this->message, 'text/html');
        }

        return $this->send();
    }


    /**
     * Generate email body
     *
     * @param array $variables
     * @return mixed
     */
    abstract public function generateMailBody(...$variables);

    /**
     * @return MailMessage
     */
    public function getMailMessage(): MailMessage
    {
        return $this->mailMessage;
    }

    /**
     * @param MailMessage $mailMessage
     */
    public function setMailMessage(MailMessage $mailMessage)
    {
        $this->mailMessage = $mailMessage;
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager(): ObjectManager
    {
        return $this->objectManager;
    }

    /**
     * @param ObjectManager $objectManager
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return array
     */
    public function getPluginSettings(): array
    {
        return $this->pluginSettings;
    }

    /**
     * @param array $pluginSettings
     */
    public function setPluginSettings(array $pluginSettings)
    {
        $this->pluginSettings = $pluginSettings;
    }

    /**
     * @return string
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }

    /**
     * @param string $senderName
     */
    public function setSenderName(string $senderName)
    {
        $this->senderName = $senderName;
    }

    /**
     * @return string
     */
    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    /**
     * @param string $senderEmail
     */
    public function setSenderEmail(string $senderEmail)
    {
        $this->senderEmail = $senderEmail;
    }

    /**
     * @return array
     */
    public function getReceivers(): array
    {
        return $this->receivers;
    }

    /**
     * @param array $receivers
     */
    public function setReceivers(array $receivers)
    {
        $this->receivers = $receivers;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
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

    /**
     * Initialize view
     *
     * @param string $templatePathAndFilename
     * @param string $templateName
     * @return StandaloneView
     * @throws OrderEmailException
     */
    protected function initializeStandaloneView(
        string $templatePathAndFilename = '',
        string $templateName = ''
    ): StandaloneView {
        /** @var StandaloneView $standaloneView */
        $standaloneView = $this->objectManager->get(StandaloneView::class);

        if (empty($templatePathAndFilename) && empty($templateName)) {
            throw new OrderEmailException(
                'Either "$templatePathAndFilename" or "$templateName" must be set for the order email.',
                1524229784912
            );
        }

        if (!empty($templatePathAndFilename)) {
            $standaloneView->setTemplatePathAndFilename($templatePathAndFilename);
        } else {
            $standaloneView->setTemplate($templateName);
        }

        $view = $this->pluginSettings['view'];
        if (isset($view['templateRootPaths']) && is_array($view['templateRootPaths'])) {
            $standaloneView->setTemplateRootPaths($view['templateRootPaths']);
        }

        if (isset($view['partialRootPaths']) && is_array($view['partialRootPaths'])) {
            $standaloneView->setPartialRootPaths($view['partialRootPaths']);
        }

        if (isset($view['layoutRootPaths']) && is_array($view['layoutRootPaths'])) {
            $standaloneView->setLayoutRootPaths($view['layoutRootPaths']);
        }

        $standaloneView->assign('settings', $this->pluginSettings['settings']);

        return $standaloneView;
    }
}
