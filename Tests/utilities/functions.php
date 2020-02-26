<?php

function createDomainInstanceWithProperties(string $className, $properties, callable $callback = null): \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /** @var \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $entity */
    $entity = new $className;

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

function createObjectStorageWithObjects(...$objects): \TYPO3\CMS\Extbase\Persistence\ObjectStorage
{
    $objectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();

    foreach ($objects as $object) {
        $objectStorage->attach($object);
    }

    return $objectStorage;
}