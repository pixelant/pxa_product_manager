<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\Extbase\Persistence\Generic\Qom;

use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\PropertyValueInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\StaticOperandInterface;

/**
 * Attributes Min and Max range
 *
 * @package Pixelant\PxaProductManager\Backend\Extbase\Persistence\Generic\Qom
 */
interface AttributesRangeInterface extends ConstraintInterface
{
    /**
     * Gets the minimum value.
     *
     * @return StaticOperandInterface the operand;
     */
    public function getMin();

    /**
     * Gets the maximum value
     *
     * @return StaticOperandInterface the operand;
     */
    public function getMax();

    /**
     * Gets the operand.
     *
     * @return PropertyValueInterface
     */
    public function getOperand();
}
