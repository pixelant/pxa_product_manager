<?php

namespace Pixelant\PxaProductManager\Domain\Model;

/*
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
 */

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Image extends AbstractFileReference
{
    /**
     * Types.
     */
    public const LISTING_IMAGE = 2;
    public const MAIN_IMAGE = 1;

    /**
     * @var int
     */
    protected int $type = 0;

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Image
     */
    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title ?: $this->getOriginalResource()->getTitle() ?? '';
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description ?: $this->getOriginalResource()->getDescription() ?? '';
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getAlternative(): string
    {
        return $this->alternative ?: $this->getOriginalResource()->getAlternative() ?? '';
    }
}
