# Pixelant

## Pixelant Product Manager (pxa_product_manager)
This is an extension that shows products with a lot of filter and sorting possibilities.

https://docs.google.com/document/d/1WuOBeH5oJ8b_yLczXVZkPRhiR12IjCa6ltb4IBuvvDk/edit

## Changelog
# Version ~ 2
- Full extension rebuild
- Use new hook to generate TCA data, no need to clear cache anymore
- Attribute type with FAL files support
- CKEditor products and categories linking
- Improvements for Solr search
- Support of product links with category tree in URL "category1/sub-category/product/product-name"
- Wish list
- Compare list
- Grouped list
- Breadcrumbs
- Divide TCA tabs according to attribute sets
- Functional tests
- Support of TYPO3 >= 8.7
- PHP version >= 7.0

## Installation

We rely on node.js for a lot of our tooling. So if you haven't got it installed(shame on you!!) go to http://nodejs.org/ and fetch it.

To install tooling dependencies, run:

    npm install

Then install the composer dependencies:

    composer install

## Workflow

It's not allowed to push directly to master branch.
All feature implementing and bugs fixing should be done using pull request.

### Test

For working with the extension, the following can be run to accomplish common tasks.

To run the PHP codesniffer run the following command:

    npm run php:codesniffer

To run the PHP Unit tests run the following command:

    npm run php:unittests

To run the PHP Functional tests run the following command:

    npm run php:functionaltests

To simulate the build process without functional tests locally, then run this packaged command:

    npm run build:suite_no_functional --silent

To simulate the full build process locally, then run this packaged command:

    npm run build:suite --silent

To watch and compile the main .less file to .css:

    npm run watch:css

When using t3kit and themes with the less compiler, three variables needs to be set (otherwise, the .less file will not be compiled):
  @main-color
  @main-text-color
  @border-color

### Products preview

Example of Page TS that allow to enable preview function of product.

```typo3_typoscript
TCEMAIN.preview {
    tx_pxaproductmanager_domain_model_product {
        useCacheHash = 1
        previewPageId = 1
        useDefaultLanguageRecord = 0
        fieldToParameterMap {
            uid = tx_pxaproductmanager_pi1[product_preview]
        }
        additionalGetParameters {
            tx_pxaproductmanager_pi1.controller = Product
            tx_pxaproductmanager_pi1.action = show
        }
    }
}
```

```typo3_typoscript
config.tx_demander {
    properties {
        property_name {
            table = tx_tablename_domain_model_blah
            field = pid
            operator = <
            additionalRestriction {
                tablename-filedname {
                    operator = =
                    value = 4
                }
            }
        }

        property_name2 {
            table = tx_tablename2_domain_model_blah
            field = uid
            operator = in
            additionalRestriction {
                tablename-fieldname {
                    operator = -
                    value {
                        0 = 2
                        1 = 4
                    }
                }
            }
        }

        property-name3 {
            table = tx_tablename3_domain_model_blah
            field = uid
            operator = =
        }
    }

    demands {
        property-name.value = 4

        or {
            property-name2.value = {
                0 = 3
                1 = 6
            }

            property-name3.value = 6
        }
    }
}
```
