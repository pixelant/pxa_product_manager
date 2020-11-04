<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction\Solr;

use Pixelant\PxaProductManager\Domain\Model\Image;

class ThumbnailImage extends AbstractImage
{
    /**
     * {@inheritdoc}
     */
    public function type(): int
    {
        return Image::LISTING_IMAGE;
    }
}
