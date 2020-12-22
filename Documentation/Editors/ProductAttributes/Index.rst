.. include:: ../../Includes.txt

.. _product-attribute:

.. include:: ../../Includes.txt

.. _product-attribute-sets:

=======================
Product Attributes
=======================

Attributes are used as additions to the standard product information (mainly name, article number, description and images)
and shown in a table below the general product information. Attributes can for example be available colors, sizes, measurements etc.

Add new attribute
=======================

To create a new attribute, go to List view and select the product manager (or equivalent) folder in the page tree.
Either click "Create new record" in top of page or "New record" in the Attributes section.

Fields
=======================

This is a short explanation of the "non standard" TYPO3 fields.

.. container:: table-row

   Field
        Name
   Description
        Name of attribute, displayed in FE if no Label exist.

.. container:: table-row

   Field
        Label
   Description
        Label of attribute, displayed in FE if set.

.. container:: table-row

   Field
        Type
   Description
        Used to select of what type the attribute is.

.. container:: table-row

   Field
        Required
   Description
        Set if the attribute is mandatory when editing products.

.. container:: table-row

   Field
        Show In Attribute Listing
   Description
        When enabled attribute will be included in attribute listings in product FE single view.

.. container:: table-row

   Field
        Show In Compare
   Description
        TBD, will probably be included from a separat addon module.

.. container:: table-row

   Field
        Image
   Description
        Option to add a image or a svg to be included in attribute listings in product FE single view.

.. container:: table-row

   Field
        Identifier
   Description
        A unique attribute identifier, primarily used in templates to be able to fetch and render attributes in FE.

.. container:: table-row

   Field
        Options
   Description
        Options for dropdown and multiselect attribute types.


The different types of attributes
==============================================

The different types of attributes apply to how the information is filled out in the product,
and then shown in the frontend. The field type options for attributes,
indicate how they will be presented in the backend of the product, when creating it.

**Here is a list of the different types, and what they are used for:**

  - Input - used to add a line of free text in the product.
  - Text - allows you to add a text mass.
  - Date Time - adds a calendar field, for example to specify a release date.
  - Dropdown - options are added in the attribute, and in the product, one option can be selected through a dropdown menu (type to use for filter function).
  - Multiselect - options are added in the attribute, and in the product, multiple options can be selected (type to use for filter function).
  - Checkbox - like the multiselect, this allows you to select more than one option, but the options are shown as checkboxes instead in the backend of the product.
  - Link - adds a link field in the product backend, and a link on the frontend.
  - Image - an image can be added in the table of product information. Note that this is not the image that will be shown in the box for product listing views.

.. important::

  **NOTE!** If the attribute is to be used as a filter, the attribute needs to use the dropdown or multiselect options
  and not for example a free text field.
