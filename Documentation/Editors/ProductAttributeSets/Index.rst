.. include:: ../../Includes.txt

.. _product-attribute-sets:

=======================
Product Attribute sets
=======================

To add attributes to the products, you must create an 'Attribute set' in which you specify the attributes.
This can then be selected in product types and automatically add attributes to products connected to that product type.

Add new attribute set
=======================

To create a new attribute set, go to List view and select the product manager (or equivalent) folder in the page tree.
Either click "Create new record" in top of page or "New record" in the Attribute set section.

Fields
=======================

This is a short explanation of the "non standard" TYPO3 fields.

.. container:: table-row

   Field
        Name
   Description
        Name of attribute set, displayed in FE if ``plugin.tx_pxaproductmanager.settings.attributes.divideAttributesBySets`` is enabled.

.. container:: table-row

   Field
        Layout
   Description
        Layout (Partial) to use when Attribute set is rendered. Only "Default" is available in extension.
        To add custom layouts see: :ref:`Custom Layouts <attributeset_customlayout>`

.. container:: table-row

   Field
        Attributes
   Description
        Used to select what attributes to include with this Attribute set.

.. container:: table-row

   Field
        Product types
   Description
        Used to select the Product types Attribute set is included in.
