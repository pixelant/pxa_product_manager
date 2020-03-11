TCEMAIN.linkHandler {
    pxappm_product {
        handler = Pixelant\PxaProductManager\LinkHandler\ProductLinkHandler
        label = LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_be.xlf:be.product_link_handler

        scanBefore = url
    }

    pxappm_category {
        handler = Pixelant\PxaProductManager\LinkHandler\CategoryLinkHandler
        label = LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_be.xlf:be.category_link_handler

        scanBefore = pxappm_product
    }
}
