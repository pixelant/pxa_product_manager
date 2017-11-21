<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Domain\Repository;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class AttributeValueRepositoryTest extends FunctionalTestCase
{
    /**
     * @var AttributeValueRepository
     */
    protected $attributeValueRepository;

    protected $testExtensionsToLoad = ['typo3conf/ext/pxa_product_manager'];

    protected function setUp()
    {
        parent::setUp();
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_pxaproductmanager_domain_model_option.xml');
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_pxaproductmanager_domain_model_attributevalue.xml');
        $this->attributeValueRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(
            AttributeValueRepository::class
        );
    }

    /**
     * @test
     */
    public function findingAttributeValueByMinAndMaxOptionsReturnCorrectAttributesValues()
    {
        $expectUid = 35; // uid of attribute value

        $result = $this->attributeValueRepository->findAttributeValuesByAttributeAndMinMaxOptionValues(
            3, // attribute uid
            27, // min value of option
            30 // max value of option
        );

        $this->assertCount(1, $result);
        $this->assertTrue($expectUid === $result[0]['uid']);
    }

    /**
     * @test
     */
    public function findingAttributeValueByMinAndMaxOptionsNoResultsFound()
    {
        $result = $this->attributeValueRepository->findAttributeValuesByAttributeAndMinMaxOptionValues(
            3, // attribute uid
            12, // min value of option
            27 // max value of option
        );

        $this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function findingAttributeValueByMinOption()
    {
        $expectUid = 35; // uid of attribute value

        $result = $this->attributeValueRepository->findAttributeValuesByAttributeAndMinMaxOptionValues(
            3, // attribute uid
            27 // min value of option
        );

        $this->assertCount(1, $result);
        $this->assertTrue($expectUid === $result[0]['uid']);
    }

    /**
     * @test
     */
    public function findingAttributeValueByMaxOption()
    {
        $expectUid = 35; // uid of attribute value

        $result = $this->attributeValueRepository->findAttributeValuesByAttributeAndMinMaxOptionValues(
            3, // attribute uid
            null, // min value of option
            30 // max option
        );

        $this->assertCount(1, $result);
        $this->assertTrue($expectUid === $result[0]['uid']);
    }

    /**
     * @test
     */
    public function findingAttributeValueIfMaxAndMinEquals()
    {
        $expectUid = 35; // uid of attribute value

        $result = $this->attributeValueRepository->findAttributeValuesByAttributeAndMinMaxOptionValues(
            3, // attribute uid
            28, // min value of option
            28 // max option
        );

        $this->assertCount(1, $result);
        $this->assertTrue($expectUid === $result[0]['uid']);
    }
}
