<?php

namespace Pixelant\PxaProductManager\ViewHelpers\Product;

use Pixelant\PxaProductManager\Domain\Model\Product;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

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
class GetFalFilesByExtensionViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Register arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('product', 'object', 'Product model', true);
        $this->registerArgument('extension', 'string', 'File extension look for', true);
        $this->registerArgument('limit', 'int', 'Limit, 0 - unlimited', false, 0);
    }

    /**
     * Get product file by extension
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return array
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): array {
        /** @var Product $product */
        $product = $arguments['product'];
        $extension = $arguments['extension'];
        $limit = (int)$arguments['limit'];

        $result = [];
        $count = 1;

        /** @var FileReference $falLink */
        foreach ($product->getFalLinks() as $falLink) {
            if ($falLink->getOriginalResource()->getExtension() === $extension) {
                $result[] = $falLink;
                if ($limit === $count) {
                    break;
                }
                $count++;
            }
        }

        return $result;
    }
}
