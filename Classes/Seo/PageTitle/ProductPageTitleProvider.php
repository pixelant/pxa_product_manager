<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Seo\PageTitle;

use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;

class ProductPageTitleProvider extends AbstractPageTitleProvider
{
    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
