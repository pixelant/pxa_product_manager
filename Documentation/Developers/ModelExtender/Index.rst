.. include:: ../../Includes.txt

.. _model-extender:

Model extender
==============

.. toctree::
  :maxdepth: 2
  :titlesonly:

  ControllerExtending/Index

.. _developer-hooks-model-extender-api-overview:

API overview
------------

We're using `evoWeb/extender <https://github.com/evoWeb/extender/>`__ extension as API for model extending.
Additional documentation can be found `here <https://docs.typo3.org/typo3cms/extensions/extender/stable/Index.html>`__.

.. _developer-hooks-model-extender-api-usage:

API usage
---------

Basically, all that you to extend model is add following code in your **ext_localconf.php**:

.. code-block:: php

   $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['<EXTKEY1>']['extender']['<CLASSNAME>']['<EXTKEY2>'] =
        '\<Vendor>\<ExtensionKey>\Domain\Model\<YourModel>';

*  EXTKEY1 - extension key of the extension in which the domain model should be extended
*  EXTKEY2 - extension key of the extension in which the extending domain model resides
*  CLASSNAME - classname of the domain model to be extended including the complete namespace

Here's an example with pxa_product_manager:

.. code-block:: php

   $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pxa_product_manager']['extender'][\Pixelant\PxaProductManager\Domain\Model\Product::class]['pxa_product_manager'] =
        \Pixelant\PxaProductManager\Domain\Model\Test::class;

.. tip::
   You can use multiple extending from different extensions and namespaces.
