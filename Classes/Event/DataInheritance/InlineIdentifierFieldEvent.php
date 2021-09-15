<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\DataInheritance;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Event to get the identifier field name by table.
 *
 * Defaults is uid_local.
 */
class InlineIdentifierFieldEvent implements StoppableEventInterface
{
    /**
     * @var string
     */
    protected $table = '';

    /**
     * @var string
     */
    protected $field = '';

    /**
     * @var bool
     */
    protected $inlineIdentifierFieldIsSet = false;

    /**
     * @param $property
     */
    public function __construct(string $table)
    {
        $this->table = $table;
        $this->field = 'uid_local';
    }

    public function isPropagationStopped(): bool
    {
        return $this->inlineIdentifierFieldIsSet;
    }

    public function markInlineIdentifierFieldIsSet(): void
    {
        $this->inlineIdentifierFieldIsSet = true;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return void
     */
    public function setField(string $field): void
    {
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }
}
