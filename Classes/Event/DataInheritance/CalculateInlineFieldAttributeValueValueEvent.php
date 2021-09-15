<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\DataInheritance;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Repository\AttributeRepository;
use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use Pixelant\PxaProductManager\Utility\DataInheritanceUtility;
use Pixelant\PxaProductManager\Utility\TcaUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Calculates value for attribute value.
 */
class CalculateInlineFieldAttributeValueValueEvent
{
    protected static array $attributeTypes;

    /**
     * Needs to also include attribute id to be able to match directly by attribute.x.
     * Sets event value to a combined string, containing attributeUid|Value.
     *
     * @param CalculatePropertyValueEvent $event
     * @return void
     */
    public function __invoke(CalculateInlineFieldValueEvent $event): void
    {
        if ($event->getTable() === AttributeValueRepository::TABLE_NAME) {
            $record = BackendUtility::getRecord(
                $event->getTable(),
                $event->getId(),
                'uid, attribute, value'
            ) ?? [];

            if (!empty($record)) {
                switch (self::getAttributeType((int)$record['attribute'])) {
                    case Attribute::ATTRIBUTE_TYPE_FILE:
                    case Attribute::ATTRIBUTE_TYPE_IMAGE:
                        $value = $record['attribute'] . '|' . implode(',', self::resolveInlineRelationTargets($record));

                        break;
                    default:
                        $value = $record['attribute'] . '|' . $record['value'];

                        break;
                }

                $event->setValue($value);
                $event->markInlineFieldValueIsSet();
            }
        }
    }

    /**
     * Get Attribute type by Attribute uid.
     *
     * @param int $attributeId
     * @return int
     */
    protected static function getAttributeType(int $attributeId): int
    {
        if (isset(self::$attributeTypes[$attributeId])) {
            return self::$attributeTypes[$attributeId];
        }

        $type = (int)BackendUtility::getRecord(
            AttributeRepository::TABLE_NAME,
            $attributeId,
            'type'
        )['type'] ?? 0;

        self::$attributeTypes[$attributeId] = $type;

        return self::$attributeTypes[$attributeId];
    }

    /**
     * Resolve inline targets.
     *
     * @param array $record AttributeValue record
     * @return array
     */
    protected static function resolveInlineRelationTargets(array $record): array
    {
        $resolvedItemsList = [];

        $fieldTcaConfiguration = TcaUtility::getTcaFieldConfigurationAndRespectColumnsOverrides(
            AttributeValueRepository::TABLE_NAME,
            'value',
            $record
        );

        if ($fieldTcaConfiguration['type'] === 'inline') {
            $resolvedItems = DataInheritanceUtility::getRelationHandlerResolvedItems(
                $fieldTcaConfiguration,
                (int)$record['uid'],
                AttributeValueRepository::TABLE_NAME
            );

            if (!empty($resolvedItems)) {
                foreach ($resolvedItems as $resolvedItem) {
                    $resolvedItemsList[] = BackendUtility::getRecord(
                        $resolvedItem['table'],
                        $resolvedItem['uid'],
                        'uid_local'
                    )['uid_local'] ?? '';
                }
            }
        }

        return $resolvedItemsList;
    }
}
