# Pixelant

## Pixelant Product Manager (pxa_product_manager)
This is an extension that shows products with a lot of filter and sorting possibilities.

## Documentation

For all kind of documentation which covers install to how to develop the extension:

| Source           | URL                                                                |
|------------------|--------------------------------------------------------------------|
| **Repository:**  | https://github.com/pixelant/pxa_product_manager                    |
| **Read online:** | https://docs.typo3.org/p/pixelant/pxa-product-manager/master/en-us |
| **TER:**         | https://extensions.typo3.org/extension/pxa_product_manager         |

## Developing the extension

### Installation

Install the composer dependencies:

    composer install

### Workflow

It's not allowed to push directly to master branch.
All feature implementing and bugs fixing should be done using pull request.

### Commands

For working with the extension, the following can be run to accomplish common tasks.

To run the PHP linter:

    composer run ci:php:lint

To run the PHP code style fixer:

    composer run ci:php:codestyle

To run the PHP codesniffer:

    composer run ci:php:sniff

To run the JSON linter:

    composer run ci:json:lint

To run the YAML linter:

    composer run ci:yaml:lint

To run the TypoScript linter:

    composer run ci:ts:lint

To run the PHP Unit tests run the following command:

    composer run ci:tests:unit

To run the PHP Functional tests run the following command:

    composer run ci:tests:functional

### Filter interface

The filtering options in the Product listing are made in [Vue.js](https://vuejs.org/).

Path to the [application](Resources/Private/product_manager) and more
information regarding the [workflow](Resources/Private/product_manager/README.md).

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
