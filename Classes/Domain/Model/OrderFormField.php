<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class OrderFormField
 * @package Pixelant\PxaProductManager\Domain\Model
 */
abstract class OrderFormField extends AbstractDomainObject
{
    /**
     * Type simple input
     */
    const FIELD_INPUT = 1;

    /**
     * Type textarea
     */
    const FIELD_TEXTAREA = 2;

    /**
     * Type select box
     */
    const FIELD_SELECTBOX = 3;

    /**
     * Type check box
     */
    const FIELD_CHECKBOX = 4;

    /**
     * Name
     *
     * @var string
     */
    protected $name = '';

    /**
     * Label
     *
     * @var string
     */
    protected $label = '';

    /**
     * If if static, user can't change it value when filling in form
     *
     * @var bool
     */
    protected $static = false;

    /**
     * Placeholder
     *
     * @var string
     */
    protected $placeholder = '';

    /**
     * Type of field
     *
     * @var int
     */
    protected $type = 0;

    /**
     * @var string
     */
    protected $validationRules = '';

    /**
     * @var bool
     */
    protected $userEmailField = false;

    /**
     * Additional text
     *
     * @var string
     */
    protected $additionalText = '';

    /**
     * Value of field
     * Has no DB field
     *
     * @var string
     */
    protected $value = '';

    /**
     * Validation errors
     * Is not stored in DB
     *
     * @var array
     */
    protected $errors = [];

    /**
     * __construct
     */
    public function __construct()
    {
        // Anything ?
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isStatic(): bool
    {
        return $this->static;
    }

    /**
     * @param bool $static
     */
    public function setStatic(bool $static)
    {
        $this->static = $static;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * @param string $placeholder
     */
    public function setPlaceholder(string $placeholder)
    {
        $this->placeholder = $placeholder;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getValidationRules(): string
    {
        return $this->validationRules;
    }

    /**
     * Explode validation rules
     *
     * @return array
     */
    public function getValidationRulesArray(): array
    {
        return GeneralUtility::trimExplode(
            ',',
            $this->getValidationRules(),
            true
        );
    }

    /**
     * @param string $validationRules
     */
    public function setValidationRules(string $validationRules)
    {
        $this->validationRules = $validationRules;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
    }

    /**
     * @return bool
     */
    public function isUserEmailField(): bool
    {
        return $this->userEmailField;
    }

    /**
     * @param bool $userEmailField
     */
    public function setUserEmailField(bool $userEmailField)
    {
        $this->userEmailField = $userEmailField;
    }

    /**
     * @return string
     */
    public function getAdditionalText(): string
    {
        return $this->additionalText;
    }

    /**
     * @param string $additionalText
     */
    public function setAdditionalText(string $additionalText)
    {
        $this->additionalText = $additionalText;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @param string $error
     */
    public function addError(string $error)
    {
        $this->errors[] = $error;
    }

    /**
     * Translate key, wrapper for testing
     *
     * @param string $key
     * @return string
     */
    protected function translateKey(string $key): string
    {
        return LocalizationUtility::translate($key, 'PxaProductManager') ?? '';
    }

    /**
     * Get value depending on field type
     *
     * @return string
     */
    abstract public function getValueAsText(): string;
}
