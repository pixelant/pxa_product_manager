<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Pixelant\PxaProductManager\Command;

use Pixelant\PxaProductManager\Domain\Repository\AttributeRepository;
use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Domain\Repository\RelationInheritanceIndexRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FixDuplicateAttributeValuesCommand extends Command
{
    protected const PRODUCT_TABLE = ProductRepository::TABLE_NAME;
    protected const ATTRIBUTEVALUE_TABLE = AttributeValueRepository::TABLE_NAME;
    protected const ATTRIBUTE_TABLE = AttributeRepository::TABLE_NAME;
    protected const RELATION_INDEX_TABLE = RelationInheritanceIndexRepository::TABLE_NAME;

    /**
     * Configure the command by defining the name, options and arguments.
     */
    protected function configure(): void
    {
        $this->setDescription('Fixes duplicate attribute values.');
    }

    /**
     * Executes the command to fix duplicate attribute values.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $io->section('Fetching duplicate attribute values');
        $records = $this->fetchDuplicateAttributeValues();
        if (count($records) > 0) {
            $io->writeLn(sprintf('Found %s duplicate attribute values', count($records)));
            $io->section('Try and determine correct attribute value');
            $io->progressStart(count($records));
            foreach ($records as $index => $record) {
                // attribute_count inheritance_count
                $riiExists = (int)$record['inheritance_count'] > 0;

                $attributeValues = $this->fetchAttributeValueData($record['attruid'], $record['product']);
                $firstAttribute = $attributeValues[0] ?? [];

                if (
                    !empty($firstAttribute)
                    && $firstAttribute['product_attrval_attribute'] === $firstAttribute['parent_attrval_attribute']
                    && $firstAttribute['product_parent'] === $firstAttribute['parent_attrval_product']
                    && $riiExists
                ) {
                    if ($firstAttribute['product_attrval_value'] !== $firstAttribute['parent_attrval_value']) {
                        $this->updateAttributeValueValue(
                            $firstAttribute['product_attrval_uid'],
                            $firstAttribute['parent_attrval_value']
                        );
                    }
                    $this->removeDuplicateAttributeValuesAndRii($attributeValues);
                }
                $io->progressAdvance();
            }
            $io->progressFinish();
        } else {
            $io->success('No duplicate attribute values found');
        }

        return true;
    }

    /**
     * Checks and removes duplicate attribute values and rii if exists.
     *
     * @param array $attributeValues
     * @return void
     */
    protected function removeDuplicateAttributeValuesAndRii(array $attributeValues): void
    {
        foreach ($attributeValues as $avIndex => $attributeValue) {
            if ($avIndex > 0) {
                $this->removeAttributeValueRecord($attributeValue['product_attrval_uid']);
                if ($attributeValue['tprii_uid_parent']) {
                    $this->removeRelationInheritanceIndexRecord(
                        $attributeValue['tprii_uid_parent'],
                        $attributeValue['product_attrval_uid'],
                        $attributeValue['product_attrval_product']
                    );
                }
            }
        }
    }

    /**
     * Fetch duplicate attribute values.
     *
     * @return array
     */
    protected function fetchDuplicateAttributeValues(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::ATTRIBUTEVALUE_TABLE);
        $queryBuilder->getRestrictions()->removeAll();
        $records = $queryBuilder
            ->select('attrval.product', 'attr.uid as attruid', 'attr.label')
            ->addSelectLiteral('COUNT(attrval.attribute) as attribute_count')
            ->addSelectLiteral('COUNT(tprii.uid_child) as inheritance_count')
            ->from(self::ATTRIBUTEVALUE_TABLE, 'attrval')
            ->join(
                'attrval',
                self::ATTRIBUTE_TABLE,
                'attr',
                $queryBuilder->expr()->eq(
                    'attr.uid',
                    $queryBuilder->quoteIdentifier('attrval.attribute')
                )
            )
            ->join(
                'attrval',
                self::PRODUCT_TABLE,
                'product',
                $queryBuilder->expr()->eq(
                    'product.uid',
                    $queryBuilder->quoteIdentifier('attrval.product')
                )
            )
            ->leftJoin(
                'attrval',
                self::RELATION_INDEX_TABLE,
                'tprii',
                $queryBuilder->expr()->eq(
                    'tprii.uid_child',
                    $queryBuilder->quoteIdentifier('attrval.uid')
                )
            )
            ->where(
                $queryBuilder->expr()->eq(
                    'attrval.deleted',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'product.deleted',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'attr.deleted',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->gt(
                    'product.parent',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                )
            )
            ->groupBy('product', 'attr.uid')
            ->having('attribute_count > 1')
            ->orHaving('attribute_count != inheritance_count')
            ->execute()
            ->fetchAll();

        return $records;
    }

    /**
     * Fetch Attribute Value data for attrbute and product.
     *
     * @param int $attribute
     * @param int $product
     * @return array
     */
    protected function fetchAttributeValueData(int $attribute, int $product): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::ATTRIBUTEVALUE_TABLE);
        $queryBuilder->getRestrictions()->removeAll();
        $records = $queryBuilder
            ->select(
                'product_attributevalue.uid as product_attrval_uid',
                'product_attributevalue.product as product_attrval_product',
                'product_attributevalue.attribute as product_attrval_attribute',
                'product_attributevalue.value as product_attrval_value',
                'tprii.uid_parent as tprii_uid_parent',
                'product.parent as product_parent',
                'parent_attributevalue.product as parent_attrval_product',
                'parent_attributevalue.attribute as parent_attrval_attribute',
                'parent_attributevalue.value as parent_attrval_value'
            )
            ->addSelectLiteral(
                'FROM_UNIXTIME(product_attributevalue.tstamp, \'%Y-%m-%d %H:%i:%s\') as product_attrval_tstamp'
            )
            ->addSelectLiteral(
                'FROM_UNIXTIME(product_attributevalue.crdate, \'%Y-%m-%d %H:%i:%s\') as product_attrval_crdate'
            )
            ->from(self::ATTRIBUTEVALUE_TABLE, 'product_attributevalue')
            ->leftJoin(
                'product_attributevalue',
                self::PRODUCT_TABLE,
                'product',
                $queryBuilder->expr()->eq(
                    'product.uid',
                    $queryBuilder->quoteIdentifier('product_attributevalue.product')
                )
            )
            ->leftJoin(
                'product_attributevalue',
                self::RELATION_INDEX_TABLE,
                'tprii',
                (string)$queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq(
                        'tprii.uid_child',
                        $queryBuilder->quoteIdentifier('product_attributevalue.uid')
                    ),
                    $queryBuilder->expr()->eq(
                        'tprii.child_parent_id',
                        $queryBuilder->quoteIdentifier('product_attributevalue.product')
                    ),
                    $queryBuilder->expr()->eq(
                        'tprii.child_parent_tablename',
                        $queryBuilder->createNamedParameter(
                            'tx_pxaproductmanager_domain_model_product',
                            \PDO::PARAM_STR
                        )
                    ),
                    $queryBuilder->expr()->eq(
                        'tprii.tablename',
                        $queryBuilder->createNamedParameter(
                            'tx_pxaproductmanager_domain_model_attributevalue',
                            \PDO::PARAM_STR
                        )
                    )
                )
            )
            ->leftJoin(
                'tprii',
                self::ATTRIBUTEVALUE_TABLE,
                'parent_attributevalue',
                $queryBuilder->expr()->eq(
                    'parent_attributevalue.uid',
                    $queryBuilder->quoteIdentifier('tprii.uid_parent')
                )
            )
            ->where(
                $queryBuilder->expr()->eq(
                    'product_attributevalue.product',
                    $queryBuilder->createNamedParameter($product, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'product_attributevalue.attribute',
                    $queryBuilder->createNamedParameter($attribute, \PDO::PARAM_INT)
                )
            )
            ->orderBy('product_attributevalue.tstamp', 'DESC')
            ->execute()
            ->fetchAll();

        return $records;
    }

    /**
     * Update Attribute Value value.
     *
     * @param int $uid
     * @param string $value
     * @return void
     */
    protected function updateAttributeValueValue(int $uid, string $value): void
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::ATTRIBUTEVALUE_TABLE);
        $queryBuilder
            ->update(self::ATTRIBUTEVALUE_TABLE)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                )
            )
            ->set('value', $value)
            ->execute();
    }

    /**
     * Remove attribute value record.
     *
     * @param int $uid
     * @return void
     */
    protected function removeAttributeValueRecord(int $uid): void
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::ATTRIBUTEVALUE_TABLE);

        $queryBuilder
            ->delete(self::ATTRIBUTEVALUE_TABLE)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                )
            )
            ->execute();
    }

    /**
     * Remove RelationInheritanceIndexRecord.
     *
     * @param int $uidParent
     * @param int $uidChild
     * @param int $childParentId
     * @return void
     */
    protected function removeRelationInheritanceIndexRecord(
        int $uidParent,
        int $uidChild,
        int $childParentId
    ): void {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::RELATION_INDEX_TABLE);

        $queryBuilder
            ->delete(self::RELATION_INDEX_TABLE)
            ->where(
                $queryBuilder->expr()->eq('uid_parent', $queryBuilder->createNamedParameter($uidParent)),
                $queryBuilder->expr()->eq('uid_child', $queryBuilder->createNamedParameter($uidChild)),
                $queryBuilder->expr()->eq('tablename', $queryBuilder->createNamedParameter(self::ATTRIBUTEVALUE_TABLE)),
                $queryBuilder->expr()->eq('child_parent_id', $queryBuilder->createNamedParameter($childParentId)),
                $queryBuilder->expr()->eq(
                    'child_parent_tablename',
                    $queryBuilder->createNamedParameter(self::PRODUCT_TABLE)
                )
            )
            ->execute();
    }
}
