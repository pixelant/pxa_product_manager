.. include:: ../../Includes.txt


.. _product-type:

============
Product Type
============

Product types or "product families" are used to define what attribute sets that is available for products of this type.

.. _product-type-new:

Add new product type
====================

To create a new product type, go to List view and select the product manager (or equivalent) folder in the page tree.
Either click "Create new record" in top of page or "New record" in the Product type section.

.. _product-type-fields:

Fields
======

This is a short explanation of the "non standard" TYPO3 fields.

.. container:: table-row

   Field
        Name
   Description
        Name of product type, it is only used in BE as identifier.

.. container:: table-row

    Field
        Attribute sets
    Description
        Used to select what attribute-sets products of this type should contain.

.. container:: table-row

    Field
        Fields inherited by child products
    Description
        Select fields and attributes that should be inherited by child products.
        See: :ref:`product-inheritance`
