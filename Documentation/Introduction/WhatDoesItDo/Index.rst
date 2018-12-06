.. include:: ../../Includes.txt

.. _what-does-it-do:

.. important::

    Please don't forget to repeat your extension's version number in the
    :file:`Settings.yml` file, in the :code:`release` property. It will be
    automatically picked up on the cover page by the :code:`|release|`
    substitution.


This chapter should give a brief overview of the extension. What does it do? What problems does it solve?
Who is interested in this? Basically, this section includes everything people need to know to decide whether they
should go on with this extension or not.


What does it do?
==================

Product manager is an extension for handling products in TYPO3 Backend. The frontend consists of next views:
    1. Simple listing
    2. Lazy loading list
    3. Lazy loading with filtering 
    4. Wish list
    5. Compare view
    6. Product single view.

.. _what-it-does:

Menus and breadcrumbs showing the product categories can be integrated using the functions provided by the extension.

The products
-------------
When editing products, the following fields are available in the different tabs:

.. figure:: ../../Images/Configuration/image10.png

General tab:
-------------
Name - The name of the product
SKU - a.k.a. article number, what uniquely identifies the product
Description - Product description displayed on the product single view

Categories tab:
----------------
Categories - The category the product belongs to. It uses the System Record Category which was introduced in TYPO3 6.0

Attributes tab:
----------------
This is dynamically generated tabs. Depending on category selected. Each tab has title of its attribute set and listing attributes connected to this attribute set.

Images tab:
--------------
Images - The product images. Each image has two optional checkboxes
1.  Is Main Product Image - Is displayed as the main image on the product single view
2.  Use In Product Listing - Is used for product listing (grid layout as well as related products)

If a product can’t find an image with the checkbox checked it will just use the first image.


Relations tab:
---------------
Related products: To show related products on the product single view page
Sub-products (Accessories): Sub-products are just other products, but are displayed in another section on the product single view page.
Files: FAL objects associated with the products.
Links: Links associated with the products can be files and/or internal/external pages, and are displayed on the product single view page.

Metadata tab:
--------------
Meta description, keywords, alternative title and speaking URL path segment

Access tab:
------------
Disable Single View: A checkbox to disable the linking of a product to the single view page.
