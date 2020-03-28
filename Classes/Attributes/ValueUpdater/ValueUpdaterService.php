<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueUpdater;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Repository\AttributeRepository;
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
     * @var AttributeRepository
     */
    protected AttributeRepository $attributeRepository;

    /**
     * @param AttributeRepository $attributeRepository
     */
    public function injectAttributeRepository(AttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

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
        // Prepare value for updating
        $value = $this->convertValue($attribute, $value);
        
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
     * Convert given value depending on attribute
     *
     * @param $attribute
     * @param $value
     * @return mixed
     */
    protected function convertValue($attribute, $value)
    {
        $attribute = $this->getAttributeEntity($attribute);

        // Wrap with for options list
        if ($attribute->isSelectBoxType()) {
            return sprintf(',%s,', $value);
        }

        return $value;
    }

    /**
     * If given attribute is uid - find attribute
     *
     * @param $attribute
     * @return Attribute
     */
    protected function getAttributeEntity($attribute): Attribute
    {
        if (! is_object($attribute)) {
            return $this->attributeRepository->findByUid((int)$attribute);
        }

        return $attribute;
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
