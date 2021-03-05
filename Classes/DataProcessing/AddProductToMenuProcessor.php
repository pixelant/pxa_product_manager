<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\DataProcessing;

use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * This file is part of the "pxa_product_manager" Extension for TYPO3 CMS.
 *
 * Based on GeorgRinger\News\DataProcessing\AddNewsToMenuProcessor
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Add the current product record to any menu, e.g. breadcrumb.
 *
 * 20 = Pixelant\PxaProductManager\DataProcessing\AddProductToMenuProcessor
 * 20.menus = breadcrumb
 */
class AddProductToMenuProcessor implements DataProcessorInterface
{
    /**
     * @param ContentObjectRenderer $cObj
     * @param array $contentObjectConfiguration
     * @param array $processorConfiguration
     * @param array $processedData
     * @return array
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        if (!$processorConfiguration['menus']) {
            return $processedData;
        }
        $productRecord = $this->getProductRecord();

        if ($productRecord) {
            $menus = GeneralUtility::trimExplode(',', $processorConfiguration['menus'], true);
            foreach ($menus as $menu) {
                if (isset($processedData[$menu])) {
                    $this->addProductRecordToMenu($productRecord, $processedData[$menu]);
                }
            }
        }

        return $processedData;
    }

    /**
     * Add the product record to the menu items.
     *
     * @param array $productRecord
     * @param array $menu
     */
    protected function addProductRecordToMenu(array $productRecord, array &$menu): void
    {
        foreach ($menu as &$menuItem) {
            $menuItem['current'] = 0;
        }

        $menu[] = [
            'data' => $productRecord,
            'title' => $productRecord['name'],
            'active' => 1,
            'current' => 1,
            'link' => GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'),
            'isProduct' => true,
        ];
    }

    /**
     * Get the product record including possible translations.
     *
     * @return array
     */
    protected function getProductRecord(): array
    {
        $productId = 0;
        $productShowVars = GeneralUtility::_GET('tx_pxaproductmanager_productshow') ?? [];
        $productRenderVars = GeneralUtility::_GET('tx_pxaproductmanager_productrender') ?? [];
        $vars = array_merge($productShowVars, $productRenderVars);

        if (isset($vars['product'])) {
            $productId = (int)$vars['product'];
        }

        if ($productId) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable(ProductRepository::TABLE_NAME);
            $row = $queryBuilder
                ->select('*')
                ->from(ProductRepository::TABLE_NAME)
                ->where(
                    $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($productId, \PDO::PARAM_INT))
                )
                ->execute()
                ->fetchAssociative();

            if ($row) {
                $row = $this->getTsfe()->sys_page->getRecordOverlay(
                    ProductRepository::TABLE_NAME,
                    $row,
                    $this->getCurrentLanguage()
                );
            }

            if (is_array($row) && !empty($row)) {
                return $row;
            }
        }

        return [];
    }

    /**
     * Get current language.
     *
     * @return int
     */
    protected function getCurrentLanguage(): int
    {
        $context = GeneralUtility::makeInstance(Context::class);

        try {
            $languageId = $context->getPropertyFromAspect('language', 'contentId');
        } catch (AspectNotFoundException $e) {
            $languageId = 0;
        }

        return (int)$languageId;
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTsfe(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
