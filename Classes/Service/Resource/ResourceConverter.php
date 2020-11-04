<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service\Resource;

use Pixelant\PxaProductManager\Domain\Resource\ResourceInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class ResourceConverter
{
    /**
     * @var ObjectManager
     */
    protected ObjectManager $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function injectObjectManager(ObjectManager $objectManager): void
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Convert entity to array using resource.
     *
     * @param AbstractEntity $entity
     * @param string|null $resource
     * @return array
     */
    public function convert(AbstractEntity $entity, string $resource = null): array
    {
        $resource ??= $this->translateEntityNameToResourceName($entity);
        /** @var ResourceInterface $resourceInstance */
        $resourceInstance = $this->objectManager->get($resource, $entity);

        return $resourceInstance->toArray();
    }

    /**
     * Convert many entities.
     *
     * @param AbstractEntity[] $entities
     * @param string $resource
     * @return array
     */
    public function covertMany(array $entities, string $resource = null)
    {
        return array_map(fn (AbstractEntity $entity) => $this->convert($entity, $resource), $entities);
    }

    /**
     * Translate entity to its corresponding resource.
     *
     * @param AbstractEntity $entity
     * @return string
     */
    protected function translateEntityNameToResourceName(AbstractEntity $entity): string
    {
        $entityName = get_class($entity);
        $resource = str_replace('\\Model\\', '\\Resource\\', $entityName);

        // If entity was extended, but no resource exist, fallback to original
        if (!class_exists($resource)) {
            [$lastPart] = explode('\\', strrev($entityName), 2);

            $resource = 'Pixelant\\PxaProductManager\\Domain\\Resource\\' . strrev($lastPart);
        }

        return $resource;
    }
}
