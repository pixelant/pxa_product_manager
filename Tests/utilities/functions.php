<?php

function createCategoriesRootLineAndReturnLastCategory(): Pixelant\PxaProductManager\Domain\Model\Category
{
    $rootLine = createMultipleEntities(\Pixelant\PxaProductManager\Domain\Model\Category::class, 5);

    // Simulate rootline
    $prev = null;
    foreach ($rootLine as $category) {
        if ($prev !== null) {
            $category->setParent($prev);
        }
        $prev = $category;
    }

    return $prev;
}

function entitiesToUidsArray($objects)
{
    if (is_object($objects)) {
        $objects = $objects->toArray();
    }

    return array_map(fn ($object) => $object->getUid(), $objects);
}

function createMultipleEntities(string $className, int $to, int $from = 1)
{
    if ($to < $from) {
        $to = $from;
    }

    $objects = [];
    for ($i = $from; $i <= $to; $i++) {
        $objects[] = createEntity($className, $i);
    }

    return $objects;
}

function createEntity(string $className, $properties, callable $callback = null): TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /** @var \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $entity */
    $entity = new $className();

    if (is_int($properties)) {
        // Assume it's uid
        $entity->_setProperty('uid', $properties);
    } elseif (is_array($properties)) {
        foreach ($properties as $property => $value) {
            $entity->_setProperty($property, $value);
        }
    }

    if ($callback) {
        $callback($entity);
    }

    return $entity;
}

function createObjectStorage(...$objects): TYPO3\CMS\Extbase\Persistence\ObjectStorage
{
    $objectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();

    foreach ($objects as $object) {
        $objectStorage->attach($object);
    }

    return $objectStorage;
}

function getProtectedVarValue($object, $property)
{
    $reflector = new ReflectionClass($object);
    $property = $reflector->getProperty($property);
    $property->setAccessible(true);

    return $property->getValue($object);
}
