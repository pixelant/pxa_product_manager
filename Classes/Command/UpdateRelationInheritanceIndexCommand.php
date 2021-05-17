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

use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Domain\Repository\RelationInheritanceIndexRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UpdateRelationInheritanceIndexCommand extends Command
{
    protected const PRODUCT_TABLE = ProductRepository::TABLE_NAME;
    protected const ATTRIBUTEVALUE_TABLE = AttributeValueRepository::TABLE_NAME;
    protected const RELATION_INDEX_TABLE = RelationInheritanceIndexRepository::TABLE_NAME;

    /**
     * Configure the command by defining the name, options and arguments.
     */
    protected function configure(): void
    {
        $this->setDescription('Update relation inheritance index.');
    }

    /**
     * Executes the command to update relation inheritance index.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $io->section('Fetching missing relations');
        $records = $this->fetchMissingChildRelations();

        if (count($records) > 0) {
            $io->writeLn(sprintf('Found %s missing relations', count($records)));

            $io->section('Creating missing relations');
            $io->progressStart(count($records));
            foreach ($records as $record) {
                $this->addRelation($record);
                $io->progressAdvance();
            }
            $io->progressFinish();
        } else {
            $io->success('No missing relations found');
        }

        return true;
    }

    /**
     * Add a relation to the inheritance index.
     *
     * @param array $record
     * @return void
     */
    protected function addRelation(array $record): void
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::RELATION_INDEX_TABLE);

        $queryBuilder
            ->insert(self::RELATION_INDEX_TABLE)
            ->values([
                'uid_parent' => $record['uid_parent'],
                'uid_child' => $record['uid_child'],
                'tablename' => $record['tablename'],
                'child_parent_id' => $record['child_parent_id'],
                'child_parent_tablename' => $record['child_parent_tablename'],
            ])
            ->execute();
    }

    /**
     * Fetch missing relation inheritance.
     *
     * @return array
     */
    protected function fetchMissingChildRelations(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::RELATION_INDEX_TABLE);
        $queryBuilder->getRestrictions()->removeAll();

        $records = $queryBuilder
            ->select(
                'tpdmaparent.uid as uid_parent',
                'tpdma.uid as uid_child',
                'tpdmp.uid as child_parent_id'
            )
            ->addSelectLiteral(
                $queryBuilder->expr()->count('*'),
                '\'' . self::PRODUCT_TABLE . '\' as child_parent_tablename',
                '\'' . self::ATTRIBUTEVALUE_TABLE . '\' as tablename'
            )
            ->from(self::PRODUCT_TABLE, 'tpdmp')
            ->leftJoin(
                'tpdmp',
                self::ATTRIBUTEVALUE_TABLE,
                'tpdma',
                $queryBuilder->expr()->eq(
                    'tpdma.product',
                    $queryBuilder->quoteIdentifier('tpdmp.uid')
                )
            )
            ->leftJoin(
                'tpdmp',
                self::ATTRIBUTEVALUE_TABLE,
                'tpdmaparent',
                (string)$queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq(
                        'tpdmaparent.product',
                        $queryBuilder->quoteIdentifier('tpdmp.parent')
                    ),
                    $queryBuilder->expr()->eq(
                        'tpdmaparent.attribute',
                        $queryBuilder->quoteIdentifier('tpdma.attribute')
                    )
                )
            )
            ->leftJoin(
                'tpdma',
                self::RELATION_INDEX_TABLE,
                'tprii',
                (string)$queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq(
                        'tprii.uid_child',
                        $queryBuilder->quoteIdentifier('tpdma.uid')
                    ),
                    $queryBuilder->expr()->eq(
                        'tprii.uid_parent',
                        $queryBuilder->quoteIdentifier('tpdmaparent.uid')
                    )
                )
            )
            ->where(
                $queryBuilder->expr()->gt(
                    'tpdmp.parent',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->isNotNull(
                    'tpdma.uid'
                ),
                $queryBuilder->expr()->isNotNull(
                    'tpdmaparent.uid'
                ),
                $queryBuilder->expr()->isNull(
                    'tprii.uid_child'
                )
            )
            ->groupBy(
                'tpdmp.uid',
                'tpdma.uid',
                'tpdmaparent.uid'
            )
            ->execute()
            ->fetchAll();

        return $records;
    }
}
