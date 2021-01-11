<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;

/**
 * Abstract mapper.
 */
abstract class AbstractMapper implements MapperInterface
{
    use CanCreateCollection;
}
