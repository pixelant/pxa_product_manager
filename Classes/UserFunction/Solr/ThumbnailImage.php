<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction\Solr;

use Pixelant\PxaProductManager\Domain\Model\Image;

/**
 * @package Pixelant\PxaProductManager\UserFunction\Solr
 */
class ThumbnailImage extends AbstractImage
{
    /**
     * @inheritDoc
     */
    public function type(): int
    {
        return Image::LISTING_IMAGE;
    }
}
