<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service;

use Pixelant\PxaProductManager\Configuration\Site\SettingsReader;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Service\Url\UrlBuilderServiceInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Typolink\AbstractTypolinkBuilder;

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
        $this->checkExtbaseMappings();
    }

    /**
     * Generates link to product single view.
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
                $finalUrl = $urlBuilder->url($record);
            }
        }

        return [$finalUrl, $linkText, $target];
    }

    /**
     * Get URL builder.
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
     * Find record.
     *
     * @param array $linkDetails
     * @return AbstractEntity|null
     */
    protected function findRecord(array $linkDetails): ?AbstractEntity
    {
        $id = (int)($linkDetails['product'] ?? 0);

        if (isset($linkDetails['product'])) {
            $repository = $this->objectManager->get(ProductRepository::class);

            return $repository->findByUid($id);
        }

        return null;
    }

    /**
     * Try to get product single view pid.
     *
     * @return int
     */
    protected function getSingleViewPageUid(): int
    {
        return (int) ($this->getPidFromSettings() ?: $this->siteConfiguration->getValue('singleViewPid') ?: 0);
    }

    /**
     * Get pid from settings.
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

        return (int) ($settings['pids']['singleViewPid'] ?? 0);
    }

    /**
     * Checks if extbase mapping is set for mappings in ext_typoscript_setup.txt.
     *
     * Workaround to prevent the wrong tablename getting written
     * in cf_extbase_datamapfactory_datamap for identifier
     * 'Pixelant%PxaProductManager%Domain%Model%Category'
     * when LinkBuilder is called from middleware in TYPO3 v9,
     * e.g. the first request after cache is cleared is a redirec to a Category (PM).
     *
     * Since extbase ts configuration isn't loaded when TypolinkBuilderService
     *
     * check progress of https://forge.typo3.org/issues/75399
     *
     * @return void
     */
    protected function checkExtbaseMappings(): void
    {
        $configurationManager = $this->objectManager->get(ConfigurationManagerInterface::class);
        $configuration = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );

        $mappings = [
            'Pixelant\PxaProductManager\Domain\Model\Image' => 'sys_file_reference',
            'Pixelant\PxaProductManager\Domain\Model\AttributeFile' => 'sys_file_reference',
            'Pixelant\PxaProductManager\Domain\Model\Category' => 'sys_category',
        ];

        foreach ($mappings as $class => $mapping) {
            if (empty($configuration['persistence']['classes'][$class]['mapping']['tableName'])) {
                $configuration['persistence']['classes'][$class]['mapping']['tableName'] = $mapping;
                $configurationManager->setConfiguration($configuration);
            }
        }

        $configuration = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
    }
}
