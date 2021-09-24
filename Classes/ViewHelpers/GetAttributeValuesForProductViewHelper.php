<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\ViewHelpers;

use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Utility\AttributeUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;
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

        if (!isset($arguments['uid'])){
            $arguments['uid'] = $arguments['product']->getUid();
        }

        if ($arguments['attributeIdentifiers']) {
            $attributeIdentifiers = $arguments['attributeIdentifiers'];
            $attributes = AttributeUtility::findAllAttributesForProduct($arguments['uid'], "uid,identifier");
            $filteredAttributes = array_filter($attributes, fn($attribute) => in_array($attributeIdentifiers, $attribute['identifier']));
            $attributeIds = array_column($filteredAttributes, 'uid');

            $attributeValues = AttributeUtility::findAttributeValues($arguments['uid'], $attributeIds);

        } elseif ($arguments['attributeUids']) {
            $attributeUids = $arguments['attributeUids'];
            $attributes = AttributeUtility::findAllAttributesForProduct($arguments['uid'], "uid");
            $filteredAttributes = array_filter($attributes, fn($attribute) => in_array($attributeUids, $attribute['uid']));

            $attributeValues = AttributeUtility::findAttributeValues($arguments['uid'], $filteredAttributes);

        } else {
            $attributes = AttributeUtility::findAllAttributesForProduct($arguments['uid'], "uid");
            $attributeValues = AttributeUtility::findAttributeValues($arguments['uid'], $attributes);
        }

            if ($attributeValues) {
                foreach ($attributeValues as $key => $attributeValue){
                    $attributeValues[$key]['renderValue'] = AttributeUtility::getAttributeValueRenderValue($attributeValue['uid']);
                }

                return $attributeValues;
            }

        return [];
    }
}
