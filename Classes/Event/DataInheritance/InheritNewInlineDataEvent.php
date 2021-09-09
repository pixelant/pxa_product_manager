<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\DataInheritance;

class InheritNewInlineDataEvent
{
    /**
     * @var string
     */
    protected $table = '';

    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @var int
     */
    protected $identifier = 0;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * InheritNewInlineDataEvent constructor.
     * @param string $table
     * @param int $id
     * @param int $identifier
     */
    public function __construct(string $table, int $id, int $identifier)
    {
        $this->table = $table;
        $this->id = $id;
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getIdentifier(): int
    {
        return $this->identifier;
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
     * @return void
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
