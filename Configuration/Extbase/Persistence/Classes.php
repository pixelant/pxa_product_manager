<?php
declare(strict_types=1);

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
    \Pixelant\PxaProductManager\Domain\Model\Category::class => [
        'tableName' => 'sys_category',
        'properties' => [
            'products' => [
                'fieldName' => 'pxapm_products',
            ],
            'attributesSets' => [
                'fieldName' => 'pxapm_attributes_sets',
            ],
            'description' => [
                'fieldName' => 'pxapm_description',
            ],
            'image' => [
                'fieldName' => 'pxapm_image',
            ],
            'bannerImage' => [
                'fieldName' => 'pxapm_banner_image',
            ],
            'subCategories' => [
                'fieldName' => 'pxapm_subcategories',
            ],
            'taxRate' => [
                'fieldName' => 'pxapm_tax_rate',
            ],
            'contentPage' => [
                'fieldName' => 'pxapm_content_page',
            ],
            'contentColPos' => [
                'fieldName' => 'pxapm_content_colpos',
            ],
            'hiddenInNavigation' => [
                'fieldName' => 'pxapm_hidden_in_navigation',
            ],
            'hideProducts' => [
                'fieldName' => 'pxapm_hide_products',
            ],
            'hideSubCategories' => [
                'fieldName' => 'pxapm_hide_subcategories',
            ],
            'alternativeTitle' => [
                'fieldName' => 'pxapm_alternative_title',
            ],
            'metaDescription' => [
                'fieldName' => 'pxapm_meta_description',
            ],
            'keywords' => [
                'fieldName' => 'pxapm_keywords',
            ],
        ],
    ],
];
