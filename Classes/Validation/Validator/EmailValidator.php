<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Validation\Validator;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class EmailValidator
 * @package Pixelant\PxaProductManager\Validation\Validator
 */
class EmailValidator extends AbstractValidator
{
    /**
     * Error key
     *
     * @var string
     */
    protected $errorKey = 'fe.validation_error.email';

    /**
     * Validate email address
     * Empty field is valid, use required to validator instead
     *
     * @param $value
     * @return bool
     */
    public function validate($value): bool
    {
        return empty($value) || GeneralUtility::validEmail($value);
    }
}
