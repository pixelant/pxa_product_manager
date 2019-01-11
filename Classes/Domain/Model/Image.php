<?php

namespace Pixelant\PxaProductManager\Domain\Model;

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

/**
 *
 *
 * @package pxa_product_manager
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Image extends \TYPO3\CMS\Extbase\Domain\Model\FileReference
{

    /**
     * Obsolete when foreign_selector is supported by ExtBase persistence layer
     *
     * @var integer
     */
    protected $uidLocal;

    /**
     * @var boolean
     */
    protected $useInListing;

    /**
     * @var boolean
     */
    protected $mainImage;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * Set File uid
     *
     * @param integer $fileUid
     * @return void
     */
    public function setFileUid(int $fileUid)
    {
        $this->uidLocal = $fileUid;
    }

    /**
     * Get File UID
     *
     * @return int
     */
    public function getFileUid(): int
    {
        return $this->uidLocal;
    }

    /**
     * @return bool
     */
    public function isUseInListing(): bool
    {
        return $this->useInListing;
    }

    /**
     * @param bool $useInListing
     */
    public function setUseInListing(bool $useInListing)
    {
        $this->useInListing = $useInListing;
    }

    /**
     * @return bool
     */
    public function isMainImage(): bool
    {
        return $this->mainImage;
    }

    /**
     * @param bool $mainImage
     */
    public function setMainImage(bool $mainImage)
    {
        $this->mainImage = $mainImage;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title ?: $this->getOriginalResource()->getTitle();
    }

    /**
     * Set description
     *
     * @param string $description
     * @return void
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description ?: $this->getOriginalResource()->getDescription();
    }
}
