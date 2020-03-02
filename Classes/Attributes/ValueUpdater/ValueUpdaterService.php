<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueUpdater;

use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;

/**
 * @package Pixelant\PxaProductManager\Attributes\ValueUpdater
 */
class ValueUpdaterService implements UpdaterInterface
{
    /**
     * @var AttributeValueRepository
     */
    protected AttributeValueRepository $attributeValueRepository;

    /**
     * @param AttributeValueRepository $attributeValueRepository
     */
    public function injectAttributeValueRepository(AttributeValueRepository $attributeValueRepository)
    {
        $this->attributeValueRepository = $attributeValueRepository;
    }

    /**
     * @inheritDoc
     */
    public function update($product, $attribute, $value): void
    {
        $product = $this->castToInt($product);
        $attribute = $this->castToInt($attribute);

        $attributeRow = $this->attributeValueRepository->findRawByProductAndAttribute($product, $attribute);
        if ($attributeRow) {
            $this->attributeValueRepository->updateValue($attributeRow['uid'], $value);
        } else {
            $this->attributeValueRepository->createWithValue($product, $attribute, $value);
        }
    }

    /**
     * Translate parameter to int if object given
     *
     * @param $value
     * @return int
     */
    protected function castToInt($value): int
    {
        if ($value instanceof AbstractDomainObject) {
            return $value->_getProperty('_localizedUid');
        }

        return (int)$value;
    }
}
