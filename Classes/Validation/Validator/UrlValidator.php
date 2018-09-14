<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Validation\Validator;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class UrlValidator
 * @package Pixelant\PxaProductManager\Validation\Validator
 */
class UrlValidator extends AbstractValidator
{
    /**
     * Error key
     *
     * @var string
     */
    protected $errorKey = 'fe.validation_error.ulr';

    /**
     * Validate url
     *
     * @param $value
     * @return bool
     */
    public function validate($value): bool
    {
        return GeneralUtility::isValidUrl($value);
    }
}
