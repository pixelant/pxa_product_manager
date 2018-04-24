<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\LinkHandler;

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

use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Utility\ConfigurationUtility;
use Pixelant\PxaProductManager\Utility\MainUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Typolink\AbstractTypolinkBuilder;

class ProductLinkBuilder extends AbstractTypolinkBuilder
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * AbstractTypolinkBuilder constructor.
     *
     * @param $contentObjectRenderer ContentObjectRenderer
     */
    public function __construct(ContentObjectRenderer $contentObjectRenderer)
    {
        parent::__construct($contentObjectRenderer);
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->productRepository = $objectManager->get(ProductRepository::class);
        $this->categoryRepository = $objectManager->get(CategoryRepository::class);
    }

    /**
     * Generates link to product single view
     *
     * @param array $linkDetails
     * @param string $linkText
     * @param string $target
     * @param array $conf
     * @return array
     */
    public function build(array &$linkDetails, string $linkText, string $target, array $conf): array
    {
        $finalUrl = '';

        if (isset($linkDetails['product'])
            && $product = $this->productRepository->findByUid((int)$linkDetails['product'])
        ) {
            $parameters = MainUtility::buildLinksArguments($product);
        } elseif (isset($linkDetails['category'])
            && $category = $this->categoryRepository->findByUid((int)$linkDetails['category'])
        ) {
            $parameters = MainUtility::buildLinksArguments(null, $category);
        }

        if (isset($parameters)) {
            $singleViewPageUid = ConfigurationUtility::getSettings()['pagePid'];

            $confLink = [
                'parameter' => $singleViewPageUid ?: MainUtility::getTSFE()->id,
                'useCacheHash' => 1,
                'additionalParams' => '&' . http_build_query($parameters),
            ];

            $finalUrl = $this->contentObjectRenderer->typolink_URL($confLink);
        }

        return [$finalUrl, $linkText, $target];
    }
}
