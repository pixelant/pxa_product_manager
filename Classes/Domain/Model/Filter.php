<?php

namespace Pixelant\PxaProductManager\Domain\Model;

/*
 *
 *  Copyright notice
 *
 *  (c) 2017
 *
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
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;

/**
 * Filter.
 */
class Filter extends AbstractEntity
{
    /**
     * Conjunctions as string.
     */
    public const CONJUNCTION_OR = 'or';
    public const CONJUNCTION_AND = 'and';

    /**
     * Types.
     */
    public const TYPE_CATEGORIES = 1;
    public const TYPE_ATTRIBUTES = 2;
    public const TYPE_ATTRIBUTES_MINMAX = 3;

    /**
     * @var int
     */
    protected int $type = 1;

    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @var \Pixelant\PxaProductManager\Domain\Model\Category
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $category = null;

    /**
     * @var \Pixelant\PxaProductManager\Domain\Model\Attribute
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $attribute = null;

    /**
     * label.
     *
     * @var string
     */
    protected string $label = '';

    /**
     * @var string
     */
    protected string $conjunction = 'and';

    /**
     * @var string
     */
    protected string $guiType = '';

    /**
     * @var string
     */
    protected string $guiState = '';

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Filter
     */
    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Filter
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        if ($this->category instanceof LazyLoadingProxy) {
            $this->category = $this->category->_loadRealInstance();
        }

        return $this->category;
    }

    /**
     * @param Category $category
     * @return Filter
     */
    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Attribute|null
     */
    public function getAttribute(): ?Attribute
    {
        if ($this->attribute instanceof LazyLoadingProxy) {
            $this->attribute = $this->attribute->_loadRealInstance();
        }

        return $this->attribute;
    }

    /**
     * @param Attribute $attribute
     * @return Filter
     */
    public function setAttribute(Attribute $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return Filter
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getConjunction(): string
    {
        return $this->conjunction;
    }

    /**
     * @param string $conjunction
     * @return Filter
     */
    public function setConjunction(string $conjunction): self
    {
        $this->conjunction = $conjunction;

        return $this;
    }

    /**
     * @return int
     */
    public function getAttributeUid(): int
    {
        $attribute = $this->getAttribute();

        return $attribute ? $attribute->getUid() : 0;
    }

    /**
     * Return array of options for filtering.
     *
     * @return array
     */
    public function getOptions(): array
    {
        switch ($this->type) {
            case static::TYPE_CATEGORIES:
                $entityOptions = $this->getCategory()->getSubCategories();

                break;
            case static::TYPE_ATTRIBUTES:
                $entityOptions = $this->getAttribute()->getOptions();

                break;
            default:
                $entityOptions = [];
        }

        $options = [];
        /** @var AbstractEntity $entityOption */
        foreach ($entityOptions as $entityOption) {
            $options[] = [
                'value' => $entityOption->getUid(),
                'label' => $entityOption->getTitle(),
            ];
        }

        return $options;
    }

    /**
     * Get the value of guiType.
     *
     * @return string
     */
    public function getGuiType()
    {
        return $this->guiType;
    }

    /**
     * Set the value of guiType.
     *
     * @param string  $guiType
     *
     * @return Filter
     */
    public function setGuiType(string $guiType): self
    {
        $this->guiType = $guiType;

        return $this;
    }

    /**
     * Get the value of guiState.
     *
     * @return string
     */
    public function getGuiState()
    {
        return $this->guiState;
    }

    /**
     * Set the value of guiState.
     *
     * @param string  $guiState
     *
     * @return Filter
     */
    public function setGuiState(string $guiState): self
    {
        $this->guiState = $guiState;

        return $this;
    }
}
