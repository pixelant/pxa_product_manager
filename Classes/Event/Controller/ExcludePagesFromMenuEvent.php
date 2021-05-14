<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\Controller;

class ExcludePagesFromMenuEvent
{
    /**
     * @var string
     */
    protected string $excludeUidList = '';

    /**
     * ExcludePagesFromMenuEvent constructor.
     * @param string $excludeUidList
     */
    public function __construct(string $excludeUidList)
    {
        $this->excludeUidList = $excludeUidList;
    }

    /**
     * @return string
     */
    public function getExcludeUidList(): string
    {
        return $this->excludeUidList;
    }

    /**
     * @param string $exludeUidList
     * @return ExcludePagesFromMenuEvent
     */
    public function setExcludeUidList(string $excludeUidList): self
    {
        $this->excludeUidList = $excludeUidList;

        return $this;
    }
}
