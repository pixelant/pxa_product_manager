<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Resource;

use Pixelant\PxaProductManager\Arrayable;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * @package Pixelant\PxaProductManager\ViewHelpers
 */
interface ResourceInterface extends Arrayable
{
    public function __construct(AbstractEntity $entity);
}
