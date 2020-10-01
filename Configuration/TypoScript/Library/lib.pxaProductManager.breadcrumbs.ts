lib.pxaProductManager {
    breadcrumbs = COA
    breadcrumbs {
        10 = HMENU
        10 {
            special = rootline
            special.range = 0:-1

            1 = TMENU
            1 {
                NO = 1
                NO {
                    wrapItemAndSub = <li class="breadcrumbs__list-item">|</li>
                }
            }
        }

        50 = HMENU
        50 {
            special = userfunction
            special {
                userFunc = Pixelant\PxaProductManager\Navigation\BreadcrumbsBuilder->buildBreadcrumbs
            }
        }

        50.1 < .10.1
        50 {
            1 {
                CUR = 1
                CUR {
                    wrapItemAndSub = <li class="breadcrumbs__list-item _active">|</li>
                    doNotLinkIt = 1
                }
            }
        }

        wrap = <div class="breadcrumbs"><ol class="breadcrumbs__list">|</ol></div>
    }
}
