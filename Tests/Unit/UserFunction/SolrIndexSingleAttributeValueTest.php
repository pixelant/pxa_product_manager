<?php

namespace Pixelant\PxaProductManager\Tests\Unit\UserFunction;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\UserFunction\SolrIndexSingleAttributeValue;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class SolrIndexSingleAttributeValueTest
 * @package Pixelant\PxaProductManager\Tests\Unit\UserFunction
 */
class SolrIndexSingleAttributeValueTest extends UnitTestCase
{
    /**
     * @var SolrIndexSingleAttributeValue|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fixture;

    /**
     * Setup
     */
    protected function setUp()
    {
        $this->fixture = $this->getMockBuilder(SolrIndexSingleAttributeValue::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMock();
    }

    /**
     * @test
     */
    public function gettingAttributeValueWithoutIdentifierThrowsException()
    {
        $this->expectException(
            \UnexpectedValueException::class
        );

        $this->fixture->getSingleAttributeValue('', []);
    }

    /**
     * @test
     */
    public function getAttributeValueOfTypeTextReturnTextValue()
    {
        $parameters['identifier'] = 'rsk';
        $value = 'text value';

        // Simulate data
        $cOj = $this->createMock(ContentObjectRenderer::class);
        $cOj->data['serialized_attributes_values'] = serialize([1 => $value]);

        $this->fixture->cObj = $cOj;

        $this->fixture->expects($this->once())
            ->method('getAttribute')
            ->with('rsk')
            ->willReturn(['uid' => 1, 'type' => Attribute::ATTRIBUTE_TYPE_INPUT]);

        $result = $this->fixture->getSingleAttributeValue('', $parameters);

        $this->assertEquals(
            $value,
            $result
        );
    }

    /**
     * @test
     */
    public function getAttributeValueOfTypeCheckBoxReturnInteger()
    {
        $parameters['identifier'] = 'rsk';
        $value = '123';

        // Simulate data
        $cOj = $this->createMock(ContentObjectRenderer::class);
        $cOj->data['serialized_attributes_values'] = serialize([1 => $value]);

        $this->fixture->cObj = $cOj;

        $this->fixture->expects($this->once())
            ->method('getAttribute')
            ->with('rsk')
            ->willReturn(['uid' => 1, 'type' => Attribute::ATTRIBUTE_TYPE_CHECKBOX]);

        $result = $this->fixture->getSingleAttributeValue('', $parameters);

        $this->assertInternalType('int', $result);
    }

    /**
     * @test
     */
    public function getAttributeValueAndAttributeNotFoundReturnEmtyString()
    {
        $parameters['identifier'] = 'rsk';
        $value = 'text value';

        // Simulate data
        $cOj = $this->createMock(ContentObjectRenderer::class);
        $cOj->data['serialized_attributes_values'] = serialize([2 => $value]);

        $this->fixture->cObj = $cOj;

        $this->fixture->expects($this->once())
            ->method('getAttribute')
            ->with('rsk')
            ->willReturn(['uid' => 1, 'type' => Attribute::ATTRIBUTE_TYPE_INPUT]);

        $result = $this->fixture->getSingleAttributeValue('', $parameters);

        $this->assertEquals(
            '',
            $result
        );
    }

    /**
     * @test
     */
    public function getAttributeValueAndAttributeTypeNotSupportedReturnEmtyString()
    {
        $parameters['identifier'] = 'rsk';
        $value = 'text value';

        // Simulate data
        $cOj = $this->createMock(ContentObjectRenderer::class);
        $cOj->data['serialized_attributes_values'] = serialize([1 => $value]);

        $this->fixture->cObj = $cOj;

        $this->fixture->expects($this->once())
            ->method('getAttribute')
            ->with('rsk')
            ->willReturn(['uid' => 1, 'type' => Attribute::ATTRIBUTE_TYPE_IMAGE]);

        $result = $this->fixture->getSingleAttributeValue('', $parameters);

        $this->assertEquals(
            '',
            $result
        );
    }

    protected function tearDown()
    {
        unset($this->fixture);
    }
}
