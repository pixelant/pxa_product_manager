<?php

namespace Pixelant\PxaProductManager\ViewHelpers\Solr;

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

use ApacheSolrForTypo3\Solr\ViewHelper\ViewHelper;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Utility\MainUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class ProductLinkViewHelper implements ViewHelper
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * Plugin settings
     *
     * @var array
     */
    protected $settings;

    /**
     * Init
     *
     * @param array $arguments
     */
    public function __construct(array $arguments = [])
    {
        $this->productRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(ProductRepository::class);
        $this->settings = MainUtility::getSettings();
    }

    /**
     * Generate product link if document of type product
     * otherwise return url
     *
     * @param array $arguments
     * @return string
     */
    public function execute(array $arguments = []): string
    {
        $document = unserialize($arguments[0]);
        $url = '';

        if (is_array($document)) {
            if (GeneralUtility::inList($this->settings['solr']['productManagerTypes'], $document['type'])) {
                /** @var Product $product */
                $product = $this->productRepository->findByUid((int)$document['uid']);

                if ($product !== null) {
                    $url = MainUtility::getTSFE()->cObj->getTypoLink_URL(
                        $this->settings['pagePid'],
                        MainUtility::buildLinksArguments($product)
                    );
                }
            } else {
                $url = $document['url'] ?? '';
            }
        }

        return $url;
    }
}
