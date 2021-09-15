<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\DataInheritance;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Event used for calculate values for inline fields.
 *
 * E.g. for sys_file_reference we need the uid of the sys_file it points at,
 * not the uid of the sys_file_reference it points to.
 *
 * These values can be used to  compare if an inherited field of a child product matches the parent product.
 */
class CalculateInlineFieldValueEvent implements StoppableEventInterface
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
     * @var string
     */
    protected $value = '';

    /**
     * @var bool
     */
    private $inlineFieldValueIsSet = false;

    /**
     * CalculateInlineFieldValueEvent constructor.
     * @param $property
     */
    public function __construct(string $table, int $id)
    {
        $this->table = $table;
        $this->id = $id;
        // Fallback to table_uid.
        $this->value = $table . '_' . $id;
    }

    public function isPropagationStopped(): bool
    {
        return $this->inlineFieldValueIsSet;
    }

    public function markInlineFieldValueIsSet(): void
    {
        $this->inlineFieldValueIsSet = true;
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
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return void
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
