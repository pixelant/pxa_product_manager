<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction\Solr;

use Pixelant\PxaProductManager\Attributes\ValueMapper\MapperFactory;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

abstract class AbstractValue
{
    /**
     * @var ContentObjectRenderer
     */
    public ContentObjectRenderer $cObj;

    /**
     * @var DataMapper
     */
    public DataMapper $dataMapper;

    /**
     * @var MapperFactory
     */
    protected MapperFactory $factory;

    /**
     * @var AttributeValueRepository
     */
    protected AttributeValueRepository $repository;

    /**
     * @param DataMapper $dataMapper
     */
    public function injectDataMapper(DataMapper $dataMapper): void
    {
        $this->dataMapper = $dataMapper;
    }

    /**
     * @param AttributeValueRepository $repository
     */
    public function injectAttributeValueRepository(AttributeValueRepository $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @param MapperFactory $repository
     */
    public function injectMapperFactory(MapperFactory $factory): void
    {
        $this->factory = $factory;
    }

    /**
     * Return mapped AttributeValue.
     *
     * @param array $params
     * @return AttributeValue|null
     * @throws \InvalidArgumentException
     */
    public function initValue(array $params): ?AttributeValue
    {
        if (empty($params['identifier'])) {
            throw new \InvalidArgumentException('Identifier could not be empty', 1503304897705);
        }

        $identifier = $params['identifier'];
        $productUid = $this->cObj->data['_LOCALIZED_UID'] ?? $this->cObj->data['uid'];

        $row = $this->repository->findRawByProductAndAttributeIdentifier(
            (int)$productUid,
            $identifier
        );

        if (!empty($row)) {
            /** @var \Pixelant\PxaProductManager\Domain\Model\AttributeValue $obj */
            $obj = $this->dataMapper->map(
                \Pixelant\PxaProductManager\Domain\Model\AttributeValue::class,
                [$row]
            )[0];

            $this->factory->create($obj)->map($obj->getProduct(), $obj);

            return $obj;
        }

        return null;
    }
}
