<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Domain\Repository;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package Pixelant\PxaProductManager\Tests\Functional\Domain\Repository
 */
class AttributeValueRepositoryTest extends FunctionalTestCase
{
    /**
     * @var object|AttributeValueRepository
     */
    protected $repository;

    protected $testExtensionsToLoad = [
        'typo3conf/ext/pxa_product_manager'
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->repository = GeneralUtility::makeInstance(ObjectManager::class)->get(AttributeValueRepository::class);
    }

    /**
     * @test
     */
    public function canFindAttributeValueByProductAndAttribute()
    {
        $this->importDataSet(__DIR__ . '/../../../Fixtures/attributevalue.xml');

        $row = $this->repository->findRawByProductAndAttribute(10, 100);
        $this->assertEquals('passed', $row['value']);
    }

}
