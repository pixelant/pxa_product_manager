<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class AttributeSetRepository.
 */
class AttributeSetRepository extends Repository
{
    public const TABLE_NAME = 'tx_pxaproductmanager_domain_model_attributeset';
}
