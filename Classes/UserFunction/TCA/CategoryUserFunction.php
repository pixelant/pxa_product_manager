<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction\TCA;

use Pixelant\PxaProductManager\Service\BackendUriService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * TCA user function
 *
 * @package Pixelant\PxaProductManager\UserFunction
 */
class CategoryUserFunction
{
    /**
     * @param $PA
     * @param $fObj
     * @return string
     */
    public function pageModuleLinkField($PA, $fObj): string
    {
        if (!current($PA['row']['pxapm_content_page'])) {
            return '';
        }

        $page = current($PA['row']['pxapm_content_page']);

        /** @var BackendUriService $backendUriService */
        $backendUriService = GeneralUtility::makeInstance(BackendUriService::class);

        $uri = $backendUriService->buildUri('web_layout', [
            'id' => $page['uid']
        ]);

        return sprintf('<a href="%s" class="btn btn-default">%s</a>', $uri, $page['title']);
    }
}
