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
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 *
 *
 * @package pxa_product_manager
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class LinkViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Arguments initialization
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        // @codingStandardsIgnoreStart
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('target', 'string', 'Target of link', false);
        $this->registerTagAttribute('rel', 'string', 'Specifies the relationship between the current document and the linked document', false);
        $this->registerArgument('pageUid', 'int', 'Target page. See TypoLink destination');
        $this->registerArgument('pageType', 'int', 'Type of the target page. See typolink.parameter');
        $this->registerArgument('noCache', 'bool', 'Set this to disable caching for the target page. You should not need this.');
        $this->registerArgument('noCacheHash', 'bool', 'Set this to suppress the cHash query parameter created by TypoLink. You should not need this.');
        $this->registerArgument('section', 'string', 'The anchor to be added to the URI');
        $this->registerArgument('linkAccessRestrictedPages', 'bool', 'If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.');
        $this->registerArgument('additionalParams', 'array', 'Additional query parameters that won\'t be prefixed like $arguments (overrule $arguments)');
        $this->registerArgument('absolute', 'bool', 'If set, the URI of the rendered link is absolute');
        $this->registerArgument('addQueryString', 'bool', 'If set, the current query parameters will be kept in the URI');
        $this->registerArgument('argumentsToBeExcludedFromQueryString', 'array', 'Arguments to be removed from the URI. Only active if $addQueryString = TRUE');
        $this->registerArgument('addQueryStringMethod', 'string', 'Set which parameters will be kept. Only active if $addQueryString = TRUE');
        // @codingStandardsIgnoreEnd

        $this->registerArgument('category', 'mixed', 'Category to link', false, null);
        $this->registerArgument('product', 'mixed', 'Product to link', false, null);
    }

    /**
     * Render product and categories link
     *
     * @return string Rendered page URI or anchor tag
     */
    public function render()
    {
        $pageUid = isset($this->arguments['pageUid']) ? (int)$this->arguments['pageUid'] : null;
        $pageType = isset($this->arguments['pageType']) ? (int)$this->arguments['pageType'] : 0;
        $noCache = isset($this->arguments['noCache']) ? (bool)$this->arguments['noCache'] : false;
        $noCacheHash = isset($this->arguments['noCacheHash']) ? (bool)$this->arguments['noCacheHash'] : false;
        $section = isset($this->arguments['section']) ? (string)$this->arguments['section'] : '';
        $linkAccessRestrictedPages = isset($this->arguments['linkAccessRestrictedPages'])
            ? (bool)$this->arguments['linkAccessRestrictedPages']
            : false;
        $additionalParams = isset($this->arguments['additionalParams'])
            ? (array)$this->arguments['additionalParams']
            : [];
        $absolute = isset($this->arguments['absolute']) ? (bool)$this->arguments['absolute'] : false;
        $addQueryString = isset($this->arguments['addQueryString']) ? (bool)$this->arguments['addQueryString'] : false;
        $argumentsToBeExcludedFromQueryString = isset($this->arguments['argumentsToBeExcludedFromQueryString'])
            ? (array)$this->arguments['argumentsToBeExcludedFromQueryString']
            : [];
        $addQueryStringMethod = $this->arguments['addQueryStringMethod'] ?? null;
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
        if ($pageUid === 0) {
            $pageUid = null;
        }

        $uriBuilder = $this->renderingContext->getControllerContext()->getUriBuilder();
        $uri = $uriBuilder->reset()
            ->setTargetPageUid($pageUid)
            ->setTargetPageType($pageType)
            ->setNoCache($noCache)
            ->setUseCacheHash(!$noCacheHash)
            ->setSection($section)
            ->setLinkAccessRestrictedPages($linkAccessRestrictedPages)
            ->setArguments($additionalParams)
            ->setCreateAbsoluteUri($absolute)
            ->setAddQueryString($addQueryString)
            ->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString)
            ->setAddQueryStringMethod($addQueryStringMethod)
            ->build();
        if ((string)$uri !== '') {
            $this->tag->addAttribute('href', $uri);
            $this->tag->setContent($this->renderChildren());
            $result = $this->tag->render();
        } else {
            $result = $this->renderChildren();
        }
        return $result;
    }
}
