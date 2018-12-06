.. include:: ../../Includes.txt


.. _product-plugin-settings:

Product Plugin Settings
========================

The same product plugin is used for all types of listings, with a setting inside that defines what it should show on the frontend.

A plugin will fetch information stored in a folder in the page tree, as we have seen in previous sections. The plugin is placed on a standard page, and settings are applied to make it fetch the infomation from the correct folder. You will also set how the products will be shown on the front.

To edit an existing product plugin, select Page in the control panel and the product page in the page tree. Edit the product plugin element with the pen icon (4.1) and go to the tab 'Plugin' (4.2).

.. figure:: ../../Images/Editors/4.1.png

    (4.1)

.. figure:: ../../Images/Editors/4.2.png

    (4.2)

Settings tab
-------------
The 'Settings' tab is where you set what type of listing that will be used in the frontend; filter listing (Lazy list) or navigation listing (List view). You will find this setting below the section 'Plugin mode' (1). There are also other settings, for example allowing you to place the detailed view of a product on a separate page, setting up a wishlist viewing page and a comparence page.

To use a separate page for viewing the products, after creating the detail view page, you can assign it in the second field of the settings tab (2). Otherwise, the built in product page will be used. The reason to have a separate page, would be to create and show static content when in detailed product view.

The bottom field contains the category selection (3), where you set which category of products that should be displayed. (4.3)

.. figure:: ../../Images/Editors/4.3.png

    (4.3)

Navigation options tab
-----------------------
If you are using the List view for product (with navigation menu), you can set it to show navigation (1), hide the navigation when switching to product detail view (2) and decide if all categories should be folded out by default (3). (4.4)

.. figure:: ../../Images/Editors/4.4.png

    (4.4)

Single view options tab
------------------------
The Single view options tab, allows you to set if the latest visited products should be shown (1), enable a message if the product page can't be found, instead of showing a 404 page (2) and if a gallery pagination should be visible for product images (3). (4.5)

.. figure:: ../../Images/Editors/4.5.png

    (4.5)

Product ordering tab
---------------------
This tab contains setting regarding sorting order (1) by name, last updated, creation date and category. You can also set the sorting direction (2), to be ascending or descending. (4.6)

.. figure:: ../../Images/Editors/4.6.png

    (4.6)

Categories ordering tab
------------------------
When using the List view (with navigation menu) you can decide if the ordering should be default sorting or by title (1) and also if the sorting direction should be ascending or descending (2). (4.7)

.. figure:: ../../Images/Editors/4.7.png

    (4.7)

Compare & Wish list tab
------------------------
When the plugin is set to show Comparison view or wish list, you can set if icons should be used for the wish list (1) and for the compare list (2). (4.8)

.. figure:: ../../Images/Editors/4.8.png

    (4.8)

Filtering tab
--------------
If the plugin is set to show the filter view (Lazy list), there will be a tab called 'Filtering', where you can set the filtering to not show options without results (1). This means that the filter will have a dependency function, which hides options that are not relevant to selected filters. There is also the selection of which filters that should be used. Select filters from the 'Available items' (2), by clicking on them in the list. They will be moved into the 'Selected items' (3) and you can handle them by marking them in the list, and using the options between the two fields to move them up and down and also delete them. (4.9)

.. figure:: ../../Images/Editors/4.9.png

    (4.9)

Record storage page
--------------------
This section sets the startingpoint of the product manager data, which is the folder containing all the product categories, attributes, products etc. If left empty, the plugin will use the folder which has been set a system default. But in some cases the information might be divided into separate folders (in the page tree), making it necessary to specify for each plugin. You can select folders in three different ways; searching by title (1), using the folder icon to find it in the page tree (2), or using the page button to search in the page tree. The startpoint can be set to recursive, which means it will also include sub folders. (4.10)

.. figure:: ../../Images/Editors/4.10.png

    (4.10)
    