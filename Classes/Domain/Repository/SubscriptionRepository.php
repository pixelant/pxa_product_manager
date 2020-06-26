<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Repository;

use Pixelant\PxaProductManager\Utility\MainUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class SubscriptionRepository
 * @package Pixelant\PxaProductManager\Domain\Repository
 */
class SubscriptionRepository extends Repository
{
    /**
     * initializeObject
     */
    public function initializeObject()
    {
        $defaultQuerySettings = MainUtility::getObjectManager()->get(Typo3QuerySettings::class);
        $defaultQuerySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($defaultQuerySettings);
    }
}
