<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Validation\Validator;

/**
 * Interface ValidatorInterface
 * @package Pixelant\PxaProductManager\Validation\Validator
 */
interface ValidatorInterface
{
    /**
     * Check if given value is valid
     *
     * @param $value
     * @return bool
     */
    public function validate($value): bool;

    /**
     * Get error message if validation fails
     *
     * @return string
     */
    public function getErrorMessage(): string;
}
