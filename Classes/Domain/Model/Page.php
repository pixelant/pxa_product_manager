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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Page extends AbstractEntity
{
    /**
     * @var string
     */
    protected string $title = '';

    /**
     * @var int
     */
    protected int $doktype = 0;

    /**
     * Get the value of title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the value of title.
     *
     * @param string $title
     *
     * @return Page
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of doktype.
     *
     * @return int
     */
    public function getDoktype(): int
    {
        return $this->doktype;
    }

    /**
     * Set the value of doktype.
     *
     * @param int $doktype
     *
     * @return Page
     */
    public function setDoktype(int $doktype): self
    {
        $this->doktype = $doktype;

        return $this;
    }
}
