<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Updates;

use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * Class AttributesValuesUpdate
 * @package Pixelant\PxaProductManager\Updates
 */
class AttributesValuesUpdate implements UpgradeWizardInterface
{
    use AttributesValuesUpdateTrait;

    /**
     * Return the identifier for this wizard
     * This should be the same string as used in the ext_localconf class registration
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return self::$identifier;
    }

    /**
     * Return the speaking name of this wizard
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->updateTitle;
    }

    /**
     * Return the description for this wizard
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->updateDescription;
    }

    /**
     * Returns an array of class names of Prerequisite classes
     *
     * This way a wizard can define dependencies like "database up-to-date" or
     * "reference index updated"
     *
     * @return string[]
     */
    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class
        ];
    }
}
