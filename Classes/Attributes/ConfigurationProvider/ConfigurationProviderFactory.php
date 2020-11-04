<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ConfigurationProvider;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
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
     */
    public static function create(Attribute $attribute): ProviderInterface
    {
        switch (true) {
            case $attribute->isInputType():
                return GeneralUtility::makeInstance(InputProvider::class, $attribute);
            case $attribute->isTextArea():
                return GeneralUtility::makeInstance(TextAreaProvider::class, $attribute);
            case $attribute->isSelectBoxType():
                return GeneralUtility::makeInstance(SelectBoxProvider::class, $attribute);
            case $attribute->isCheckboxType():
                return GeneralUtility::makeInstance(CheckboxProvider::class, $attribute);
            case $attribute->isLinkType():
                return GeneralUtility::makeInstance(LinkProvider::class, $attribute);
            case $attribute->isFalType():
                return GeneralUtility::makeInstance(FalProvider::class, $attribute);
            case $attribute->isDateType():
                return GeneralUtility::makeInstance(DateTimeProvider::class, $attribute);
            case $attribute->isLabelType():
                return GeneralUtility::makeInstance(LabelProvider::class, $attribute);
        }

        throw new \UnexpectedValueException(
            "Attribute with type '{$attribute->getType()}' not supported.",
            1568986135545
        );
    }
}
