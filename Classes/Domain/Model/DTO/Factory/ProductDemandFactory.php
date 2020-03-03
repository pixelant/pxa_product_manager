<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model\DTO\Factory;

use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * @package Pixelant\PxaProductManager\Domain\Model\DTO\Factory
 */
class ProductDemandFactory
{
    /**
     * @var Dispatcher
     */
    protected Dispatcher $dispatcher;

    /**
     * @param Dispatcher $dispatcher
     */
    public function injectDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Create product demand from settings
     *
     * @param array $settings
     * @param string $className
     * @return DemandInterface
     */
    public function buildFromSettings(array $settings, string $className = null): DemandInterface
    {
        $className = !empty($className) && class_exists($className) ? $className : ProductDemand::class;

        /** @var ProductDemand $demand */
        $demand = GeneralUtility::makeInstance($className);

        if (!empty($settings['limit'])) {
            $demand->setLimit((int)$settings['limit']);
        }
        if (!empty($settings['offSet'])) {
            $demand->setOffSet((int)$settings['offSet']);
        }
        if (!empty($settings['productOrderings']['orderBy'])) {
            $demand->setOrderBy($settings['productOrderings']['orderBy']);
        }
        if (!empty($settings['productOrderings']['orderDirection'])) {
            $demand->setOrderDirection($settings['productOrderings']['orderDirection']);
        }
        if (!empty($settings['demand']['orderByAllowed'])) {
            $demand->setOrderByAllowed($settings['demand']['orderByAllowed']);
        }
        if (!empty($settings['categories'])) {
            $demand->setCategories($settings['categories']);
        }
        if (!empty($settings['categoryConjunction'])) {
            $demand->setCategoryConjunction($settings['categoryConjunction']);
        }

        $this->dispatcher->dispatch(__CLASS__, 'AfterDemandCreationBeforeReturn', [$demand, $settings]);

        return $demand;
    }
}
