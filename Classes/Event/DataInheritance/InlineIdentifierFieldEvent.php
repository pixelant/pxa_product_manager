<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\DataInheritance;

use Psr\EventDispatcher\StoppableEventInterface;

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
     * CalculateInlineFieldValueEvent constructor.
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
