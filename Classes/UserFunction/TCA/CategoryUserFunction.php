<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction\TCA;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * TCA user function.
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

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uri = (string)$uriBuilder->buildUriFromRoute('web_layout', ['id' => $page['uid']]);

        return sprintf('<a href="%s" class="btn btn-default">%s</a>', $uri, $page['title']);
    }
}
