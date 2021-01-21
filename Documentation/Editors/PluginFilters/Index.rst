.. include:: ../../Includes.txt


.. _plugin-filters:

==============
Plugin Filters
==============

Product filters can be attached to "Product Render" and "Product List" plugins to be used as filtering options in FE.


Add new filter
==============

To create a new filter, go to List view and select the product manager (or equivalent) folder in the page tree.
Either click "Create new record" in top of page or "New record" in the Filter type section.

Fields
======

This is a short explanation of the "non standard" TYPO3 fields.

.. container:: table-row

   Field
        Type
   Description
        Type of filter, e.g. filter by category or attribute. Only applicable on some type of attributes like options.

.. container:: table-row

    Field
        Name
    Description
        Name of the filter. Will be displayed in BE and in FE if no label is set.

.. container:: table-row

    Field
        Label
    Description
        Label of the filter. Will be displayed in FE if set.

.. container:: table-row

    Field
        Render type
    Description
        Decides how the filter is rendered in FE, currently checkboxes, options or dropdown.
        When using option only one option can be selected, checkboxes and dropdown allows for multple selections.

.. container:: table-row

    Field
        State
    Description
        Decides if the filter can be collapsible in FE. If collapsible also if it should be expanded or collapsed at load.
        Doesn't apply to filters of the type dropdown.

.. container:: table-row

    Field
        Conjunction
    Description
        Sets the conjuction for the filter. With option "Or" any of the selected options can match.
        With option "And" all of the seleced options must match.

.. container:: table-row

    Field
        Attribute / Category
    Description
        Attribute: select what attribute to use as filter.

        Category: select a "parent" filter to fetch sub categories from and use as filter.
