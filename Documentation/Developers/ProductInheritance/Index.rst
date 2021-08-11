.. include:: ../../Includes.txt


.. _developers-inheritance:

===================
Product Inheritance
===================

This chapter explains the technical sides of product value inheritance between
parent and child products.

.. tip::

   Before you read this chapter, take a look at :ref:`product-inheritance` in
   the :ref:`user-manual` section.

.. _developers-inheritance-processing:

Processing inherited data
=========================

When a child or parent product is saved, hooks within the class
:php:`Pixelant\PxaProductManager\Hook\ProcessDatamap\ProductInheritanceProcessDatamap`
will go through the data map, recursively adding inherited for ancestor product.
This class has a high complexity.

The hooks will also take care of creating attributes that do not exist and
remove those that should not be there (e.g. if the included attributes have been
changed in the product type record).

.. _developers-inheritance-relationtracking:

Tracking relations
==================

When updating a child product or its attributes, it can be difficult to
ascertain which inherited relations ("cousin records") represent which relation
record in the parent. To solve this problem, the Product Manager keeps an index
of relations in the table
:sql:`tx_pxaproductmanager_relation_inheritance_index`.

Operations on this table are handled by :php:`ProductInheritanceProcessDatamap`
through
:php:`Pixelant\PxaProductManager\Domain\Repository\RelationInheritanceIndexRepository`.

.. tip::

   If the relation inheritance index comes out of sync, it can be rebuilt by
   running the command `typo3 productmanager:updaterelationinheritanceindex`.
