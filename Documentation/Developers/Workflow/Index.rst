.. include:: ../../Includes.txt


.. _workflow:

========
Workflow
========

How to work with the extension from a development perspective.

.. _installation:

Installation
============

Install the composer dependencies.

.. code-block::

   composer install

.. _commands:

Commands
========

For working with the extension, the following can be run to accomplish common tasks.

To run the PHP linter.

.. code-block::

   composer run ci:php:lint

To run the PHP code style fixer.

.. code-block::

   composer run ci:php:codestyle

To run the PHP codesniffer.

.. code-block::

   composer run ci:php:sniff

To run the JSON linter.

.. code-block::

   composer run ci:json:lint

To run the YAML linter.

.. code-block::

   composer run ci:yaml:lint

To run the TypoScript linter.

.. code-block::

   composer run ci:ts:lint

To run the PHP Unit tests run the following command.

.. code-block::

   composer run ci:tests:unit

To run the PHP Functional tests run the following command.

.. code-block::

   composer run ci:tests:functional
