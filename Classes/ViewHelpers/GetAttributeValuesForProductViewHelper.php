<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\ViewHelpers;

use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Utility\AttributeUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class GetAttributeValuesForProductViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('product', Product::class, 'Product', true, null);
        $this->registerArgument('attributesIdentifiers', 'array', 'attributesIdentifiers', false, null);
        $this->registerArgument('attributesUids', 'array', 'attributesUids', false, null);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): array {
        $product = $arguments['product'];
        $attributeValues = [];

        if ($arguments['attributesIdentifiers']) {
            $attributesIdentifiers = $arguments['attributesIdentifiers'];

            foreach ($product->getAttributes() as $attribute) {
                foreach ($attributesIdentifiers as $identifier) {
                    if ($attribute->getIdentifier() === $identifier) {
                        $attributeValue = AttributeUtility::findAttributeValue(
                            $product->getUid(),
                            $attribute->getUid()
                        );

                        if ($attributeValue) {
                            $attributeValue['renderValue']
                                = AttributeUtility::getAttributeValueRenderValue($attributeValue['uid']);
                            $attributeValues[$attribute->getIdentifier()] = $attributeValue;
                        }
                    }
                }
            }
        } elseif ($arguments['attributesUids']) {
            $attributesUids = $arguments['attributesUids'];

            foreach ($product->getAttributes() as $attribute) {
                foreach ($attributesUids as $uid) {
                    if ($attribute->getUid() === $uid) {
                        $attributeValue = AttributeUtility::findAttributeValue(
                            $product->getUid(),
                            $attribute->getUid()
                        );

                        if ($attributeValue) {
                            $attributeValue['renderValue']
                                = AttributeUtility::getAttributeValueRenderValue($attributeValue['uid']);
                            $attributeValues[$attribute->getUid()] = $attributeValue;
                        }
                    }
                }
            }

            return $attributeValues;
        }

        foreach ($product->getAttributes() as $attribute) {
            $attributeValue = AttributeUtility::findAttributeValue(
                $product->getUid(),
                $attribute->getUid()
            );

            if ($attributeValue) {
                $attributeValue['renderValue'] = AttributeUtility::getAttributeValueRenderValue($attributeValue['uid']);
                $attributeValues[$attribute->getIdentifier()] = $attributeValue;
            }
        }

        return $attributeValues;
    }
}
