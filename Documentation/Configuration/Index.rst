.. include:: ../Includes.txt


.. _configuration:

Configuration Reference
=======================

Technical information: Installation, Reference of TypoScript options,
configuration options on system level, how to extend it, the technical
details, how to debug it and so on.

Language should be technical, assuming developer knowledge of TYPO3.
Small examples/visuals are always encouraged.

Target group: **Developers**


.. _configuration-typoscript:

TypoScript Reference
--------------------

Possible subsections: Reference of TypoScript options.


Properties
^^^^^^^^^^

Property details listView
^^^^^^^^^^^^^^^^^^^^^^^^^

.. only:: html

	.. contents::
		:local:
		:depth: 1


.. _ts-plugin-tx-pxaproductmanager-listView-limit:

settings.listView.limit
"""""""""""""""""""""""

:typoscript:`plugin.tx_pxaproductmanager.settings.listView.limit = 12`

Default number of products loaded in product list if not set in plugin settings.

.. _ts-plugin-tx-pxaproductmanager-listView-images-maxwidth:

settings.listView.images.maxWidth
"""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pxaproductmanager.settings.listView.images.maxWidth = 265`

Max width of product images in product list.

.. _ts-plugin-tx-pxaproductmanager-listView-images-maxheight:

settings.listView.images.maxHeight
""""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pxaproductmanager.settings.listView.images.maxHeight = 265`

Max height of product images in product list.

.. _ts-plugin-tx-pxaproductmanager-listView-additionalfields:

settings.listView.additionalFields
""""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pxaproductmanager.settings.listView.additionalFields =`

Additional product properties to return in XHR result for product list.
Comma separated list of additional field names to include (usp,tax_rate...).

.. _ts-plugin-tx-pxaproductmanager-listView-additionalattributes:

settings.listView.additionalAttributes
""""""""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pxaproductmanager.settings.listView.additionalAttributes =`

Additional product attributes to return in XHR result for product list.
Comma separated list of attribute identifiers to include (identifier1,identifier2...).

.. _ts-plugin-tx-pxaproductmanager-listView-includeemptyadditionalattributes:

settings.listView.includeEmptyAdditionalAttributes
""""""""""""""""""""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pxaproductmanager.settings.listView.includeEmptyAdditionalAttributes =`

Enable to include "empty" attributes in XHR result for product list.
Can be enabled if vue template depends on one of the additional attributes to avoid javascript error.

.. _ts-plugin-tx-pxaproductmanager-listView-menupageid:

settings.listView.menuPageId
""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pxaproductmanager.settings.listView.menuPageId =`

Option to override what page the menu in product list view should begin from.
Default is to use current page.

.. _ts-plugin-tx-pxaproductmanager-listView-menulevels:

settings.listView.menuLevels
""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pxaproductmanager.settings.listView.menuLevels =`

Option to override how many levels down the page menu will show, when menuPageId is set.

.. _ts-plugin-tx-pxaproductmanager-listView-menucollapsible:

settings.listView.menuCollapsible
"""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pxaproductmanager.settings.listView.menuCollapsible = 0`

Option to override and make menu collapsible, 0 = disabled, 1 = enabled collapsed and 2 = enabled expanded.

.. _ts-plugin-tx-pxaproductmanager-listView-menuitemcollapsible:

settings.listView.menuItemCollapsible
"""""""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pxaproductmanager.settings.listView.menuItemCollapsible =`

Decides if page menu items can be collapsed, 0 = disabled, 1 = enabled collapsed.

.. _ts-plugin-tx-pxaproductmanager-listView-filtercollapsible:

settings.listView.filterCollapsible
"""""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pxaproductmanager.settings.listView.filterCollapsible =`

Decides if filter is collapsible, 0 = disabled, 1 = enabled collapsed and 2 = enabled expanded.

.. _ts-plugin-tx-pxaproductmanager-listView-excludeuidlist:

settings.listView.excludeUidList
""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pxaproductmanager.settings.listView.excludeUidList =`

List of comma-separated page Id's that must be excluded from menu page tree.


Property details attributes
^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. only:: html

	.. contents::
		:local:
		:depth: 1


.. _ts-plugin-tx-pxaproductmanager-dateformat:

settings.attributes.dateFormat
""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pxaproductmanager.settings.attributes.dateFormat = %B %d %Y`

Sets date format in FE rendering of attributes of type datetime.


.. _ts-plugin-tx-pxaproductmanager-divideattributesbysets:

settings.attributes.divideAttributesBySets
""""""""""""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pxaproductmanager.settings.attributes.divideAttributesBySets = 1`

Sets if attributes will be divided by attribute sets in frontend rendering of attributes.


.. _ts-plugin-tx-pxaproductmanager-imagemaxsize:

settings.attributes.imageMaxSize
""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pxaproductmanager.settings.attributes.imageMaxSize = 50`

Sets image max size of attribute images/icons in FE rendering.

.. _ts-plugin-tx-pxaproductmanager-types-image-imagemaxwidth:

settings.attributes.types.image.maxWidth
""""""""""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pxaproductmanager.settings.attributes.types.image.maxWidth = 265`

Sets image max width in FE rendering attributes of type datetime.

Property details price
^^^^^^^^^^^^^^^^^^^^^^

.. only:: html

	.. contents::
		:local:
		:depth: 1

settings.price.currency
"""""""""""""""""""""""

.. _ts-plugin-tx-pxaproductmanager-currency:

:typoscript:`plugin.tx_pxaproductmanager.settings.price.currency = USD`

The 3-letter ISO 4217 currency code indicating the currency to use.

settings.price.fractionDigits
"""""""""""""""""""""""""""""

.. _ts-plugin-tx-pxaproductmanager-fractionDigits:

:typoscript:`plugin.tx_pxaproductmanager.settings.price.fractionDigits = 2`

Number of fraction digits in formatted price. Note, using 0 fraction digits might round rendered price!


.. _configuration-faq:

FAQ
---

Possible subsection: FAQ
