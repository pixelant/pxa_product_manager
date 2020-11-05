<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction\Solr;

use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class AttributeValue
{
    /**
     * @var ContentObjectRenderer
     */
    public ContentObjectRenderer $cObj;

    /**
     * @var AttributeValueRepository
     */
    protected AttributeValueRepository $repository;

    /**
     * Initialize.
     */
    public function __construct()
    {
        $this->repository = GeneralUtility::makeInstance(ObjectManager::class)->get(AttributeValueRepository::class);
    }

    /**
     * Return string value of attribute.
     *
     * @param $_
     * @param array $params
     * @return string
     * @throws \InvalidArgumentException
     */
    public function value($_, array $params): string
    {
        if (empty($params['identifier'])) {
            throw new \InvalidArgumentException('Identifier could not be empty', 1503304897705);
        }
        $identifier = $params['identifier'];
        $productUid = (int)$this->cObj->data['uid'];

        $row = $this->repository->findRawByProductAndAttributeIdentifier(
            $productUid,
            $identifier
        );
        if ($row !== null) {
            return $row['value'];
        }

        return '';
    }
}
