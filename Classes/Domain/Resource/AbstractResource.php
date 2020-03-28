<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Resource;

use Pixelant\PxaProductManager\Event\Resource\ResourceToArray;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * Base resource to array
 *
 * @package Pixelant\PxaProductManager\Domain\Resource
 */
abstract class AbstractResource implements ResourceInterface
{
    /**
     * @var AbstractEntity
     */
    protected AbstractEntity $entity;

    /**
     * @var Dispatcher
     */
    protected Dispatcher $dispatcher;

    /**
     * @param AbstractEntity $entity
     */
    public function __construct(AbstractEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @param Dispatcher $dispatcher
     */
    public function injectDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Convert entity to array
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = $this->extractProperties();

        $eventData = GeneralUtility::makeInstance(ResourceToArray::class, $result);
        $this->dispatcher->dispatch(get_class($this), 'resourceToArray', [$eventData, $this->entity]);

        return $eventData->getData();
    }

    /**
     * Extract properties from entity. Allow to pass additional values from child object
     * when override this method
     *
     * @param array|null $additionalProperties
     * @return array
     */
    protected function extractProperties(array $additionalProperties = null): array
    {
        $result = [];

        foreach ($this->extractableProperties() as $property) {
            $result[$property] = ObjectAccess::getProperty($this->entity, $property);
        }

        if ($additionalProperties) {
            $result = array_merge($result, $additionalProperties);
        }

        return $result;
    }

    /**
     * Properties that will be extracted to array
     *
     * @return array
     */
    abstract protected function extractableProperties(): array;
}
