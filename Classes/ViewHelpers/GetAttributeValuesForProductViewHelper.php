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
        $this->registerArgument('uid', 'string', 'uid', false, null);
        $this->registerArgument('attributeIdentifiers', 'array', 'attributesIdentifiers', false, null);
        $this->registerArgument('attributeUids', 'array', 'attributesUids', false, null);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): array {
        if (!isset($arguments['uid'])) {
            $arguments['uid'] = $arguments['product']->getUid();
        }

        if ($arguments['attributeIdentifiers']) {
            $attributes = AttributeUtility::findAttributesForProduct(
                $arguments['uid'],
                [],
                $arguments['attributeIdentifiers'],
                'uid,identifier'
            );
            $attributeIds = array_column($attributes, 'uid');

            $attributeValues = AttributeUtility::findAttributeValues($arguments['uid'], array_keys($attributeIds));
        } elseif ($arguments['attributeUids']) {
            $attributes = AttributeUtility::findAttributesForProduct(
                $arguments['uid'],
                $arguments['attributeUids'],
                [],
                'uid'
            );
            $attributeValues = AttributeUtility::findAttributeValues($arguments['uid'], array_keys($attributes));
        } else {
            $attributes = AttributeUtility::findAttributesForProduct($arguments['uid'], [], [], 'uid');
            $attributeValues = AttributeUtility::findAttributeValues($arguments['uid'], array_keys($attributes));
        }

        if ($attributeValues) {
            foreach ($attributeValues as $key => $attributeValue) {
                $attributeValues[$key]['renderValue'] = AttributeUtility::getAttributeValueRenderValue(
                    $attributeValue['uid']
                );
            }

            return $attributeValues;
        }

        return [];
    }
}
