<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model\OrderFormFields;

use Pixelant\PxaProductManager\Domain\Model\Option;
use Pixelant\PxaProductManager\Domain\Model\OrderFormField;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class SelectboxFormField
 * @package Pixelant\PxaProductManager\Domain\Model
 */
class SelectBoxFormField extends OrderFormField
{
    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Option>
     */
    protected $options = [];

    /**
     * __construct
     */
    public function __construct()
    {
        parent::__construct();
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
    public function getValueAsText(): string
    {
        $optionUid = (int)$this->value;
        /** @var Option $option */
        foreach ($this->getOptions() as $option) {
            if ($option->getUid() === $optionUid) {
                return $option->getValue();
            }
        }

        return '';
    }
}
