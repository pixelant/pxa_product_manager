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

use Pixelant\PxaProductManager\Service\Link\LinkBuilderService;
use Pixelant\PxaProductManager\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\ErrorController;
use TYPO3\CMS\Frontend\Typolink\AbstractTypolinkBuilder;

class ProductLinkBuilder extends AbstractTypolinkBuilder
{
    /**
     * Link builder modes
     */
    const PRODUCT_MODE = 1;
    const CATEGORY_MODE = 2;

    /**
     * Product or category UID
     *
     * @var int
     */
    protected $recordUid = 0;

    /**
     * Determinate builder mode
     *
     * @var int
     */
    protected $mode = 0;

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

        $this->defineBuilderMode($linkDetails);

        $singleViewPageUid = ConfigurationUtility::getSettingsByPath('pagePid');

        if (empty($singleViewPageUid) && ($site = $this->getSite())) {
            $singleViewPageUid = $site->getConfiguration()['productSingleViewFallbackPid'];
        }

        $singleViewPageUid = (int)$singleViewPageUid;
        if ($singleViewPageUid > 0) {
            $linkBuilder = $this->getLinkBuilder();

            switch ($this->mode) {
                case self::PRODUCT_MODE:
                    $finalUrl = $linkBuilder->buildForProduct($singleViewPageUid, $this->recordUid);
                    break;
                case self::CATEGORY_MODE:
                    $finalUrl = $linkBuilder->buildForCategory($singleViewPageUid, $this->recordUid);
                    break;
            }
        }

        return [$finalUrl, $linkText, $target];
    }

    /**
     * Get link builder
     *
     * @return LinkBuilderService
     */
    protected function getLinkBuilder(): LinkBuilderService
    {
        return GeneralUtility::makeInstance(
            LinkBuilderService::class,
            null, // Detect language automatically
            $this->getTypoScriptFrontendController() // Pass give TypoScriptFrontendController
        );
    }

    /**
     * Define link builder mode
     *
     * @param array $linkDetails
     */
    protected function defineBuilderMode(array $linkDetails): void
    {
        if (isset($linkDetails['product'])) {
            $this->mode = self::PRODUCT_MODE;
            $this->recordUid = (int)$linkDetails['product'];

            return;
        }

        if (isset($linkDetails['category'])) {
            $this->mode = self::CATEGORY_MODE;
            $this->recordUid = (int)$linkDetails['category'];

            return;
        }

        // @codingStandardsIgnoreStart
        throw new \InvalidArgumentException('Product or Category shall be provided for product link builder', 1557304991448);
        // @codingStandardsIgnoreEnd
    }

    /**
     * Get site entry from request or try to find by record Pid
     *
     * @return Site|null
     */
    protected function getSite(): ?Site
    {
        /** @var Site $site */
        $site = isset($GLOBALS['TYPO3_REQUEST'])
            ? $GLOBALS['TYPO3_REQUEST']->getAttribute('site')
            : null;

        // Try to find site by record PID
        if ($site === null || $site instanceof NullSite) {
            $table = $this->mode === self::PRODUCT_MODE
                ? 'tx_pxaproductmanager_domain_model_product'
                : 'sys_category';

            $pid = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable($table)
                ->select(
                    ['pid'],
                    $table,
                    ['uid' => $this->recordUid]
                )
                ->fetchColumn(0);

            if ($pid > 0) {
                $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
                try {
                    return $siteFinder->getSiteByPageId((int)$pid);
                } catch (SiteNotFoundException $exception) {
                    // Just return null
                    return null;
                }
            }
        }

        return $site;
    }
}
