<?php

namespace Pixelant\PxaProductManager\Event\Model\DTO;

use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;

class DemandEvent
{
    /**
     * @var DemandInterface
     */
    protected DemandInterface $demand;

    /**
     * @var array
     */
    protected array $settings;

    /**
     * DemandEvent constructor.
     * @param DemandInterface $demand
     * @param array $settings
     */
    public function __construct(DemandInterface $demand, array $settings)
    {
        $this->demand = $demand;
        $this->settings = $settings;
    }

    /**
     * @return DemandInterface
     */
    public function getDemand(): DemandInterface
    {
        return $this->demand;
    }

    /**
     * @param DemandInterface $demand
     */
    public function setDemand(DemandInterface $demand): void
    {
        $this->demand = $demand;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }
}
