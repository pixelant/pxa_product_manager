<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Configuration\AttributesTCA;


/**
 * Class AttributeConfigurationProviderFactory
 * @package Pixelant\PxaProductManager\Configuration\AttributesTCA
 */
class ConfigurationProviderFactory
{
    /**
     * Factory method
     *
     * @param Attribute $attribute
     * @return ConcreteProviderInterface
     */
    public static function create(Attribute $attribute): ConcreteProviderInterface
    {
        switch (true) {
            case $attribute->isInputType():
                return GeneralUtility::makeInstance(InputProviderConcrete::class, $attribute);
            case $attribute->isSelectBoxType():
                return GeneralUtility::makeInstance(SelectBoxProviderConcrete::class, $attribute);
            case $attribute->isCheckboxType():
                return GeneralUtility::makeInstance(CheckboxProviderConcrete::class, $attribute);
            case $attribute->isLinkType():
                return GeneralUtility::makeInstance(LinkProviderConcrete::class, $attribute);
            case $attribute->isFalType():
                return GeneralUtility::makeInstance(FalProviderConcrete::class, $attribute);
        }

        throw new \UnexpectedValueException("Attribute with type '{$attribute->getType()}' not supported.", 1568986135545);
    }
}
