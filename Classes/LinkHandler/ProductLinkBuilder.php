<?php
declare(strict_types=1);
namespace Pixelant\PxaProductManager\LinkHandler;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Utility\ConfigurationUtility;
use Pixelant\PxaProductManager\Utility\MainUtility;
use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Configuration\AbstractConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\FrontendConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\EnvironmentService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Typolink\AbstractTypolinkBuilder;
use TYPO3\CMS\Frontend\Controller\ErrorController;

class ProductLinkBuilder extends AbstractTypolinkBuilder
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var AbstractConfigurationManager
     */
    protected $configurationManager;

    /**
     * AbstractTypolinkBuilder constructor.
     *
     * @param $contentObjectRenderer ContentObjectRenderer
     */
    public function __construct(ContentObjectRenderer $contentObjectRenderer)
    {
        parent::__construct($contentObjectRenderer);
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->productRepository = $objectManager->get(ProductRepository::class);
        $this->categoryRepository = $objectManager->get(CategoryRepository::class);

        $environmentService = $objectManager->get(EnvironmentService::class);
        if ($environmentService->isEnvironmentInFrontendMode()) {
            $this->configurationManager = $objectManager->get(FrontendConfigurationManager::class);
        } else {
            $this->configurationManager = $objectManager->get(BackendConfigurationManager::class);
        }
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

        $this->checkExtbaseMapping();

        if (isset($linkDetails['product'])
            && $product = $this->productRepository->findByUid((int)$linkDetails['product'])
        ) {
            $parameters = MainUtility::buildLinksArguments($product);
        } elseif (isset($linkDetails['category'])
            && $category = $this->categoryRepository->findByUid((int)$linkDetails['category'])
        ) {
            $parameters = MainUtility::buildLinksArguments(null, $category);
        }

        if (isset($parameters)) {
            $singleViewPageUid = ConfigurationUtility::getSettingsByPath('pagePid');

            if (empty($singleViewPageUid) && !empty($GLOBALS['TYPO3_REQUEST'])) {
                $site = $GLOBALS['TYPO3_REQUEST']->getAttribute('site');
                $singleViewPageUid = $site->getConfiguration()['productSingleViewFallbackPid'];

                if (empty($singleViewPageUid) || (int)$singleViewPageUid === 0) {
                    $response = GeneralUtility::makeInstance(ErrorController::class)->pageNotFoundAction(
                        $GLOBALS['TYPO3_REQUEST'],
                        'The requested product single view page was not found',
                        ['The fallback pid is not set']
                    );
                    throw new ImmediateResponseException($response, 1533931329);
                }
            }

            $confLink = [
                'parameter' => $singleViewPageUid ?: MainUtility::getTSFE()->id,
                'useCacheHash' => 1,
                'additionalParams' => '&' . http_build_query($parameters),
            ];

            $finalUrl = $this->contentObjectRenderer->typolink_URL($confLink);
        }

        return [$finalUrl, $linkText, $target];
    }

    /**
     * Checks if extbase mapping is set for PM Category to sys_category
     *
     * Workaround to prevent the wrong tablename getting written
     * in cf_extbase_datamapfactory_datamap for identifier
     * 'Pixelant%PxaProductManager%Domain%Model%Category'
     * when LinkBuilder is called from middleware in TYPO3 v9,
     * e.g. the first request after cache is cleared is a redirec to a Category (PM).
     *
     * Since extbase ts configuration isn't loaded when ProductLinkBuilder
     * is called from middleware, mapping for
     * 'Pixelant\PxaProductManager\Domain\Model\Category' to 'sys_category'
     * isn't set and TYPO3 will then assume that the table name
     * for object is 'tx_pxaproductmanager_domain_model_category'.
     *
     * Once set in cache TYPO3 will try to load categories from
     * 'tx_pxaproductmanager_domain_model_category' instead from 'sys_category'
     * and exception will be thrown.
     *
     * This will make sure the mapping for
     * 'Pixelant\PxaProductManager\Domain\Model\Category' to 'sys_category'
     * exists when buildDataMapInternal in DataMapFactory is executed.
     *
     * @return void
     */
    protected function checkExtbaseMapping()
    {
        $version = VersionNumberUtility::convertVersionStringToArray(VersionNumberUtility::getNumericTypo3Version());
        if ($version['version_main'] == 9) {
            $className = 'Pixelant\PxaProductManager\Domain\Model\Category';
            $configuration = $this->configurationManager->getConfiguration(null, null);
            if (empty($configuration['persistence']['classes'][$className]['mapping']['tableName'])) {
                $pmCategoryMapping['persistence']['classes'][$className]['mapping']['tableName'] = 'sys_category';
                $this->configurationManager->setConfiguration($pmCategoryMapping);
            }
        }
    }
}
