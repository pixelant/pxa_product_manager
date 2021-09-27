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

use Pixelant\PxaProductManager\Domain\Repository\InheritanceQueueRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Utility\DataInheritanceUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UpdateInheritanceCommand extends Command
{
    /**
     * Configure the command by defining the name, options and arguments.
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Checks and if needed, updates inheritance to child products.')
            ->addOption(
                'rebuild-queue',
                'r',
                InputOption::VALUE_OPTIONAL,
                'Rebuild queue before processing queue.' . PHP_EOL .
                'You can use --rebuild-queue true or -r true when running command'
            )
            ->addOption(
                'limit',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Limit how many parent products to process per run.',
                5
            );
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

        if ($input->getOption('rebuild-queue') === 'true') {
            $io->section('Rebuilding inheritance queue.');
            $count = $this->rebuildInheritanceQueue();
            $io->writeln($count . ' product(s) where added to inheritance queue.');
        }

        $products = $this->fetchInheritanceQueue((int)$input->getOption('limit'));
        if (empty($products)) {
            $io->success('No products in queue, no processing needed.');

            return true;
        }

        $queueCount = $this->fetchInheritanceQueueCount();
        $io->section(
            'Check inheritance status for ' . count($products) . ' of ' . $queueCount . ' product(s) total in queue.'
        );

        $updated = 0;
        foreach ($products as $index => $product) {
            $io->section(
                sprintf(
                    'Check inheritance for product [%s] (%s/%s)',
                    $product['product_uid'],
                    $index + 1,
                    count($products)
                )
            );

            $inheritdData = DataInheritanceUtility::inheritDataFromParent($product['product_uid']);

            if (!empty($inheritdData)) {
                $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
                $dataHandler->start($inheritdData['data'], $inheritdData['cmd']);
                $dataHandler->process_datamap();
                $dataHandler->process_cmdmap();
            }

            if (
                !empty($inheritdData)
                && isset($inheritdData['data'][ProductRepository::TABLE_NAME])
            ) {
                foreach ($inheritdData['data'][ProductRepository::TABLE_NAME] as $id => $data) {
                    $updated++;
                    unset($data['is_inherited']);
                    $io->writeln(
                        'Inherited data was updated for product: ' . $id .
                        ', fields: ' . implode(',', array_keys($data))
                    );
                }
            }

            $this->removeProductFromInheritanceQueue($product['product_uid']);
        }

        $io->success(
            sprintf(
                'Inheritance status was checked for %s product(s) and updated on %s product(s).',
                count($products),
                $updated
            )
        );

        return true;
    }

    /**
     * Fetch all products with parent.
     *
     * @return array
     */
    protected function fetchChildProducts(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(ProductRepository::TABLE_NAME);
        $queryBuilder->getRestrictions()->removeAll();

        $records = $queryBuilder->select('uid')
            ->from(ProductRepository::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'deleted',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->gt(
                    'parent',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                )
            )
            ->orderBy('tstamp', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return $records ?? [];
    }

    /**
     * Rebuild inheritance queue and return number of products added to queue.
     *
     * @return int
     */
    protected function rebuildInheritanceQueue(): int
    {
        $this->emptyInheritanceQueue();

        $allChilds = $this->fetchChildProducts();
        foreach ($allChilds as $index => $child) {
            $this->addProductToInheritanceQueue($child['uid']);
        }

        return count($allChilds);
    }

    /**
     * Fetch number if products in inheritance queue.
     *
     * @return int
     */
    protected function fetchInheritanceQueueCount(): int
    {
        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(InheritanceQueueRepository::TABLE_NAME);
        $queryBuilder->getRestrictions()->removeAll();

        return $queryBuilder
            ->addSelectLiteral('COUNT(*) as cnt')
            ->from(InheritanceQueueRepository::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'indexed',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'errors',
                    $queryBuilder->createNamedParameter('', \PDO::PARAM_STR)
                )
            )
            ->execute()
            ->fetchOne();
    }

    /**
     * Fetch products to update inheritance for.
     *
     * @return int
     */
    protected function fetchInheritanceQueue(int $limit): array
    {
        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(InheritanceQueueRepository::TABLE_NAME);
        $queryBuilder->getRestrictions()->removeAll();

        return $queryBuilder
            ->addSelectLiteral('product_uid')
            ->from(InheritanceQueueRepository::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'indexed',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'errors',
                    $queryBuilder->createNamedParameter('', \PDO::PARAM_STR)
                )
            )
            ->setMaxResults($limit)
            ->execute()
            ->fetchAllAssociative();
    }

    /**
     * Empty inheritance queue.
     *
     * @return void
     */
    protected function emptyInheritanceQueue(): void
    {
        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(InheritanceQueueRepository::TABLE_NAME);
        $queryBuilder->getRestrictions()->removeAll();

        $queryBuilder->delete(InheritanceQueueRepository::TABLE_NAME)
            ->execute();
    }

    /**
     * Add product uid to inheritance queue.
     *
     * @param int $product_uid
     * @return void
     */
    protected function addProductToInheritanceQueue(int $product_uid): void
    {
        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(InheritanceQueueRepository::TABLE_NAME);
        $queryBuilder->getRestrictions()->removeAll();

        $queryBuilder->insert(InheritanceQueueRepository::TABLE_NAME)
            ->values([
                'product_uid' => $product_uid,
                'indexed' => 0,
                'errors' => '',
            ])
            ->execute();
    }

    /**
     * Remove product from queue by uid.
     *
     * @param int $product_uid
     * @return void
     */
    protected function removeProductFromInheritanceQueue(int $product_uid): void
    {
        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(InheritanceQueueRepository::TABLE_NAME);
        $queryBuilder->getRestrictions()->removeAll();

        $queryBuilder->delete(InheritanceQueueRepository::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'product_uid',
                    $queryBuilder->createNamedParameter($product_uid, \PDO::PARAM_INT)
                ),
            )
            ->execute();
    }
}
