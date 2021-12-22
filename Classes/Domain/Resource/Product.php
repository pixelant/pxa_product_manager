<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Resource;

use Pixelant\PxaProductManager\Configuration\Site\SettingsReader;
use Pixelant\PxaProductManager\Service\Url\UrlBuilderServiceInterface;
use Pixelant\PxaProductManager\Utility\AttributeUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
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
            'listImage' => $this->getProductListImages($this->entity),
            'url' => $this->getUrl(),
        ];

        // Add additional fields to result.
        $additionalFields = $this->settings['listView']['additionalFields'] ?? false;
        if (!empty($additionalFields)) {
            $additionalFields = GeneralUtility::underscoredToLowerCamelCase($additionalFields);
            $additionalFieldsList = GeneralUtility::trimExplode(',', $additionalFields, true);
            $additional = $this->getAdditionalFieldsValues($additionalFieldsList);
        }

        // Add additional attributes to result.
        $additionalAttributes = $this->settings['listView']['additionalAttributes'] ?? false;
        if (!empty($additionalAttributes)) {
            $additionalAttributesList = GeneralUtility::trimExplode(',', $additionalAttributes, true);
            foreach ($additionalAttributesList as $attributeIdentifier) {
                foreach ($this->entity->getAttributes() as $attribute) {
                    if ($attribute->getIdentifier() === $attributeIdentifier) {
                        $attributeValue = AttributeUtility::findAttributeValue(
                            $this->entity->getUid(),
                            $attribute->getUid()
                        );

                        $resource[$attributeIdentifier] = [
                            'label' => $attribute->getLabel() ?? $attribute->getName(),
                            'data' => AttributeUtility::getAttributeValueRenderValue(
                                $attributeValue['uid']
                            ),
                        ];
                    }
                }
            }
        }

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
     * @param FileReference[] $references
     * @return array
     */
    protected function getProcessedImagesUri(array $references): array
    {
        $processedReferences = [];

        foreach ($references as $reference) {
            $processedReferences[] = $this->getProcessedImageUri($reference);
        }

        return $processedReferences;
    }

    /**
     * Return product list images.
     *
     * @return array|string|null
     */
    protected function getProductListImages()
    {
        /** @codingStandardsIgnoreStart */
        $listImages = ($this->settings['listView']['fetchAllImagesAsListImage'])
            ? $this->entity->getImagesAsArray()
            : $this->entity->getListImage();
        // @codingStandardsIgnoreEnd

        if (is_array($listImages)) {
            $listImages = $this->getProcessedImagesUri($listImages);
        } else {
            $listImages = $this->getProcessedImageUri($listImages);
        }

        return $listImages;
    }

    /**
     * Return additional fields values.
     *
     * @param array $additionalFields
     * @return array
     * @throws \TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException
     */
    protected function getAdditionalFieldsValues(array $additionalFields): array
    {
        $values = [];

        foreach ($additionalFields as $additionalField) {
            $values[$additionalField] = $this->convertPropertyValue(
                ObjectAccess::getProperty($this->entity, $additionalField)
            );
        }

        return $values;
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
            'teaser',
            'productType',
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
