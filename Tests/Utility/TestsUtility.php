<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Utility;

use Pixelant\PxaProductManager\Domain\Model\Category;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class TestsUtility
{
    public static function createCategoriesRootLineAndReturnLastCategory(): Category
    {
        $rootLine = self::createMultipleEntities(Category::class, 5);

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

    public static function entitiesToUidsArray($objects)
    {
        if (is_object($objects)) {
            $objects = $objects->toArray();
        }

        return array_map(fn ($object) => $object->getUid(), $objects);
    }

    public static function createMultipleEntities(string $className, int $to, int $from = 1)
    {
        if ($to < $from) {
            $to = $from;
        }

        $objects = [];
        for ($i = $from; $i <= $to; $i++) {
            $objects[] = self::createEntity($className, $i);
        }

        return $objects;
    }

    public static function createEntity(string $className, $properties, callable $callback = null): AbstractEntity
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

    public static function createObjectStorage(...$objects): ObjectStorage
    {
        $objectStorage = new ObjectStorage();

        foreach ($objects as $object) {
            $objectStorage->attach($object);
        }

        return $objectStorage;
    }

    public static function getProtectedVarValue($object, $property)
    {
        $reflector = new \ReflectionClass($object);
        $property = $reflector->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
