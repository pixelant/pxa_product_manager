<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction\Solr;

use Pixelant\PxaProductManager\Domain\Model\Image;

class MainImage extends AbstractImage
{
    /**
     * {@inheritdoc}
     */
    public function type(): int
    {
        return Image::MAIN_IMAGE;
    }
}
