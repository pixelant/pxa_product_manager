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
"""""""""""""""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pxaproductmanager.settings.attributes.types.image.maxWidth = 265`

Sets image max width in FE rendering attributes of type datetime.

Property details price
^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. only:: html

	.. contents::
		:local:
		:depth: 1

settings.price.currency
"""""""""""""""""""""""""""""""""""""""""""""

.. _ts-plugin-tx-pxaproductmanager-currency:

:typoscript:`plugin.tx_pxaproductmanager.settings.price.currency = USD`

The 3-letter ISO 4217 currency code indicating the currency to use.

settings.price.fractionDigits
"""""""""""""""""""""""""""""""""""""""""""""

.. _ts-plugin-tx-pxaproductmanager-fractionDigits:

:typoscript:`plugin.tx_pxaproductmanager.settings.price.fractionDigits = 2`

Number of fraction digits in formatted price. Note, using 0 fraction digits might round rendered price!


.. _configuration-faq:

FAQ
---

Possible subsection: FAQ
