<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Updates;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * Class AttributesValuesUpdate
 * @package Pixelant\PxaProductManager\Updates
 */
class AttributesValuesUpdate implements UpgradeWizardInterface
{
    /**
     * Upgrade identifier
     *
     * @var string
     */
    public static $identifier = 'tx_pxaproductmanager_attributes_values';

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
        return 'Migrate serialized attribute values from "serialized_attributes_values" to JSON "attributes_values".';
    }

    /**
     * Return the description for this wizard
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Before Product Manager was saving attribute values as serialized values, now as JSON.';
    }

    /**
     * Execute the update
     *
     * Called when a wizard reports that an update is necessary
     *
     * @return bool
     */
    public function executeUpdate(): bool
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_pxaproductmanager_domain_model_product');

        $queryBuilder->getRestrictions()->removeAll();

        $statement = $queryBuilder
            ->select('uid', 'serialized_attributes_values')
            ->from('tx_pxaproductmanager_domain_model_product')
            ->execute();

        while ($row = $statement->fetch()) {
            $attributeValues = unserialize($row['serialized_attributes_values'] ?? '');

            if (!is_array($attributeValues)) {
                continue;
            }

            GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable('tx_pxaproductmanager_domain_model_product')
                ->update(
                    'tx_pxaproductmanager_domain_model_product',
                    [
                        'attributes_values' => json_encode($attributeValues)
                    ],
                    ['uid' => $row['uid']],
                    [\PDO::PARAM_STR]
                );
        }

        return true;
    }

    /**
     * Is an update necessary?
     *
     * Is used to determine whether a wizard needs to be run.
     * Check if data for migration exists.
     *
     * @return bool
     */
    public function updateNecessary(): bool
    {
        $tableColumns = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_pxaproductmanager_domain_model_product')
            ->getSchemaManager()
            ->listTableColumns('tx_pxaproductmanager_domain_model_product');

        return isset($tableColumns['serialized_attributes_values']) && isset($tableColumns['attributes_values']);
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
