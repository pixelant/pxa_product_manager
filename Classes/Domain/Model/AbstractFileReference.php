<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FileReference;

abstract class AbstractFileReference extends FileReference
{
    /**
     * @var string
     */
    protected string $alternative = '';

    /**
     * @var string
     */
    protected string $title = '';

    /**
     * @var string
     */
    protected string $description = '';

    /**
     * @return string
     */
    public function getAlternative(): string
    {
        return $this->alternative;
    }

    /**
     * @param string $alternative
     * @return AbstractFileReference
     */
    public function setAlternative(string $alternative): self
    {
        $this->alternative = $alternative;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return AbstractFileReference
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return AbstractFileReference
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
