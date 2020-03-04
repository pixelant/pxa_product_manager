<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model\DTO\Factory;

use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;

/**
 * @package Pixelant\PxaProductManager\Domain\Model\DTO\Factory
 */
class ProductDemandFactory extends AbstractFactory
{
    /**
     * @inheritDoc
     */
    protected function className(array $settings): string
    {
        return !empty($settings['demand']['objects']['productDemand'])
            ? $settings['demand']['objects']['productDemand']
            : ProductDemand::class;
    }

    /**
     * Product demand specific
     *
     * @inheritDoc
     */
    protected function demandSpecial(DemandInterface $demand, array $settings): void
    {
        if (!empty($settings['productOrderings']['orderBy'])) {
            $demand->setOrderBy($settings['productOrderings']['orderBy']);
        }
        if (!empty($settings['productOrderings']['orderDirection'])) {
            $demand->setOrderDirection($settings['productOrderings']['orderDirection']);
        }
        if (!empty($settings['categories'])) {
            $demand->setCategories($settings['categories']);
        }
        if (!empty($settings['categoryConjunction'])) {
            $demand->setCategoryConjunction($settings['categoryConjunction']);
        }
    }
}
