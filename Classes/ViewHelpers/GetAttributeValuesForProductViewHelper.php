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
        $this->registerArgument('attributeIdentifiers', 'array', 'attributesIdentifiers', false, null);
        $this->registerArgument('attributeUids', 'array', 'attributesUids', false, null);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): array {
        $product = $arguments['product'];
        $attributeValues = [];

        if ($arguments['attributeIdentifiers']) {
            $attributeIdentifiers = $arguments['attributeIdentifiers'];

            foreach ($product->getAttributes() as $attribute) {
                if (in_array($attribute->getIdentifier(), $attributeIdentifiers, true)) {
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
        } elseif ($arguments['attributeUids']) {
            $attributeUids = $arguments['attributeUids'];

            foreach ($product->getAttributes() as $attribute) {
                if (in_array($attribute->getUid(), $attributeUids, true)) {
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
