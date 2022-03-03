.. include:: ../../../Includes.txt


.. _attributeset_customlayout:

==================================
Custom Layout
==================================

It is possible to add custom layouts for Attribute sets.
Basically layout from Attribute sets is used to determine what partial to use when rendering the attribute listing.

.. code-block:: html

    <f:render partial="Product/AttributeSet/{attributeset.layout}" arguments="{_all}" />


Example Custom Layout
==================================

Following example adds a new Layout which uses MyCustomLayout.html to render attribute listing for Attribute sets with layout "My Custom Layout".


Add new item to Attribute set Layout field with pagets.
---------------------------------------------------------------

.. code-block:: typoscript

    TCEFORM {
        # Add option for custom partial for General Specifications attribute set
        tx_pxaproductmanager_domain_model_attributeset {
            layout {
                addItems.MyCustomLayout = My Custom Layout
            }
        }
    }

.. important::

    Note the key needs to start with a capital letter because it is used directly in template to decide what partial to use.


Add partialRootPaths to typoscript setup of your extension.
---------------------------------------------------------------

.. code-block:: typoscript

    plugin.tx_pxaproductmanager {
        view {
            partialRootPaths {
                20 = EXT:<yourextension>/Resources/Private/Extensions/pxa_product_manager/Partials/
            }
        }
    }


Create the new partial template in your extension.
---------------------------------------------------------------

Partial should to be created in folder Product/AttributeSet/ inside the partialRootPaths set in typoscript.

In this example a file with the name MyCustomLayout.html should be created in folder:
``<yourextension>/Resources/Private/Extensions/pxa_product_manager/Partials/Product/AttributeSet/``

To have something to start with, copy the Default Layout ``pxa_product_manager/Resources/Private/Partials/Product/AttributeSet/Default.html`` to your new layout.
