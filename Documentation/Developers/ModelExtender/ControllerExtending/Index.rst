.. include:: ../../../Includes.txt

.. _controller-extending:

Controller extending
====================

.. _controller-usage:

Usage
-----

With this extension you can easily extending controllers from whenever. Just like an example with
:ref:`developer-hooks-model-extender-api-usage`.

.. code-block:: php

   $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['pxa_product_manager']['extender'][\Pixelant\PxaProductManager\Controller\TestController::class]['t3kit'] =
        'EXT:t3kit/Classes/Controller/TestyController.php';

But to make it work properly you need to allow extended actions to plugin which will use it.

To make it possible without overriding :code:`\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin()` function, we provide class :code:`\Pixelant\PxaProductManager\Utility\ExtensionUtility::class`.

Function :code:`\Pixelant\PxaProductManager\Utility\ExtensionUtility::addControllerAction(string $extensionName, string $pluginName, string $controllerClassName, array $actions)`, gave us possibility to extend list of allowed actions.

Example:

.. code-block:: php

       \Pixelant\PxaProductManager\Utility\ExtensionUtility::addControllerAction(
        'PxaProductManager',
        'ProductRender',
        'Pixelant\PxaProductManager\Controller\TestController',
        [
            'test', 'testy'
        ]
    );

*  **test** - action that exists in *TestController*.
*  **testy** - action that we extend from parent controller *EXT:t3kit/Classes/Controller/TestyController.php*

Controller interface
--------------------

Actually, this way of controller extending have an issue that distinguish it from straight class extending.

You need always implement :code:`TYPO3\CMS\Extbase\Mvc\Controller\ControllerInterface` (or extend class that implements it like :code:`TYPO3\CMS\Extbase\Mvc\Controller\ActionController::class`) at child class, even if it's implemented at parent.

Example:

.. code-block:: php

   <?php

    declare(strict_types=1);

    namespace \Pixelant\PxaProductManager\Controller;

    use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

    class TestController extends ActionController
    {
        public function testAction()
        {
            $this->view->assign('test', 'test');
        }
    }

You can see from :ref:`controller-usage`, that **TestController** extends **TestyController**.

.. code-block:: php

   <?php

    declare(strict_types=1);

    namespace T3k\t3kit\Controller;

    use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

    class TestyController extends ActionController
    {
        public function testyAction()
        {
            $this->view->assign('testy', 'testy');
        }
    }

In straight extending we can avoid implementing **ControllerInterface** in **TestController** as it implemented at parent **TestyController**.

But in our case that will trigger an PHP error.
