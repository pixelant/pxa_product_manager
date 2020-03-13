<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction\Solr;

use Pixelant\PxaProductManager\Domain\Model\Image;

/**
 * @package Pixelant\PxaProductManager\UserFunction\Solr
 */
class MainImage extends AbstractImage
{

    /**
     * @inheritDoc
     */
    public function type(): int
    {
        return Image::MAIN_IMAGE;
    }
}
