<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class OrderFormField
 * @package Pixelant\PxaProductManager\Domain\Model
 */
class OrderFormField extends AbstractDomainObject
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
     * Value of field
     *
     * @var string
     */
    protected $value = '';

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
    protected $validationRules = [];

    /**
     * Validation errors
     *
     * @var array
     */
    protected $errors = [];

    /**
     * @lazy
     * @cascade
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Option>
     */
    protected $options = [];

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->options = new ObjectStorage();
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
     * Adds a Option
     *
     * @param Option $option
     * @return void
     */
    public function addOption(Option $option)
    {
        $this->options->attach($option);
    }

    /**
     * Removes a Option
     *
     * @param Option $optionToRemove The Option to be removed
     * @return void
     */
    public function removeOption(Option $optionToRemove)
    {
        $this->options->detach($optionToRemove);
    }

    /**
     * Returns the options
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Option> $options
     */
    public function getOptions(): ObjectStorage
    {
        return $this->options;
    }

    /**
     * Sets the options
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Option> $options
     * @return void
     */
    public function setOptions(ObjectStorage $options)
    {
        $this->options = $options;
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
}
