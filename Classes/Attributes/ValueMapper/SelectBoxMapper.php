<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Option;
use Pixelant\PxaProductManager\Domain\Model\Product;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Set attribute options as values
 */
class SelectBoxMapper extends AbstractMapper
{
    /**
     * @inheritDoc
     */
    public function map(Product $product, Attribute $attribute): void
    {
        if ($attributeValue = $this->searchAttributeValue($product, $attribute)) {
            $attribute->setValue(array_values(array_filter(
                $attribute->getOptions()->toArray(),
                fn(Option $option) => GeneralUtility::inList($attributeValue->getValue(), $option->getUid())
            )));
        }
    }
}
