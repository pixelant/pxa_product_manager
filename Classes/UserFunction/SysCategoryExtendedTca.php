<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SysCategoryExtendedTca
{
    /**
     * @param $PA
     * @param $fObj
     * @return string
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    public function pageModuleLinkField($PA, $fObj)
    {
        if (! current($PA['row']['pxapm_content_page'])) {
            return '';
        }

        $page = current($PA['row']['pxapm_content_page']);

        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $params = [
            "id" => $page['uid']
        ];

        try {
            $uri = $uriBuilder->buildUriFromRoute('web_layout', $params, UriBuilder::ABSOLUTE_URL);
        } catch (\TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException $e) {
            return 'Can\'t generate the link';
        }

        $uri = (string)$uri;

        return "<a class='btn btn-default' href='{$uri}'>
                    <span class='text-primary'>{$page['title']}</span>
                </a>";
    }
}
