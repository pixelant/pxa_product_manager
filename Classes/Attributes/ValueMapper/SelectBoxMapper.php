<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Option;
use Pixelant\PxaProductManager\Utility\AttributeUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Set attribute options as values.
 */
class SelectBoxMapper extends AbstractMapper
{
    /**
     * {@inheritdoc}
     */
    public function map(AttributeValue $attributeValue): void
    {
        if ($attributeValue) {
            $selectedOptions = array_filter(
                $attributeValue->getAttribute()->getOptions()->toArray(),
                function (Option $option) use ($attributeValue) {
                    return GeneralUtility::inList($attributeValue->getValue(), $option->getUid());
                }
            );
            $attributeValue->setArrayValue(array_values($selectedOptions));
        }
    }

    /**
     * Same functionality as map() but exclude Extbase entities.
     *
     * @param string $attributeValueValue
     * @param int $attribute
     * @return array
     */
    public function mapToArray(string $attributeValueValue, int $attribute): array
    {
        $selectedOptions = array_filter(
            AttributeUtility::findAttributeOptions($attribute),
            function (array $option) use ($attributeValueValue) {
                return GeneralUtility::inList($attributeValueValue, $option['uid']);
            }
        );

        return array_values($selectedOptions);
    }
}
