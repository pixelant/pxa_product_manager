<?php

namespace Pixelant\PxaProductManager\LinkHandler;

use Pixelant\PxaProductManager\Traits\TranslateBeTrait;
use TYPO3\CMS\Backend\Form\Element\InputLinkElement;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class LinkHandlingFormData
 * @package Pixelant\PxaProductManager\LinkHandler
 */
class LinkHandlingFormData
{
    use TranslateBeTrait;

    /**
     * @var IconFactory
     */
    protected $iconFactory;

    /**
     * Initialization
     */
    public function __construct()
    {
        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
    }

    /**
     * Data for link fields preview
     *
     * @param array $linkData
     * @param array $linkParts
     * @param array $data
     * @param InputLinkElement $inputLinkElement
     * @return array
     */
    public function getFormData(
        /** @noinspection PhpUnusedParameterInspection */ array $linkData,
        array $linkParts,
        array $data,
        InputLinkElement $inputLinkElement
    ): array {
        if (isset($linkData['category'])) {
            $row = $this->getRow((int)$linkData['category'], 'sys_category');

            $text = sprintf(
                '%s [%s (%d)]',
                BackendUtility::getRecordTitle('sys_category', $row),
                $this->translate('be.category_link_handler'),
                $linkData['category']
            );
            $icon = $this->iconFactory->getIconForRecord('sys_category', $row, Icon::SIZE_SMALL)->render();
        } elseif (isset($linkData['product'])) {
            $row = $this->getRow((int)$linkData['product'], 'tx_pxaproductmanager_domain_model_product');

            $text = sprintf(
                '%s [%s (%d)]',
                BackendUtility::getRecordTitle('tx_pxaproductmanager_domain_model_product', $row),
                $this->translate('be.product_link_handler'),
                $linkData['product']
            );
            $icon = $this->iconFactory->getIconForRecord(
                'tx_pxaproductmanager_domain_model_product',
                $row,
                Icon::SIZE_SMALL
            )->render();
        } else {
            $text = 'Category or product should be set for PM links.';
            $icon = '';
        }

        return [
            'text' => $text,
            'icon' => $icon
        ];
    }

    /**
     * Get row record
     *
     * @param int $uid
     * @param string $table
     * @return array
     */
    protected function getRow(int $uid, string $table): array
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        $row = $queryBuilder->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                )
            )
            ->execute()
            ->fetch();

        return $row ?: [];
    }
}
