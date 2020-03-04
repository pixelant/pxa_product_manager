<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model\DTO\Factory;

use Pixelant\PxaProductManager\Domain\Model\DTO\CategoryDemand;
use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;

/**
 * @package Pixelant\PxaProductManager\Domain\Model\DTO\Factory
 */
class CategoryDemandFactory extends AbstractFactory
{

    /**
     * @inheritDoc
     */
    protected function className(array $settings): string
    {
        return !empty($settings['demand']['objects']['categoryDemand'])
            ? $settings['demand']['objects']['categoryDemand']
            : CategoryDemand::class;
    }

    /**
     * @inheritDoc
     */
    protected function demandSpecial(DemandInterface $demand, array $settings): void
    {
        if (!empty($settings['categoriesOrderings']['orderBy'])) {
            $demand->setOrderBy($settings['categoriesOrderings']['orderBy']);
        }
        if (!empty($settings['categoriesOrderings']['orderDirection'])) {
            $demand->setOrderDirection($settings['categoriesOrderings']['orderDirection']);
        }
        if (!empty($settings['navigation']['hideCategoriesWithoutProducts'])) {
            $demand->setHideCategoriesWithoutProducts((bool)$settings['navigation']['hideCategoriesWithoutProducts']);
        }
        if (!empty($settings['navigation']['onlyVisibleInNavigation'])) {
            $demand->setOnlyVisibleInNavigation((bool)$settings['navigation']['onlyVisibleInNavigation']);
        }
        if (!empty($settings['parent'])) {
            $demand->setParent($settings['parent']);
        }
    }
}
