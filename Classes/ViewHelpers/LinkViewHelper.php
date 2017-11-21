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
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Utility\MainUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Link\PageViewHelper;

/**
 *
 *
 * @package pxa_product_manager
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class LinkViewHelper extends PageViewHelper
{

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('category', 'mixed', 'Category to link', false, null);
        $this->registerArgument('product', 'mixed', 'Product to link', false, null);
    }

    // @codingStandardsIgnoreStart
    /**
     * @param int|NULL $pageUid target page. See TypoLink destination
     * @param array $additionalParams query parameters to be attached to the resulting URI
     * @param int $pageType type of the target page. See typolink.parameter
     * @param bool $noCache set this to disable caching for the target page. You should not need this.
     * @param bool $noCacheHash set this to suppress the cHash query parameter created by TypoLink. You should not need this.
     * @param string $section the anchor to be added to the URI
     * @param bool $linkAccessRestrictedPages If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.
     * @param bool $absolute If set, the URI of the rendered link is absolute
     * @param bool $addQueryString If set, the current query parameters will be kept in the URI
     * @param array $argumentsToBeExcludedFromQueryString arguments to be removed from the URI. Only active if $addQueryString = TRUE
     * @param string $addQueryStringMethod Set which parameters will be kept. Only active if $addQueryString = TRUE
     * @return string Rendered page URI
     *
     */
    // @codingStandardsIgnoreEnd
    public function render(
        $pageUid = null,
        array $additionalParams = [],
        $pageType = 0,
        $noCache = false,
        $noCacheHash = false,
        $section = '',
        $linkAccessRestrictedPages = false,
        $absolute = false,
        $addQueryString = false,
        array $argumentsToBeExcludedFromQueryString = [],
        $addQueryStringMethod = null
    ) {
        $product = $this->arguments['product'];
        $category = $this->arguments['category'];

        // add categories arguments
        if (!is_object($category) && $category !== null) {
            // if uid of category was passed
            $category = $this->objectManager->get(CategoryRepository::class)->findByUid(
                (int)$category
            );
        }

        // add product
        if (!is_object($product) && $product !== null) {
            // if uid of category was passed
            $product = $this->objectManager->get(ProductRepository::class)->findByUid(
                (int)$product
            );
        }

        $arguments = MainUtility::buildLinksArguments($product, $category);

        if (!empty($arguments)) {
            $additionalParams = array_merge_recursive($additionalParams, $arguments);
        }

        // don't pass empty string or '0'
        $pageUid = (int)$pageUid === 0 ? null : $pageUid;

        return parent::render(
            $pageUid,
            $additionalParams,
            $pageType,
            $noCache,
            $noCacheHash,
            $section,
            $linkAccessRestrictedPages,
            $absolute,
            $addQueryString,
            $argumentsToBeExcludedFromQueryString,
            $addQueryStringMethod
        );
    }
}
