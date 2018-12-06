<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model\OrderFormFields;

use Pixelant\PxaProductManager\Domain\Model\OrderFormField;

/**
 * Class CheckBoxFormField
 * @package Pixelant\PxaProductManager\Domain\Model\OrderFormFields
 */
class CheckBoxFormField extends OrderFormField
{
    /**
     * @return string
     */
    public function getValueAsText(): string
    {
        $key = 'fe.checkbox.' . (int)$this->getValue();

        return $this->translateKey($key) ?? '';
    }
}
