<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Validation\Validator;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class AbstractValidator
 * @package Pixelant\PxaProductManager\Validation\Validator
 */
abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * Translate error key
     *
     * @var string
     */
    protected $errorKey = 'fe.validation_error.general';

    /**
     * Translate error message
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        return LocalizationUtility::translate($this->errorKey, 'PxaProductManager') ?? '';
    }

    /**
     * Validate
     */
    abstract public function validate($value): bool;
}
