<?php

namespace Pixelant\PxaProductManager\Event\Repository;

class RegisterAdditionalProductListReturnFieldsEvent
{
    /**
     * @var array
     */
    protected array $selectFields;

    /**
     * DemandEvent constructor.
     * @param array $selectFields
     */
    public function __construct(array $selectFields)
    {
        $this->selectFields = $selectFields;
    }

    /**
     * @return array
     */
    public function getSelectFields(): array
    {
        return $this->selectFields;
    }

    /**
     * @param array $settings
     */
    public function setSelectFields(array $selectFields): void
    {
        $this->selectFields = $selectFields;
    }

    /**
     * @param string $fieldName
     */
    public function addSelectField(string $fieldName): void
    {
        $this->selectFields[] = $fieldName;
    }
}
