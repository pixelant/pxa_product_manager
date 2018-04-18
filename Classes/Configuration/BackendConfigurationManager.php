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

use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager as ExtbaseBackendConfigurationManager;

/**
 * A general purpose configuration manager used in backend mode.
 */
class BackendConfigurationManager extends ExtbaseBackendConfigurationManager
{
    /**
     * Set the current page ID
     * @param integer $currentPageId Current page id
     * @return void
     */
    public function setCurrentPageId($currentPageId)
    {
        $this->currentPageId = $currentPageId;
    }
}
