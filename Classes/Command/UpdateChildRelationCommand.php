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

use Symfony\Component\Console\Command\Command;
<<<<<<< HEAD
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UpdateChildRelationCommand extends Command
{
    protected const PRODUCT_TABLE = 'tx_pxaproductmanager_domain_model_product';
    protected const ATTRIBUTEVALUE_TABLE = 'tx_pxaproductmanager_domain_model_attributevalue';
    protected const RELATION_INDEX_TABLE = 'tx_pxaproductmanager_relation_inheritance_index';

    /**
     * Configure the command by defining the name, options and arguments.
     */
    protected function configure(): void
    {
        $this->setDescription('Update child relations table.');
    }

    /**
     * Executes the command for showing sys_log entries.
=======
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateChildRelationCommand extends Command
{
    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Update child relations table.')
           ->setHelp('Prints a list of recent sys_log entries.' . LF . 'If you want to get more detailed information, use the --verbose option.');
    }

    /**
     * Executes the command for showing sys_log entries
>>>>>>> cab139966027ca64e66bcdd60aaded1d4b951695
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

<<<<<<< HEAD
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
     * Add child relation.
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
     * Fetch missing child relation.
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
                '\'tx_pxaproductmanager_domain_model_product\' as child_parent_tablename',
                '\'tx_pxaproductmanager_domain_model_attributevalue\' as tablename'
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
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq(
                        'tpdmaparent.product',
                        $queryBuilder->quoteIdentifier('tpdmp.parent')
                    ),
                    $queryBuilder->expr()->eq(
                        'tpdmaparent.attribute',
                        $queryBuilder->quoteIdentifier('tpdma.attribute')
                    )
                )->__toString()
            )
            ->leftJoin(
                'tpdma',
                self::RELATION_INDEX_TABLE,
                'tprii',
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq(
                        'tprii.uid_child',
                        $queryBuilder->quoteIdentifier('tpdma.uid')
                    ),
                    $queryBuilder->expr()->eq(
                        'tprii.uid_parent',
                        $queryBuilder->quoteIdentifier('tpdmaparent.uid')
                    )
                )->__toString()
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
            ->fetchAllAssociative();

        return $records;
    }
=======
        // ...
        $io->writeln('Write something');
        return true;
    }
>>>>>>> cab139966027ca64e66bcdd60aaded1d4b951695
}
