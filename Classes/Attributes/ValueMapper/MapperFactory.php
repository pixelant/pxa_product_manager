<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MapperFactory
{
    /**
     * Factory method create adapter depend on attribute.
     *
     * @param AttributeValue $attributeValue
     * @return MapperInterface
     */
    public function create(AttributeValue $attributeValue): MapperInterface
    {
        return GeneralUtility::makeInstance($this->detectMapperName($attributeValue));
    }

    /**
     * Detect mapper based on attribute.
     *
     * @param AttributeValue $attributeValue
     * @return string
     */
    protected function detectMapperName(AttributeValue $attributeValue): string
    {
        if ($attributeValue->getAttribute()->isFalType()) {
            return FalMapper::class;
        }
        if ($attributeValue->getAttribute()->isSelectBoxType()) {
            return SelectBoxMapper::class;
        }

        return GeneralMapper::class;
    }
}
