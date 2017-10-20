<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Attribute;

/**
 * Test case for class \Pixelant\PxaProductManager\Domain\Model\Attribute.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Products Manager
 *
 */
class AttributeTest extends UnitTestCase
{
    /**
     * @var Attribute
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = new Attribute();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function nameCanBeSet()
    {
        $name = 'Name';
        $this->fixture->setName($name);

        self::assertEquals(
            $name,
            $this->fixture->getName()
        );
    }

    /**
     * @test
     */
    public function typeCanBeSet()
    {
        $type = 7;
        $this->fixture->setType($type);

        self::assertEquals(
            $type,
            $this->fixture->getType()
        );
    }

    /**
     * @test
     */
    public function getRequiredReturnsInitialForRequired()
    {
        self::assertEquals(
            false,
            $this->fixture->getRequired()
        );
    }

    /**
     * @test
     */
    public function requiredCanBeSet()
    {
        $value = true;
        $this->fixture->setRequired($value);

        self::assertEquals(
            $value,
            $this->fixture->getRequired()
        );
    }


    /**
     * @test
     */
    public function getShowInAttributeListingReturnsInitialForShowInAttributeListing()
    {
        self::assertEquals(
            false,
            $this->fixture->getShowInAttributeListing()
        );
    }

    /**
     * @test
     */
    public function showInAttributeListingCanBeSet()
    {
        $value = true;
        $this->fixture->setShowInAttributeListing($value);

        self::assertEquals(
            $value,
            $this->fixture->getShowInAttributeListing()
        );
    }

    /**
     * @test
     */
    public function getShowInCompareReturnsInitialForShowInCompare()
    {
        self::assertEquals(
            false,
            $this->fixture->getShowInCompare()
        );
    }

    /**
     * @test
     */
    public function showInCompareCanBeSet()
    {
        $value = true;
        $this->fixture->setShowInCompare($value);

        self::assertEquals(
            $value,
            $this->fixture->getShowInCompare()
        );
    }

    /**
     * @test
     */
    public function identifierCanBeSet()
    {
        $identifier = 'Identifier';
        $this->fixture->setIdentifier($identifier);

        self::assertEquals(
            $identifier,
            $this->fixture->getIdentifier()
        );
    }

    /**
     * @test
     */
    public function getOptionsReturnsInitialValueForOption()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->fixture->getOptions()
        );
    }

    /**
     * @test
     */
    public function setOptionsForObjectStorageContainingOptionSetsOptions()
    {
        $option = new \Pixelant\PxaProductManager\Domain\Model\Option();
        $objectStorageHoldingExactlyOneOptions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneOptions->attach($option);
        $this->fixture->setOptions($objectStorageHoldingExactlyOneOptions);

        self::assertSame(
            $objectStorageHoldingExactlyOneOptions,
            $this->fixture->getOptions()
        );
    }
    
    /**
     * @test
     */
    public function addOptionToObjectStorageHoldingOptions()
    {
        $option = new \Pixelant\PxaProductManager\Domain\Model\Option();
        $objectStorageHoldingExactlyOneOption = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneOption->attach($option);
        $this->fixture->addOption($option);

        $this->assertEquals(
            $objectStorageHoldingExactlyOneOption,
            $this->fixture->getOptions()
        );
    }

    /**
     * @test
     */
    public function removeOptionFromObjectStorageHoldingOptions()
    {
        $option = new \Pixelant\PxaProductManager\Domain\Model\Option();
        $localObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $localObjectStorage->attach($option);
        $localObjectStorage->detach($option);
        $this->fixture->addOption($option);
        $this->fixture->removeOption($option);

        $this->assertEquals(
            $localObjectStorage,
            $this->fixture->getOptions()
        );
    }

    /**
     * @test
     */
    public function labelCheckedCanBeSet()
    {
        $label = 'LabelChecked';
        $this->fixture->setLabelChecked($label);

        self::assertEquals(
            $label,
            $this->fixture->getLabelChecked()
        );
    }

    /**
     * @test
     */
    public function labelUnCheckedCanBeSet()
    {
        $label = 'LabelUnChecked';
        $this->fixture->setLabelUnchecked($label);

        self::assertEquals(
            $label,
            $this->fixture->getLabelUnchecked()
        );
    }

    /**
     * @test
     */
    public function defaultLabelCanBeSet()
    {
        $defaultLabel = 'DefaultLabel';
        $this->fixture->setDefaultValue($defaultLabel);

        self::assertEquals(
            $defaultLabel,
            $this->fixture->getDefaultValue()
        );
    }

    /**
     * @test
     */
    public function valueStringCanBeSet()
    {
        $value = 'value-of-attribute';
        $this->fixture->setValue($value);

        self::assertEquals(
            $value,
            $this->fixture->getValue()
        );
    }

    /**
     * @test
     */
    public function valueArrayCanBeSet()
    {
        $value = [];
        $this->fixture->setValue($value);

        self::assertEquals(
            $value,
            $this->fixture->getValue()
        );
    }

    /**
     * @test
     */
    public function labelCanBeSet()
    {
        $label = 'Label';
        $this->fixture->setLabel($label);

        self::assertEquals(
            $label,
            $this->fixture->getLabel()
        );
    }
}
