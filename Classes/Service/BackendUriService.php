<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUriService
{
    /**
     * @param string $module
     * @param array $params
     * @return string
     */
    public function buildUri(string $module, array $params)
    {
        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        try {
            $uri = $uriBuilder->buildUriFromRoute($module, $params, UriBuilder::ABSOLUTE_URL);
        } catch (\TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException $e) {
            return '';
        }

        return (string)$uri;
    }
}
