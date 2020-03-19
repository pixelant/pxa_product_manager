<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service;

use Pixelant\PxaProductManager\Configuration\Site\SettingsReader;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Service\Url\UrlBuilderServiceInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Typolink\AbstractTypolinkBuilder;

/**
 * @package Pixelant\PxaProductManager\Service
 */
class TypolinkBuilderService extends AbstractTypolinkBuilder
{
    /**
     * @var ObjectManager
     */
    protected ObjectManager $objectManager;

    /**
     * @var SettingsReader
     */
    protected SettingsReader $siteConfiguration;

    /**
     * @param ContentObjectRenderer $contentObjectRenderer
     * @param TypoScriptFrontendController|null $typoScriptFrontendController
     */
    public function __construct(
        ContentObjectRenderer $contentObjectRenderer,
        TypoScriptFrontendController $typoScriptFrontendController = null
    ) {
        parent::__construct($contentObjectRenderer, $typoScriptFrontendController);

        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->siteConfiguration = GeneralUtility::makeInstance(SettingsReader::class);
    }


    /**
     * Generates link to product single view
     *
     * @param array $linkDetails
     * @param string $linkText
     * @param string $target
     * @param array $conf
     * @return array
     */
    public function build(array &$linkDetails, string $linkText, string $target, array $conf): array
    {
        $finalUrl = '';

        $record = $this->findRecord($linkDetails);
        $pageUid = $this->getSingleViewPageUid();

        if ($pageUid > 0 && $record) {
            $urlBuilder = $this->getUrlBuilder();

            if ($record instanceof Product) {
                $finalUrl = $urlBuilder->url($pageUid, $record->getFirstCategory(), $record);
            } elseif ($record instanceof Category) {
                $finalUrl = $urlBuilder->url($pageUid, $record);
            }
        }

        return [$finalUrl, $linkText, $target];
    }

    /**
     * Get URL builder
     *
     * @return UrlBuilderServiceInterface
     */
    protected function getUrlBuilder(): UrlBuilderServiceInterface
    {
        return $this->objectManager->get(
            UrlBuilderServiceInterface::class,
            $this->getTypoScriptFrontendController()
        );
    }

    /**
     * Find record
     *
     * @param array $linkDetails
     * @return AbstractEntity|null
     */
    protected function findRecord(array $linkDetails): ?AbstractEntity
    {
        $id = intval($linkDetails['product'] ?? $linkDetails['category']);

        $repository = isset($linkDetails['product'])
            ? $this->objectManager->get(ProductRepository::class)
            : $this->objectManager->get(CategoryRepository::class);

        return $repository->findByUid($id);
    }

    /**
     * Try to get product single view pid
     *
     * @return int
     */
    protected function getSingleViewPageUid(): int
    {
        return intval($this->getPidFromSettings() ?: $this->siteConfiguration->getValue('singleViewPid') ?: 0);
    }

    /**
     * Get pid from settings
     *
     * @return int
     */
    protected function getPidFromSettings(): int
    {
        /** @var ConfigurationManagerInterface $configurationManager */
        $configurationManager = $this->objectManager->get(ConfigurationManagerInterface::class);

        $settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'PxaProductManager',
            'Pi1'
        );

        return intval($settings['pids']['singleViewPid'] ?? 0);
    }
}
