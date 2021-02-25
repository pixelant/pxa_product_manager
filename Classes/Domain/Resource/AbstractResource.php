<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Resource;

use Pixelant\PxaProductManager\Event\Resource\ResourceToArray;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * Base resource to array.
 */
abstract class AbstractResource implements ResourceInterface
{
    /**
     * @var AbstractEntity
     */
    protected AbstractEntity $entity;

    /**
     * @var EventDispatcher
     */
    protected EventDispatcher $dispatcher;

    /**
     * @param AbstractEntity $entity
     */
    public function __construct(AbstractEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @param EventDispatcher $dispatcher
     */
    public function injectDispatcher(EventDispatcher $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Convert entity to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = $this->extractProperties();

        $eventData = $this->dispatcher->dispatch(
            GeneralUtility::makeInstance(ResourceToArray::class, $result)
        );

        return $eventData->getData();
    }

    /**
     * Extract properties from entity. Allow to pass additional values from child object
     * when override this method.
     *
     * @param array|null $additionalProperties
     * @return array
     */
    protected function extractProperties(array $additionalProperties = null): array
    {
        $result = [];

        foreach ($this->extractableProperties() as $property) {
            $result[$property] = $this->convertPropertyValue(
                ObjectAccess::getProperty($this->entity, $property)
            );
        }

        if ($additionalProperties) {
            $result = array_merge($result, $additionalProperties);
        }

        return $result;
    }

    /**
     * Process property value.
     *
     * @param $value
     * @return mixed
     */
    protected function convertPropertyValue($value)
    {
        if ($value instanceof ObjectStorage) {
            return $value->toArray();
        }

        return $value;
    }

    /**
     * Properties that will be extracted to array.
     *
     * @return array
     */
    abstract protected function extractableProperties(): array;
}
