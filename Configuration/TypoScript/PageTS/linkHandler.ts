TCEMAIN.linkHandler {
    pxappm_product {
        handler = Pixelant\PxaProductManager\LinkHandler\CKEditorProductLinkHandler
        label = LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_be.xlf:be.product_link_handler

        scanBefore = url
    }

    pxappm_category {
        handler = Pixelant\PxaProductManager\LinkHandler\CKEditorCategoryLinkHandler
        label = LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_be.xlf:be.category_link_handler

        scanBefore = pxappm_product
    }
}