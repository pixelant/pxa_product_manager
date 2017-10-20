lib.pxaProductManager {
    wishListCart = COA
    wishListCart {
        10 = USER
        10 {
            userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
            extensionName = PxaProductManager
            pluginName = Pi2
            vendorName = Pixelant

            switchableControllerActions {
                Product {
                    1 = wishListCart
                }
            }
        }
    }
}