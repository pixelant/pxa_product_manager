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
     * Conversion table from attribute type to configuration provider class name.
     */
    protected const TYPE_TO_PROVIDER = [
        Attribute::ATTRIBUTE_TYPE_INPUT => InputProvider::class,
        Attribute::ATTRIBUTE_TYPE_TEXT => TextAreaProvider::class,
        Attribute::ATTRIBUTE_TYPE_MULTISELECT => SelectBoxProvider::class,
        Attribute::ATTRIBUTE_TYPE_DROPDOWN => SelectBoxProvider::class,
        Attribute::ATTRIBUTE_TYPE_CHECKBOX => CheckboxProvider::class,
        Attribute::ATTRIBUTE_TYPE_LINK => LinkProvider::class,
        Attribute::ATTRIBUTE_TYPE_FILE => FalProvider::class,
        Attribute::ATTRIBUTE_TYPE_IMAGE => FalProvider::class,
        Attribute::ATTRIBUTE_TYPE_DATETIME => DateTimeProvider::class,
        Attribute::ATTRIBUTE_TYPE_LABEL => LabelProvider::class,
    ];

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

        $className = self::TYPE_TO_PROVIDER[$attribute['type']];

        if ($className === null) {
            throw new \UnexpectedValueException(
                'Attribute with type "' . $attribute['type'] . '" not supported.',
                1568986135545
            );
        }

        return GeneralUtility::makeInstance($className, $attribute);
    }
}
