<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction\Solr;

use Pixelant\PxaProductManager\Domain\Model\Option;

class AttributeMultiValue extends AbstractValue
{
    /**
     * Return string value of attribute.
     *
     * @param string $content
     * @param array $params
     * @return string
     * @throws \InvalidArgumentException
     */
    public function value(string $content, array $params): string
    {
        $attributeValue = $this->initValue($params);
        $value = '';

        if (!empty($attributeValue)) {
            $renderValue = $attributeValue->getRenderValue();

            if (is_array($renderValue)) {
                $arrayValue = [];
                foreach ($renderValue as $item) {
                    if ($item instanceof Option) {
                        $arrayValue[] = $item->getValue();
                    }
                }
                $arrayValue = array_unique($arrayValue);
                $value = $arrayValue;
            } else {
                $value = [$renderValue];
            }
        }

        return serialize($value);
    }
}
