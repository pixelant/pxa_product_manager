<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Validation\Validator;

/**
 * Class RequiredValidator
 * @package Pixelant\PxaProductManager\Validation\Validator
 */
class RequiredValidator extends AbstractValidator
{
    /**
     * Error key
     *
     * @var string
     */
    protected $errorKey = 'fe.validation_error.required';

    /**
     * Check if string is not empty
     *
     * @param $value
     * @return bool
     */
    public function validate($value): bool
    {
        return !empty($value);
    }
}
