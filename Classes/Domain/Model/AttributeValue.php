<?php

namespace Pixelant\PxaProductManager\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

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
class AttributeValue extends AbstractEntity
{

    /**
     * @var \Pixelant\PxaProductManager\Domain\Model\Product
     */
    protected ?Product $product = null;

    /**
     * @var string
     */
    protected string $value = '';

    /**
     * @var \Pixelant\PxaProductManager\Domain\Model\Attribute
     */
    protected ?Attribute $attribute = null;

    /**
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product|null $product
     * @return AttributeValue
     */
    public function setProduct(?Product $product): AttributeValue
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return AttributeValue
     */
    public function setValue(string $value): AttributeValue
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return Attribute|null
     */
    public function getAttribute(): ?Attribute
    {
        return $this->attribute;
    }

    /**
     * @param Attribute|null $attribute
     * @return AttributeValue
     */
    public function setAttribute(?Attribute $attribute): AttributeValue
    {
        $this->attribute = $attribute;
        return $this;
    }
}
