<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction;

use Pixelant\PxaProductManager\Service\BackendUriService;
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

        /** @var BackendUriService $backendUriService */
        $backendUriService = GeneralUtility::makeInstance(BackendUriService::class);

        $uri = $backendUriService->buildUri('web_layout', [
            'id' => $page['uid']
        ]);

        return "<a class='btn btn-default' href='{$uri}'>
                    <span class='text-primary'>{$page['title']}</span>
                </a>";
    }
}
