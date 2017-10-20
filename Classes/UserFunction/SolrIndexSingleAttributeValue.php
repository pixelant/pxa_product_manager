<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\UserFunction;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class SolrIndexSingleAttributeValue
 * @package Pixelant\PxaProductManager\UserFunction
 */
class SolrIndexSingleAttributeValue
{
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
        /** @noinspection PhpUnusedParameterInspection */ string $content,
        array $parameters
    ) {
        if (empty($parameters['identifier'])) {
            throw new \UnexpectedValueException('Identifier could not be empty', 1503304897705);
        }

        $attributeValues = unserialize($this->cObj->data['serialized_attributes_values']);
        $attribute = $this->getAttribute($parameters['identifier']);
        $value = '';

        if ($attribute
            && isset($attributeValues[$attribute['uid']])) {
            switch ($attribute['type']) {
                case Attribute::ATTRIBUTE_TYPE_INPUT:
                case Attribute::ATTRIBUTE_TYPE_TEXT:
                    $value = $attributeValues[$attribute['uid']];
                    break;
                case Attribute::ATTRIBUTE_TYPE_CHECKBOX:
                    $value = $attributeValues[$attribute['uid']] ? 1 : 0;
                    break;
                default:
                    // @TODO support other types
                    return $value;
            }
        }

        return $value;
    }

    /**
     * @param string $identifier
     * @return \Doctrine\DBAL\Driver\Statement|int
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
