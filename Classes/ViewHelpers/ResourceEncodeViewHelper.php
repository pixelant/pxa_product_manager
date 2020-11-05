<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\ViewHelpers;

use Pixelant\PxaProductManager\Domain\Resource\ResourceInterface;
use Pixelant\PxaProductManager\Service\Resource\ResourceConverter;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class ResourceEncodeViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var ResourceConverter
     */
    /** @codingStandardsIgnoreStart */
    protected static ?ResourceConverter $converter = null;
    /** @codingStandardsIgnoreEnd */

    /**
     * View helper arguments.
     */
    public function initializeArguments(): void
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

        /** @var ResourceInterface $resource */
        $resourceArray = static::getConverter()->convert($entity, $arguments['resource']);

        return json_encode($resourceArray);
    }

    /**
     * @return ResourceConverter
     */
    protected static function getConverter(): ResourceConverter
    {
        if (static::$converter === null) {
            static::$converter = GeneralUtility::makeInstance(ObjectManager::class)->get(ResourceConverter::class);
        }

        return self::$converter;
    }
}
