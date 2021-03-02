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

class RemoveOrphanAttributeValuesCommand extends Command
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
        $this->setDescription('Removes orphan attribute values, attribute values without a product attached to it.');
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

        $io->section('Fetching orphant attribute values count');
        $count = $this->fetchOrphanAttributeValuesCount();
        if ($count > 0) {
            $io->writeLn(sprintf('Found %s orphan attribute values that will be deleted.', $count));
            $this->removeOrphanAttributeValues();
        } else {
            $io->success('No orphant attribute values found');
        }

        return true;
    }

    /**
     * Fetch orphan attribute values count.
     * Atrribute Values with no product attached.
     *
     * @return int
     */
    protected function fetchOrphanAttributeValuesCount(): int
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::ATTRIBUTEVALUE_TABLE);
        $queryBuilder->getRestrictions()->removeAll();
        $count = $queryBuilder
            ->count('*')
            ->from(self::ATTRIBUTEVALUE_TABLE, 'attrval')
            ->where(
                $queryBuilder->expr()->eq(
                    'product',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                )
            )
            ->execute()
            ->fetchColumn(0);

        return $count;
    }

    /**
     * Remove attribute value record.
     *
     * @return void
     */
    protected function removeOrphanAttributeValues(): void
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::ATTRIBUTEVALUE_TABLE);

        $queryBuilder
            ->delete(self::ATTRIBUTEVALUE_TABLE)
            ->where(
                $queryBuilder->expr()->eq(
                    'product',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                )
            )
            ->execute();
    }
}
