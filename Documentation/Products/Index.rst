.. include:: ../Includes.txt



.. _products:


Products
========


.. _product-overview:

Product overview
----------------

The Product record overview in TYPO3 Backend.

.. figure:: ../Images/product-record-overview.png
   :alt: Product record overview in the TYPO3 backend.

   Product record overview in the TYPO3 backend.

.. _general-tab:

General tab
-----------

* Name - The name of the product
* SKU - a.k.a. article number, what uniquely identifies the product
* Description - Product description displayed on the product single view

.. _attributes-tab:

Attributes tab
--------------

This is dynamically generated tabs. Depending on category selected.
Each tab has title of its attribute set and listing attributes connected to
this attribute set.

.. _images-media-tab:

Images/Media tab
----------------

Images - The product images. Each image has two optional checkboxes

1. Is Main Product Image - Is displayed as the main image on the product single view
2. Use In Product Listing - Is used for product listing (grid layout as well as related products)

If a product can’t find an image with the checkbox checked it will just use the first image.

.. _relations-tab:

Relations tab
-------------

* Related products: To show related products on the product single view page
* Sub-products (Accessories): Sub-products are just other products, but are displayed in another section on the product single view page.
* Files: FAL objects associated with the products.
* Links: Links associated with the products can be files and/or internal/external pages, and are displayed on the product single view page.

.. _metadata-tab:

Metadata tab
------------

Meta description, keywords, alternative title and speaking URL path segment.

.. _access-tab:

Access tab
----------

Disable Single View: A checkbox to disable the linking of a product to the single view page.

.. _categories-tab:

Categories tab
-----------

Categories - The category the product belongs to. It uses the System Record Category
which exists natively in TYPO3.
