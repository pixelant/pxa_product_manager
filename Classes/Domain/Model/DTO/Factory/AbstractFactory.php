<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model\DTO\Factory;

use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * @package Pixelant\PxaProductManager\Domain\Model\DTO\Factory
 */
abstract class AbstractFactory
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
     * @return DemandInterface
     */
    public function buildFromSettings(array $settings): DemandInterface
    {
        /** @var DemandInterface $demand */
        $demand = GeneralUtility::makeInstance($this->className($settings));

        if (!empty($settings['limit'])) {
            $demand->setLimit((int)$settings['limit']);
        }
        if (!empty($settings['offSet'])) {
            $demand->setOffSet((int)$settings['offSet']);
        }
        if (!empty($settings['demand']['orderByAllowed'])) {
            $demand->setOrderByAllowed($settings['demand']['orderByAllowed']);
        }

        $this->demandSpecial($demand, $settings);

        $this->dispatcher->dispatch(__CLASS__, 'AfterDemandCreationBeforeReturn', [$demand, $settings]);

        return $demand;
    }

    /**
     * Demand class
     *
     * @param array $settings
     * @return string
     */
    abstract protected function className(array $settings): string;

    /**
     * Special demand settings
     *
     * @param DemandInterface $demand
     * @param array $settings
     */
    abstract protected function demandSpecial(DemandInterface $demand, array $settings): void;
}
