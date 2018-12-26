<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Updates;

use TYPO3\CMS\Install\Updates\AbstractUpdate;

/**
 * Class AttributesValuesUpdate
 * @package Pixelant\PxaProductManager\Updates
 */
class AttributesValuesUpdateCompatibility extends AbstractUpdate
{
    use AttributesValuesUpdateTrait;

    /**
     * Title of wizard
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->updateTitle;
    }

    /**
     * If should run
     *
     * @param string $description
     * @return bool
     */
    public function checkForUpdate(&$description)
    {
        if ($this->isWizardDone()) {
            return false;
        }

        $description = $this->updateDescription;
        return $this->updateNecessary();
    }

    /**
     * Perform update
     *
     * @param array $dbQueries
     * @param string $customMessage
     * @return bool
     */
    public function performUpdate(array &$dbQueries, &$customMessage)
    {
        $this->executeUpdate();

        $this->markWizardAsDone();
        return true;
    }
}
