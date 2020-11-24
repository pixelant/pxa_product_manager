<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Resource;

use Pixelant\PxaProductManager\Configuration\Site\SettingsReader;
use Pixelant\PxaProductManager\Service\Url\UrlBuilderServiceInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Service\ImageService;

class Product extends AbstractResource
{
    /**
     * @var ConfigurationManagerInterface
     */
    protected ConfigurationManagerInterface $configurationManager;

    /**
     * @var ImageService
     */
    protected ImageService $imageService;

    /**
     * @var UrlBuilderServiceInterface
     */
    protected UrlBuilderServiceInterface $urlBuilderService;

    /**
     * @var SettingsReader
     */
    protected SettingsReader $siteConfiguration;

    /**
     * Plugin TS settings.
     *
     * @var array
     */
    protected array $settings;

    /**
     * @param ConfigurationManagerInterface $configurationManager
     */
    public function injectConfigurationManagerInterface(ConfigurationManagerInterface $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param ImageService $imageService
     */
    public function injectImageService(ImageService $imageService): void
    {
        $this->imageService = $imageService;
    }

    /**
     * @param UrlBuilderServiceInterface $urlBuilderServiceInterface
     */
    public function injectUrlBuilderServiceInterface(UrlBuilderServiceInterface $urlBuilderServiceInterface): void
    {
        $this->urlBuilderService = $urlBuilderServiceInterface;
    }

    /**
     * @param SettingsReader $settingsReader
     */
    public function injectSettingsReader(SettingsReader $settingsReader): void
    {
        $this->siteConfiguration = $settingsReader;
    }

    /**
     * @param array|null $additional
     * @return array
     */
    public function extractProperties(array $additional = null): array
    {
        $this->settings = $this->getPluginSettings();

        $resource = [
            'listImage' => $this->getProcessedImageUri($this->entity->getListImage()),
            'url' => $this->getUrl(),
        ];

        return parent::extractProperties($resource + ($additional ?? []));
    }

    /**
     * Product url.
     *
     * @return string
     */
    protected function getUrl(): string
    {
        $tsPid = $this->settings['pids']['singleViewPid'] ?? 0;
        $pageUid = (int) ($tsPid ?: $this->siteConfiguration->getValue('singleViewPid') ?: 0);

        return $this->urlBuilderService->url(
            $this->entity
        );
    }

    /**
     * Return uri of image with listing processing instructions.
     *
     * @param FileReference|null $reference
     * @param array $settings
     * @return string|null
     */
    protected function getProcessedImageUri(?FileReference $reference): ?string
    {
        if ($reference === null) {
            return null;
        }

        $processingInstructions = $this->settings['listView']['images'];

        $processedImage = $this->imageService->applyProcessingInstructions(
            $reference->getOriginalResource(),
            $processingInstructions
        );

        return $this->imageService->getImageUri($processedImage);
    }

    /**
     * {@inheritdoc}
     */
    protected function extractableProperties(): array
    {
        return [
            'uid',
            'name',
            'sku',
            'price',
            'formattedPrice',
        ];
    }

    /**
     * Plugin settings.
     *
     * @return array
     */
    protected function getPluginSettings(): array
    {
        return $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'PxaProductManager'
        );
    }
}
