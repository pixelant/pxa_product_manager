<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MapperFactory
{
    /**
     * Factory method create adapter depend on attribute.
     *
     * @param Attribute $attribute
     * @return MapperInterface
     */
    public function create(Attribute $attribute): MapperInterface
    {
        return GeneralUtility::makeInstance($this->detectMapperName($attribute));
    }

    /**
     * Detect mapper based on attribute.
     *
     * @param Attribute $attribute
     * @return string
     */
    protected function detectMapperName(Attribute $attribute): string
    {
        if ($attribute->isFalType()) {
            return FalMapper::class;
        }
        if ($attribute->isSelectBoxType()) {
            return SelectBoxMapper::class;
        }

        return GeneralMapper::class;
    }
}
