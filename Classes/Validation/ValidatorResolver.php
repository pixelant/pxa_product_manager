<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Validation;

use Pixelant\PxaProductManager\Exception\Validation\NoSuchValidatorException;
use Pixelant\PxaProductManager\Validation\Validator\EmailValidator;
use Pixelant\PxaProductManager\Validation\Validator\RequiredValidator;
use Pixelant\PxaProductManager\Validation\Validator\UrlValidator;
use Pixelant\PxaProductManager\Validation\Validator\ValidatorInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ValidatorResolver
 */
class ValidatorResolver implements SingletonInterface
{
    /**
     * Registry of available validator
     *
     * @var array
     */
    protected static $validatorsRegistry = [
        'required' => RequiredValidator::class,
        'email' => EmailValidator::class,
        'url' => UrlValidator::class
    ];

    /**
     * Create validator instance
     *
     * @param string $type
     * @return ValidatorInterface
     */
    public function createValidator(string $type): ValidatorInterface
    {
        if (isset(self::$validatorsRegistry[$type])) {
            $validator = GeneralUtility::makeInstance(self::$validatorsRegistry[$type]);

            if ($validator instanceof ValidatorInterface) {
                return $validator;
            }
        }

        throw new NoSuchValidatorException(
            'The validator "' . $type . '" doesn\'t exist or doesn\'t implement ValidatorInterface',
            1534514683513
        );
    }

    /**
     * Register new validator or override existing
     * For use in ext_localconf.php
     *
     * @param string $type
     * @param string $validator
     */
    public static function registerValidator(string $type, string $validator)
    {
        self::$validatorsRegistry[$type] = $validator;
    }
}
