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
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\ImageService;

/**
 * Class ImageViewHelper
 * @package Pixelant\PxaProductManager\ViewHelpers\Solr
 */
class ImageViewHelper implements ViewHelper
{
    /**
     * @var ImageService
     */
    protected $imageService;

    /**
     * Init
     *
     * @param array $arguments
     */
    public function __construct(array $arguments = [])
    {
        $this->imageService = GeneralUtility::makeInstance(ObjectManager::class)->get(ImageService::class);
    }

    /**
     * Process instructions for image
     *
     * @param array $arguments
     * @return string
     */
    public function execute(array $arguments = []): string
    {
        if (is_numeric($arguments[0]) && (int)$arguments[0]) {
            $image = $arguments[0];
            $treatIdAsReference = true;
        } else {
            $image = $arguments[0];
            $treatIdAsReference = false;
        }

        $maxWidth = $arguments[1];
        $maxHeight = $arguments[2];

        try {
            $image = $this->imageService->getImage($image, null, $treatIdAsReference);
            if ($image->hasProperty('crop') && $image->getProperty('crop')) {
                $cropString = $image->getProperty('crop');
            }

            $cropVariantCollection = CropVariantCollection::create((string)$cropString);
            $cropVariant = $arguments['cropVariant'] ?: 'default';
            $cropArea = $cropVariantCollection->getCropArea($cropVariant);

            $processingInstructions = [
                'width' => null,
                'height' => null,
                'minWidth' => null,
                'minHeight' => null,
                'maxWidth' => $maxWidth,
                'maxHeight' => $maxHeight,
                'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
            ];

            $processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);
            return $this->imageService->getImageUri($processedImage);
        } catch (\Exception $e) {
        };

        return '';
    }
}
