<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Hook;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;

class TcaProcFunc
{
    public function getTemplateLayouts(array &$config): void
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $extbaseConfiguration = $configurationManager
            ->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

        /** @codingStandardsIgnoreStart */
        $templateLayouts = $extbaseConfiguration['plugin.']['tx_pxaproductmanager.']['settings.']['productType.']['templateLayouts.'];
        // @codingStandardsIgnoreEnd

        foreach ($templateLayouts as $title => $layout) {
            array_push($config['items'], [$title, $layout]);
        }
    }
}
