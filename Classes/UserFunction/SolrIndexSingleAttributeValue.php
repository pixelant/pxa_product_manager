<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Traits\ProductRecordTrait;
use Pixelant\PxaProductManager\Utility\TCAUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Resource\FileCollector;

/**
 * Class SolrIndexSingleAttributeValue
 * @package Pixelant\PxaProductManager\UserFunction
 */
class SolrIndexSingleAttributeValue
{
    use ProductRecordTrait;
    
    /**
     * @var ContentObjectRenderer
     */
    public $cObj;

    /**
     * Get attribute value for product by identifier
     *
     * @param string $content
     * @param array $parameters
     * @return int|string
     */
    public function getSingleAttributeValue(
        /** @noinspection PhpUnusedParameterInspection */
        string $content,
        array $parameters
    ) {
        if (empty($parameters['identifier'])) {
            throw new \UnexpectedValueException('Identifier could not be empty', 1503304897705);
        }

        $attributeValues = $this->getAttributesValuesFromRow($this->cObj->data);
        $attribute = $this->getAttribute($parameters['identifier']);
        $value = '';

        if (is_array($attribute)
            && (isset($attributeValues[$attribute['uid']]) || $this->isFalType($attribute['type']))
        ) {
            switch ($attribute['type']) {
                case Attribute::ATTRIBUTE_TYPE_INPUT:
                case Attribute::ATTRIBUTE_TYPE_TEXT:
                    $value = $attributeValues[$attribute['uid']];
                    break;
                case Attribute::ATTRIBUTE_TYPE_CHECKBOX:
                    $value = $attributeValues[$attribute['uid']] ? 1 : 0;
                    break;
                case Attribute::ATTRIBUTE_TYPE_IMAGE:
                case Attribute::ATTRIBUTE_TYPE_FILE:
                    $fileCollector = GeneralUtility::makeInstance(FileCollector::class);
                    $fileCollector->addFilesFromRelation(
                        'tx_pxaproductmanager_domain_model_product',
                        TCAUtility::ATTRIBUTE_FAL_FIELD_NAME,
                        $this->cObj->data
                    );
                    /** @var FileReference[] $allAttributeFiles */
                    $allAttributeFiles = $fileCollector->getFiles();
                    $attributeFiles = [];
                    foreach ($allAttributeFiles as $file) {
                        if ($file->getReferenceProperty('pxa_attribute') === $attribute['uid']) {
                            $attributeFiles[] = $file->getPublicUrl();
                        }
                    }

                    return implode(',', $attributeFiles);
                    break;
                default:
                    // @TODO support other types
                    return $value;
            }
        }

        return $value;
    }

    /**
     * Check if attribute is fal type
     *
     * @param int $type
     * @return bool
     */
    protected function isFalType(int $type): bool
    {
        return $type === Attribute::ATTRIBUTE_TYPE_FILE || $type === Attribute::ATTRIBUTE_TYPE_IMAGE;
    }

    /**
     * @param string $identifier
     * @return array
     */
    protected function getAttribute(string $identifier)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
            'tx_pxaproductmanager_domain_model_attribute'
        );

        $row = $queryBuilder->select('uid', 'type')
            ->from('tx_pxaproductmanager_domain_model_attribute')
            ->where(
                $queryBuilder->expr()->eq(
                    'sys_language_uid',
                    $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'identifier',
                    $queryBuilder->createNamedParameter($identifier, Connection::PARAM_STR)
                )
            )
            ->setMaxResults(1)
            ->execute()
            ->fetch();

        return $row;
    }
}
