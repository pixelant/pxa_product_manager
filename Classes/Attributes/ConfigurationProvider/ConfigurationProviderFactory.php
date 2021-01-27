<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ConfigurationProvider;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Repository\AttributeRepository;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AttributeConfigurationProviderFactory.
 */
class ConfigurationProviderFactory
{
    /**
     * Factory method.
     *
     * @param Attribute $attribute
     * @return ProviderInterface
     * @throws \UnexpectedValueException
     */
    public static function create(int $attributeId, array $attribute = null): ProviderInterface
    {
        if ($attribute === null) {
            $attribute = BackendUtility::getRecord(
                AttributeRepository::TABLE_NAME,
                $attributeId
            );
        }

        switch ($attribute['type']) {
            case Attribute::ATTRIBUTE_TYPE_INPUT:
                return GeneralUtility::makeInstance(InputProvider::class, $attribute);
            case Attribute::ATTRIBUTE_TYPE_TEXT:
                return GeneralUtility::makeInstance(TextAreaProvider::class, $attribute);
            case Attribute::ATTRIBUTE_TYPE_MULTISELECT:
                return GeneralUtility::makeInstance(SelectBoxProvider::class, $attribute);
            case Attribute::ATTRIBUTE_TYPE_CHECKBOX:
                return GeneralUtility::makeInstance(CheckboxProvider::class, $attribute);
            case Attribute::ATTRIBUTE_TYPE_LINK:
                return GeneralUtility::makeInstance(LinkProvider::class, $attribute);
            case Attribute::ATTRIBUTE_TYPE_FILE:
            case Attribute::ATTRIBUTE_TYPE_IMAGE:
                return GeneralUtility::makeInstance(FalProvider::class, $attribute);
            case Attribute::ATTRIBUTE_TYPE_DATETIME:
                return GeneralUtility::makeInstance(DateTimeProvider::class, $attribute);
            case Attribute::ATTRIBUTE_TYPE_LABEL:
                return GeneralUtility::makeInstance(LabelProvider::class, $attribute);
        }

        throw new \UnexpectedValueException(
            'Attribute with type "' . $attribute['type'] . '" not supported.',
            1568986135545
        );
    }
}
