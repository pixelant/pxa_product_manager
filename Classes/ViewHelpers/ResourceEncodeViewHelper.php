<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\ViewHelpers;

use Pixelant\PxaProductManager\Domain\Resource\ResourceInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * @package Pixelant\PxaProductManager\ViewHelpers
 */
class ResourceEncodeViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * View helper arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('entity', AbstractEntity::class, 'Entity that should be convert to resource');
        $this->registerArgument('resource', 'string', 'Override resource class name');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        /** @var AbstractEntity $entity */
        $entity = $arguments['entity'] ?? $renderChildrenClosure();
        $resourceClassName = $arguments['resource'] ?? static::translateEntityNameToResourceName(get_class($entity));

        /** @var ResourceInterface $resource */
        $resource = GeneralUtility::makeInstance(ObjectManager::class)->get($resourceClassName, $entity);

        return json_encode($resource->toArray());
    }

    /**
     * Translate entity to its corresponsing resource
     *
     * @param string $entityClassName
     * @return string
     */
    protected static function translateEntityNameToResourceName(string $entityClassName): string
    {
        return str_replace('\\Model\\', '\\Resource\\', $entityClassName);
    }
}
