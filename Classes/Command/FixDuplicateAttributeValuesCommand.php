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
use Pixelant\PxaProductManager\Utility\DataInheritanceUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Backend\Utility\BackendUtility;
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

        $inheritDataOnProducts = [];
        $outputAttributeValues = false;

        $records = $this->fetchDuplicateAttributeValues();
        if (count($records) > 0) {
            $io->writeLn(sprintf('Found %s product attributes with multiple attribute values', count($records)));
            $io->section('Try and determine correct attribute value');
            $io->progressStart(count($records));
            foreach ($records as $index => $record) {
                $attributeValues = $this->fetchAttributeValuesByProductAndAttribute(
                    (int)$record['product'],
                    (int)$record['attribute']
                );

                if ((int)$record['product'] === 0) {
                    foreach ($attributeValues as $attributeValue) {
                        $io->writeln('DELETE attribute value with uid: ' . $attributeValue['uid'] . ' - PRODUCT 0');
                        $this->removeAttributeValueRecord((int)$attributeValue['uid']);
                    }

                    continue;
                }

                $this->determineAttributeValueScores($attributeValues);

                usort($attributeValues, function ($a, $b) {
                    return $a['score'] < $b['score'];
                });

                if ($outputAttributeValues) {
                    $io->table(array_keys($attributeValues[0]), $attributeValues, true);
                }

                if (!in_array('score', array_keys($attributeValues), false)) {
                    $io->writeln(
                        sprintf(
                            'SKIPPING attribute values for product %s, attribute %s, no score could be calculated.',
                            $record['product'],
                            $record['attribute']
                        )
                    );
                    $io->table(array_keys($attributeValues[0]), $attributeValues, true);

                    continue;
                }

                $this->removeAttributeValuesAccordingScore($attributeValues, $inheritDataOnProducts);

                $io->progressAdvance();
            }
            $io->progressFinish();

            $inheritDataOnProducts = array_unique($inheritDataOnProducts);
            if (count($inheritDataOnProducts) > 0) {
                $io->writeLn(sprintf(
                    'Found %s products that have attributes not up to date with parents',
                    count($inheritDataOnProducts)
                ));
                $io->section('Update inheritage on products');

                $io->progressStart(count($inheritDataOnProducts));
                foreach ($inheritDataOnProducts as $uid) {
                    $io->writeln('Would try and update product: ' . $uid);
                    DataInheritanceUtility::inheritDataFromParent($uid);

                    $io->progressAdvance();
                }
                $io->progressFinish();
            }
        } else {
            $io->success('No duplicate attribute values found');
        }

        return true;
    }

    /**
     * Remove duplicate attribute values according score.
     *
     * @param array $attributeValues
     * @param array $inheritDataOnProducts
     * @return void
     */
    protected function removeAttributeValuesAccordingScore(array $attributeValues, array &$inheritDataOnProducts): void
    {
        foreach ($attributeValues as $sIndex => $attributeValue) {
            if ($sIndex > 0) {
                $this->removeAttributeValueRecord((int)$attributeValue['uid']);
            } else {
                // If first records score is zero, we need to update inheritance for product.
                if ($attributeValue['score'] === 0) {
                    $inheritDataOnProducts[] = $attributeValue['product'];
                }
            }
        }
    }

    /**
     * Fetch all Attribute Values connected to same product and attribute more than once.
     *
     * @return array
     */
    protected function fetchDuplicateAttributeValues(): array
    {
        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::ATTRIBUTEVALUE_TABLE);
        $queryBuilder->getRestrictions()->removeAll();

        $records = $queryBuilder->select('product', 'attribute')
            ->addSelectLiteral('COUNT(*) as cnt')
            ->from(self::ATTRIBUTEVALUE_TABLE)
            ->where(
                $queryBuilder->expr()->eq(
                    'deleted',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                )
            )
            ->groupBy('product', 'attribute')
            ->having('cnt > 1')
            ->execute()
            ->fetchAllAssociative();

        return $records;
    }

    /**
     * Fetch attribute values by product and attribute.
     *
     * @param int $product
     * @param int $attribute
     * @return array
     */
    protected function fetchAttributeValuesByProductAndAttribute(int $product, int $attribute): array
    {
        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::ATTRIBUTEVALUE_TABLE);
        $queryBuilder->getRestrictions()->removeAll();

        $records = $queryBuilder->select('*')
            ->from(self::ATTRIBUTEVALUE_TABLE)
            ->where(
                $queryBuilder->expr()->eq(
                    'product',
                    $queryBuilder->createNamedParameter($product, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'attribute',
                    $queryBuilder->createNamedParameter($attribute, \PDO::PARAM_INT)
                )
            )
            ->orderBy('crdate')
            ->execute()
            ->fetchAllAssociative();

        return $records;
    }

    /**
     * Fetch attribute value by id.
     *
     * @param int $id
     * @return array
     */
    protected function fetchAttributeValueById(int $id): array
    {
        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::ATTRIBUTEVALUE_TABLE);
        $queryBuilder->getRestrictions()->removeAll();

        $records = $queryBuilder->select('*')
            ->from(self::ATTRIBUTEVALUE_TABLE)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)
                )
            )
            ->orderBy('crdate')
            ->execute()
            ->fetchAllAssociative();

        return $records[0] ?? [];
    }

    /**
     * Try and create a score to be able to decide what attribute values to keep.
     *
     * @param array $attributeValues
     * @return void
     * @throws \Exception
     */
    protected function determineAttributeValueScores(array &$attributeValues): void
    {
        $values = array_column($attributeValues, 'value');
        $sysLanguageUid = array_column($attributeValues, 'sys_language_uid');
        $localizedFromUid = array_column($attributeValues, 'l10n_parent');
        $copiedFromUid = array_column($attributeValues, 't3_origuid');

        $allValuesAreSame = count(array_unique($values)) === 1;
        $allLanguagesAreSame = count(array_unique($sysLanguageUid)) === 1;
        $allLocalizedFromSame = count(array_unique($localizedFromUid)) === 1;
        $allCopiedFromSame = count(array_unique($copiedFromUid)) === 1;

        // All attribute values are fetched by attribute and product,
        // so all attribute values have same product.
        // Use first attribute value for product and attribute id.
        $parentProduct = BackendUtility::getRecord(
            self::PRODUCT_TABLE,
            $attributeValues[0]['product'],
            'parent'
        )['parent'] ?? [];

        $parentAttribute = [];
        if (!empty($parentProduct)) {
            $parentAttribute = $this->fetchAttributeValuesByProductAndAttribute(
                (int)$parentProduct,
                (int)$attributeValues[0]['attribute']
            ) ?? [];

            if (count($parentAttribute) > 1) {
                throw new \Exception(
                    sprintf(
                        'Parent product have duplicate attribute values (product: %s, attribute: %s)',
                        $parentProduct,
                        $attributeValues[0]['attribute']
                    ),
                    1
                );
            }

            $parentAttribute = $parentAttribute[0];
        }

        try {
            // Use index in score, attribute values are sorted in query.
            foreach ($attributeValues as $index => &$attributeValue) {
                $attributeValue['parent_attribute_value'] = $parentAttribute['value'];
                $attributeValue['parent_attribute_uid'] = $parentAttribute['uid'];
                $attributeValue['parent_product_uid'] = $parentProduct;

                $attributeValue['score'] = $this->determineAttributeValueScore(
                    $index,
                    $attributeValue,
                    $allValuesAreSame,
                    $allLanguagesAreSame,
                    $allLocalizedFromSame,
                    $allCopiedFromSame,
                    $parentAttribute
                );
            }
        } catch (\Throwable $th) {
            $attributeValue['error'] = $th->getMessage();
        }
    }

    /**
     * Calulate attribute value score.
     *
     * @param int $index
     * @param array $attributeValue
     * @param bool $allValuesAreSame
     * @param bool $allLanguagesAreSame
     * @param bool $allLocalizedFromSame
     * @param bool $allCopiedFromSame
     * @param array $parentAttribute
     * @return int
     * @throws \Exception
     */
    protected function determineAttributeValueScore(
        int $index,
        array $attributeValue,
        bool $allValuesAreSame,
        bool $allLanguagesAreSame,
        bool $allLocalizedFromSame,
        bool $allCopiedFromSame,
        array $parentAttribute
    ): int {
        /* caculate most correct attribute value: score,
        * not important what "max score" is just to be accurate by how important the "match" is.
        * compare fields: value, sys_language_uid, l10n_parent, t3_origuid, value
        */
        $score = 0;

        if (
            (string)$parentAttribute['value'] !== (string)$attributeValue['value']
            && $allValuesAreSame
            && !empty($parentAttribute)
        ) {
            throw new \Exception('Could not determine attribute score, no attribute values are correct. (Delete)?', 1);
        }
        // Value is same as "parent"
        if ((string)$parentAttribute['value'] === (string)$attributeValue['value'] && !empty($parentAttribute)) {
            $score += 100;
        }

        // If all attribue values are same, the index indicates how important value is according to query.
        if ($allValuesAreSame) {
            if ($allLanguagesAreSame && $allLocalizedFromSame && $allCopiedFromSame) {
                $score += (100 - ($index * 1));
            }
        }

        return $score;
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
            ->fetchAllAssociative();

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
