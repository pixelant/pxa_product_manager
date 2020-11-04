<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\Resource;

/**
 * Even data when convert entity to resource array.
 */
class ResourceToArray
{
    /**
     * @var array
     */
    protected array $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return ResourceToArray
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
