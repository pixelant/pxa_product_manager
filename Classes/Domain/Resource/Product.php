<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Resource;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Service\ImageService;

/**
 * @package Pixelant\PxaProductManager\Domain\Resource
 */
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
     * @param ConfigurationManagerInterface $configurationManager
     */
    public function injectConfigurationManagerInterface(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param ImageService $imageService
     */
    public function injectImageService(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = parent::toArray();
        $array['listImage'] = $this->getProcessedImageUri($this->entity->getListImage());

        return $array;
    }

    /**
     * Return uri of image with listing processing instructions
     *
     * @param FileReference|null $reference
     * @return string|null
     */
    protected function getProcessedImageUri(?FileReference $reference): ?string
    {
        if ($reference === null) {
            return null;
        }

        $processingInstructions = $this->getPluginSettings()['listView']['images'];
        //$array['mainImage']=
        $processedImage = $this->imageService->applyProcessingInstructions(
            $reference->getOriginalResource(),
            $processingInstructions
        );

        return $this->imageService->getImageUri($processedImage);
    }

    /**
     * @inheritDoc
     */
    protected function extractableProperties(): array
    {
        return [
            'uid',
            'name',
            'sku',
        ];
    }

    /**
     * Plugin settings
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
