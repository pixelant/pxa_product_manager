<?php
declare(strict_types = 1);

return [
    \Pixelant\PxaProductManager\Domain\Model\Image::class => [
        'tableName' => 'sys_file_reference',
        'properties' => [
            'type' => [
                'fieldName' => 'pxapm_type',
            ],
        ],
    ],
    \Pixelant\PxaProductManager\Domain\Model\AttributeFile::class => [
        'tableName' => 'sys_file_reference',
        'properties' => [
            'attribute' => [
                'fieldName' => 'pxa_attribute',
            ],
        ],
    ],
];
