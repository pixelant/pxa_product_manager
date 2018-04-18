<?php
namespace Pixelant\PxaProductManager\Configuration;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager as ExtbaseConfigurationManager;

/**
 * A configuration manager following the strategy pattern (GoF315). It hides the concrete
 * implementation of the configuration manager and provides an unified acccess point.
 *
 * Use the shutdown() method to drop the concrete implementation.
 */
class ConfigurationManager extends ExtbaseConfigurationManager
{
    /**
     */
    protected function initializeConcreteConfigurationManager()
    {
        if ($this->environmentService->isEnvironmentInFrontendMode()) {
            $this->concreteConfigurationManager = $this->objectManager->get(
                \TYPO3\CMS\Extbase\Configuration\FrontendConfigurationManager::class
            );
        } else {
            $this->concreteConfigurationManager = $this->objectManager->get(
                \Pixelant\PxaProductManager\Configuration\BackendConfigurationManager::class
            );
        }
    }

    /**
     * Set the current page ID for BackendConfiguration
     * @param integer $currentPageId Current page id
     * @return void
     */
    public function setCurrentPageId($currentPageId)
    {
        if ($this->concreteConfigurationManager instanceof BackendConfigurationManager) {
            $this->concreteConfigurationManager->setCurrentPageId($currentPageId);
        }
    }

    /**
     * check if environment is FrontentMode in ConfigurationManager
     * @return void
     */
    public function isEnvironmentInFrontendMode()
    {
        return $this->environmentService->isEnvironmentInFrontendMode();
    }
}
