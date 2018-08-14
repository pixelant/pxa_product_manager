<?php

namespace Pixelant\PxaProductManager\ViewHelpers;

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Filter;
use Pixelant\PxaProductManager\Domain\Model\Option;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

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
class FilterOptionsViewHelper extends AbstractViewHelper
{

    /**
     * @var \Pixelant\PxaProductManager\Domain\Repository\CategoryRepository
     * @inject
     */
    protected $categoryRepository;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('filter', 'object', 'Filter record', true);
    }

    /**
     * Get filtering options
     *
     * @return array
     */
    public function render(): array
    {
        $options = [];

        /** @var Filter $filter */
        $filter = $this->arguments['filter'];

        switch ($filter->getType()) {
            case Filter::TYPE_CATEGORIES:
                $categories = $this->categoryRepository->findByParent($filter->getParentCategory());
                /** @var Category $category */
                foreach ($categories as $category) {
                    $options[] = [
                        'title' => $category->getTitle(),
                        'value' => $category->getUid()
                    ];
                }
                break;
            case Filter::TYPE_ATTRIBUTES:
                /** @var Option $attributeOption */
                foreach ($filter->getAttribute()->getOptions() as $attributeOption) {
                    $options[] = [
                        'title' => $attributeOption->getValue(),
                        'value' => $attributeOption->getUid()
                    ];
                }
                break;
            case Filter::TYPE_ATTRIBUTES_MINMAX:
                /** @var Option $attributeOption */
                foreach ($filter->getAttribute()->getOptions() as $attributeOption) {
                    $options[] = [
                        'title' => $attributeOption->getValue(),
                        'value' => (int)$attributeOption->getValue(),
                        'uid' => $attributeOption->getUid()
                    ];
                }
                break;
            default:
                break;
        }

        return $options;
    }
}
