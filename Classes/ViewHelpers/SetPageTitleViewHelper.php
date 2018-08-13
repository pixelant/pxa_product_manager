<?php
namespace Pixelant\PxaProductManager\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014
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

use Pixelant\PxaProductManager\Utility\MainUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class SetPageTitleViewHelper
 * @package Pixelant\PxaProductManager\ViewHelpers
 */
class SetPageTitleViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('title', 'string', 'Optional title', false);
    }

    /**
     * Replace page title
     */
    public function render()
    {
        if (empty($title = $this->arguments['title'])) {
            $title = $this->renderChildren();
        }

        $title = trim($title);

        // NOTICE: This function doesn't work there are non-cachable objects on the page. Title is wrongly cached.
        // Same with tx_news but maybe will be fixed later in TYPO3 or News?
        $title = strip_tags($title);
        MainUtility::getTSFE()->altPageTitle = $title;
        MainUtility::getTSFE()->indexedDocTitle = $title;
    }
}
