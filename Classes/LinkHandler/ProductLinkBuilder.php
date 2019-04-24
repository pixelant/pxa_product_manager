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
use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\ErrorController;
use TYPO3\CMS\Frontend\Typolink\AbstractTypolinkBuilder;

class ProductLinkBuilder extends AbstractTypolinkBuilder
{
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

        if (isset($linkDetails['product']) || isset($linkDetails['category'])) {
            $singleViewPageUid = ConfigurationUtility::getSettingsByPath('pagePid');

            if (empty($singleViewPageUid) && !empty($GLOBALS['TYPO3_REQUEST'])) {
                /** @var Site $site */
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

            $linkBuilder = $this->getLinkBuilder();

            $finalUrl = isset($linkDetails['product'])
                ? $linkBuilder->buildForProduct((int)$singleViewPageUid, (int)$linkDetails['product'])
                : $linkBuilder->buildForCategory((int)$singleViewPageUid, (int)$linkDetails['category']);
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
        return GeneralUtility::makeInstance(LinkBuilderService::class);
    }
}
