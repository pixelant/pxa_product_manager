lib.pxaProductManager {
    compareListCart = COA
    compareListCart {
        10 = USER
        10 {
            userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
            extensionName = PxaProductManager
            pluginName = Pi2
            vendorName = Pixelant

            switchableControllerActions {
                Product {
                    1 = compareListCart
                }
            }

            settings.isMainCart = true
        }
    }
}