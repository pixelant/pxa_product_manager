<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FileReference;

/**
 * Class AttributeFalFile
 * @package Pixelant\PxaProductManager\Domain\Model
 */
class AttributeFalFile extends FileReference
{
    /**
     * Attribute uid if belongs to attribute
     *
     * @var int
     */
    protected $attribute = 0;

    /**
     * @return int
     */
    public function getAttribute(): int
    {
        return $this->attribute;
    }

    /**
     * @param int $attribute
     */
    public function setAttribute(int $attribute)
    {
        $this->attribute = $attribute;
    }
}
