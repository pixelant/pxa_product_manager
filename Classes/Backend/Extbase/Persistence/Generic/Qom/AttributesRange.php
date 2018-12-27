<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\Extbase\Persistence\Generic\Qom;

use TYPO3\CMS\Extbase\Persistence\Generic\Qom\PropertyValueInterface;

/**
 * Attributes range constraint
 *
 * @package Pixelant\PxaProductManager\Backend\Extbase\Persistence\Generic\Qom
 */
class AttributesRange implements AttributesRangeInterface
{

    /**
     * @var PropertyValueInterface
     */
    protected $operand = null;

    /**
     * @var int
     */
    protected $min = null;

    /**
     * @var int
     */
    protected $max = null;

    /**
     * Initialize
     *
     * @param PropertyValueInterface $operand
     * @param int|null $min
     * @param int|null $max
     */
    public function __construct(PropertyValueInterface $operand, int $min = null, int $max = null)
    {
        $this->operand = $operand;
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return PropertyValueInterface
     */
    public function getOperand(): PropertyValueInterface
    {
        return $this->operand;
    }

    /**
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Fills an array with the names of all bound variables in the constraints
     *
     * @param array &$boundVariables
     */
    public function collectBoundVariableNames(&$boundVariables)
    {
    }
}
