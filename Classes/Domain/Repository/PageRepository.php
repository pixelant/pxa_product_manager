<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Repository;

use TYPO3\CMS\Core\Domain\Repository\PageRepository as CorePageRepository;

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PageRepository extends CorePageRepository
{
    /**
     * Named constants for "magic numbers" of the field doktype
     */
    public const DOKTYPE_PRODUCT_DISPLAY = 9;
}
