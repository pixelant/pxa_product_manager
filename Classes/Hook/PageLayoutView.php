<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Hook;

/**
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

use Pixelant\PxaProductManager\Traits\TranslateBeTrait;
use Pixelant\PxaProductManager\Utility\ConfigurationUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook to display verbose information about pi1 plugin in Web>Page module
 *
 * @package TYPO3
 * @subpackage pxa_product_manager
 */
class PageLayoutView
{
    use TranslateBeTrait;

    /**
     * HR tag
     *
     * @var string
     */
    protected static $hrMarkup = '<hr style="margin: 5px 0;background: #ccc">';

    /**
     * Returns information about this extension's pi1 plugin
     *
     * @param array $params Parameters to the hook
     * @return string Information about pi1 plugin
     */
    public function getExtensionSummary(array $params)
    {
        $result = sprintf(
            '<strong>%s</strong><br>',
            $this->translate('be.extension_info.name')
        );

        $additionalInfo = '';

        if ($params['row']['list_type'] == 'pxaproductmanager_pi1') {
            $flexformData = GeneralUtility::xml2array($params['row']['pi_flexform']);

            $flexFormSettings = [];

            if (is_array($flexformData['data']['sDEF']['lDEF'])) {
                foreach ($flexformData['data'] as $sheet) {
                    $rawSettings = $sheet['lDEF'];
                    foreach ($rawSettings as $field => $rawSetting) {
                        $this->flexFormToArray($field, $rawSetting['vDEF'], $flexFormSettings);
                    }
                }
            }

            // if flexform data is found
            $switchableControllerActions = $flexFormSettings['switchableControllerActions'];
            if (!empty($switchableControllerActions)) {
                list($action) = GeneralUtility::trimExplode(';', $switchableControllerActions);

                // translate the first action into its translation
                $actionTranslationKey = str_replace(
                    '->',
                    '_',
                    GeneralUtility::camelCaseToLowerCaseUnderscored($action)
                );
                $actionTranslation = $this->translate('flexform.mode.' . $actionTranslationKey);

                $additionalInfo .= $actionTranslation;
                if (is_array($flexFormSettings['settings'])) {
                    switch ($action) {
                        case 'Product->list':
                            $additionalInfo .= $this->getListModeInfo($flexFormSettings['settings']);
                            break;
                        case 'Product->groupedList':
                            $additionalInfo .= $this->getGroupedListModeInfo($flexFormSettings['settings']);
                            break;
                        case 'Product->show':
                            $additionalInfo .= $this->getSingleViewModeInfo($flexFormSettings['settings']);
                            break;
                        case 'Product->lazyList':
                            $additionalInfo .= $this->getLazyListModeInfo($flexFormSettings['settings']);
                            break;
                        case 'Navigation->show':
                            $additionalInfo .= $this->getNavigationModeInfo($flexFormSettings['settings']);
                            break;
                        case 'Product->wishList':
                            $additionalInfo .= $this->getWishListInfo($flexFormSettings['settings']);
                            break;
                        case 'Product->comparePreView':
                        case 'Product->compareView':
                            $additionalInfo .= $this->getCompareInfo($flexFormSettings['settings']);
                            break;
                        case 'Product->customProductsList':
                            $additionalInfo .= $this->getCustomProductsListInfo($flexFormSettings['settings']);
                            break;
                    }
                }
            } else {
                $additionalInfo .= $this->translate('be.extension_info.mode.not_configured');
            }
        }

        return $result . ($additionalInfo ? '<hr><pre>' . $additionalInfo . '</pre>' : '');
    }

    /**
     * Get info about filters
     *
     * @param string $filters
     * @return string
     */
    protected function getFiltersInfo(string $filters): string
    {
        $filters = GeneralUtility::intExplode(',', $filters, true);
        $info = '<b>' . $this->translate('flexform.filters') . '</b>:';

        if (!empty($filters)) {
            $filtersInfo = '';
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
                'tx_pxaproductmanager_domain_model_filter'
            );
            $statement = $queryBuilder
                ->select('uid', 'name')
                ->from('tx_pxaproductmanager_domain_model_filter')
                ->where(
                    $queryBuilder->expr()->in(
                        'uid',
                        $queryBuilder->createNamedParameter($filters, Connection::PARAM_INT_ARRAY)
                    )
                )
                ->execute();
            while ($filter = $statement->fetch()) {
                $filtersInfo .= ', ' . ($filter['name'] ?: $this->translate('be.extension_info.no_title'));
            }

            $filtersInfo = ltrim($filtersInfo, ',');
        }

        if (!isset($filtersInfo)) {
            $filtersInfo = ' ' . $this->translate('be.extension_info.none');
        }

        return $info . $filtersInfo . '<br>';
    }

    /**
     * Get html preview for NavigationMode mode
     *
     * @param $settings
     * @return string
     */
    public function getNavigationModeInfo(array $settings): string
    {
        $info = '<br>';
        $info .= $this->getPagePidInfo((int)$settings['pagePid']);
        $info .= $this->getCategoriesInfo(
            $this->translate('flexform.navigation_category'),
            $settings['category'] ?? ''
        );

        $info .= $this->getCategoriesInfo(
            $this->translate('flexform.exclude_categories'),
            $settings['excludeCategories'] ?? ''
        );

        $info .= $this->menuGeneraInfo($settings);

        $info .= $this->getCategoriesOrderingsInfo($settings);

        return $info;
    }

    /**
     * Information about navigation options
     *
     * @param array $settings
     * @return string
     */
    protected function menuGeneraInfo(array $settings): string
    {
        $info = sprintf(
            '<b>%s</b>: %s<br>',
            $this->translate('flexform.navigation_expand_all'),
            $this->translate('be.extension_info.checkbox_' .
                ($settings['navigationExpandAll'] ? 'yes' : 'no'))
        );

        $info .= sprintf(
            '<b>%s</b>: %s<br>',
            $this->translate('flexform.navigation_hide_categories_without_products'),
            $this->translate('be.extension_info.checkbox_' .
                ($settings['navigationHideCategoriesWithoutProducts'] ? 'yes' : 'no'))
        );

        return $info;
    }

    /**
     * Get html preview for LazyList mode
     *
     * @param $settings
     * @return string
     */
    public function getLazyListModeInfo(array $settings): string
    {
        $info = '<br>';
        $info .= $this->getPagePidInfo((int)$settings['pagePid']);

        $info .= sprintf(
            '<b>%s</b>: %s<br>',
            $this->translate('flexform.limit'),
            (int)$settings['limit'] ?: $this->translate('flexform.no_limit')
        );
        $info .= $this->getProductOrderingInfo($settings);
        $info .= self::$hrMarkup;

        $info .= $this->getCategoriesInfo(
            $this->translate('flexform.allowed_categories'),
            $settings['allowedCategories'] ?? ''
        );
        $info .= $this->getCategoriesInfo(
            $this->translate('flexform.exclude_categories'),
            $settings['excludeCategories'] ?? ''
        );
        $info .= self::$hrMarkup;

        $info .= sprintf(
            '<b>%s</b>: %s<br>',
            $this->translate('flexform.hide_filter_options_no_result'),
            $this->translate('be.extension_info.checkbox_' .
                ($settings['hideFilterOptionsNoResult'] ? 'yes' : 'no'))
        );
        $info .= $this->getFiltersInfo($settings['filters'] ?? '');


        return $info;
    }

    /**
     * Get html preview for single view mode
     *
     * @param $settings
     * @return string
     */
    public function getSingleViewModeInfo(array $settings): string
    {
        $info = '<br>';
        $info .= $this->getPagePidInfo((int)$settings['pagePid']);
        $checkboxes = [
            'showLatestVisitedProducts',
            'enableMessageInsteadOfPage404',
            'showGalleryPagination',
            'showGoBackButton'
        ];

        foreach ($checkboxes as $checkbox) {
            $checkboxLoweCase = GeneralUtility::camelCaseToLowerCaseUnderscored($checkbox);

            $info .= sprintf(
                '<b>%s</b>: %s<br>',
                $this->translate('flexform.' . $checkboxLoweCase),
                $this->translate('be.extension_info.checkbox_' .
                    ($settings[$checkbox] ? 'yes' : 'no'))
            );
        }

        return $info;
    }

    /**
     * Get html preview for LazyList mode
     *
     * @param $settings
     * @return string
     */
    public function getGroupedListModeInfo(array $settings): string
    {
        $info = '<br>';
        $info .= $this->getPagePidInfo((int)$settings['pagePid']);

        $info .= $this->getProductOrderingInfo($settings);
        $info .= self::$hrMarkup;

        $info .= $this->getCategoriesInfo(
            $this->translate('flexform.allowed_categories'),
            $settings['category'] ?? ''
        );
        $info .= $this->getCategoriesInfo(
            $this->translate('flexform.exclude_categories'),
            $settings['excludeCategories'] ?? ''
        );

        $info .= self::$hrMarkup;
        $checkboxes = [
            'showLatestVisitedProducts',
            'enableMessageInsteadOfPage404',
            'showGalleryPagination',
            'showGoBackButton'
        ];

        foreach ($checkboxes as $checkbox) {
            if ($checkbox === 'hr') {
                $info .= self::$hrMarkup;
                continue;
            }
            $checkboxLowerCase = GeneralUtility::camelCaseToLowerCaseUnderscored($checkbox);

            $info .= sprintf(
                '<b>%s</b>: %s<br>',
                $this->translate('flexform.' . $checkboxLowerCase),
                $this->translate('be.extension_info.checkbox_' .
                    ($settings[$checkbox] ? 'yes' : 'no'))
            );
        }

        $info .= $this->getCategoriesOrderingsInfo($settings);

        return $info;
    }

    /**
     * Get html preview for list mode
     *
     * @param $settings
     * @return string
     */
    protected function getListModeInfo(array $settings): string
    {
        $info = '<br>';
        $info .= $this->getPagePidInfo((int)$settings['pagePid']);
        $info .= $this->getCategoriesInfo(
            $this->translate('flexform.navigation_category'),
            $settings['category'] ?? ''
        );

        $info .= $this->getProductOrderingInfo($settings);
        $info .= self::$hrMarkup;

        $checkboxes = [
            'showNavigationListView',
            'hideNavigationListViewOnDetailMode',
            'navigationExpandAll',
            'navigationHideCategoriesWithoutProducts',
            'hr',
            'showLatestVisitedProducts',
            'enableMessageInsteadOfPage404',
            'showGalleryPagination',
            'showGoBackButton'
        ];

        foreach ($checkboxes as $checkbox) {
            if ($checkbox === 'hr') {
                $info .= self::$hrMarkup;
                continue;
            }
            $checkboxLowerCase = GeneralUtility::camelCaseToLowerCaseUnderscored($checkbox);

            $info .= sprintf(
                '<b>%s</b>: %s<br>',
                $this->translate('flexform.' . $checkboxLowerCase),
                $this->translate('be.extension_info.checkbox_' .
                    ($settings[$checkbox] ? 'yes' : 'no'))
            );
        }

        if ((int)$settings['showNavigationListView'] === 1) {
            $info .= $this->getCategoriesOrderingsInfo($settings);
        }

        return $info;
    }

    /**
     * Ordering info for categories
     *
     * @param array $settings
     * @return string
     */
    protected function getCategoriesOrderingsInfo(array $settings): string
    {
        $info = self::$hrMarkup;

        $info .= '<b>' . $this->translate('flexform.categories_sorting') . '</b><br>';
        $info .= sprintf(
            '<b>%s</b>: %s<br>',
            $this->translate('flexform.product_sortby'),
            $this->translate('flexform.sortby_' . $settings['orderCategoriesBy'])
        );
        $info .= sprintf(
            '<b>%s</b>: %s<br>',
            $this->translate('flexform.product_sort_direction'),
            $this->translate('flexform.sort_direction_' . $settings['orderCategoriesDirection'])
        );

        return $info;
    }

    /**
     * Information about products order
     *
     * @param array $settings
     * @return string
     */
    protected function getProductOrderingInfo(array $settings): string
    {
        $info = sprintf(
            '<b>%s</b>: %s<br>',
            $this->translate('flexform.product_sortby'),
            $this->translate('flexform.sortby_' . $settings['orderProductBy'])
        );

        $info .= sprintf(
            '<b>%s</b>: %s<br>',
            $this->translate('flexform.product_sort_direction'),
            $this->translate(
                $settings['orderProductDirection'] === 'desc'
                    ? 'flexform.sort_direction_desc'
                    : 'flexform.sort_direction_asc'
            )
        );

        return $info;
    }

    /**
     * Summary info for page
     *
     * @param int $pageUid
     * @return string
     */
    protected function getPagePidInfo(int $pageUid): string
    {
        // Set the pagePid using the flexform -> typoscript -> 0 priority
        $pageUid = $pageUid ?: ConfigurationUtility::getSettings((int)GeneralUtility::_GP('id'))['pagePid'];
        $pageUid = (int)$pageUid;

        if ($pageUid) {
            $pageRecord = BackendUtility::getRecord(
                'pages',
                $pageUid,
                'title'
            );

            if ($pageRecord !== null) {
                return sprintf(
                    '<b>%s</b>: %s (Uid: %d)<br>',
                    $this->translate('be.extension_info.page_pid'),
                    $pageRecord['title'],
                    $pageUid
                );
            }
        }

        return '';
    }

    /**
     * Summary info for categories
     *
     * @param string $title
     * @param string $categories
     * @return string
     */
    protected function getCategoriesInfo(string $title, string $categories): string
    {
        $categories = GeneralUtility::intExplode(',', $categories, true);
        $info = '<b>' . $title . '</b>:';

        if (!empty($categories)) {
            $categoriesInfo = '';
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
                'sys_category'
            );
            $statement = $queryBuilder
                ->select('uid', 'title')
                ->from('sys_category')
                ->where(
                    $queryBuilder->expr()->in(
                        'uid',
                        $queryBuilder->createNamedParameter($categories, Connection::PARAM_INT_ARRAY)
                    )
                )
                ->execute();
            while ($category = $statement->fetch()) {
                $categoriesInfo .= ', ' . $category['title'];
            }

            $categoriesInfo = ltrim($categoriesInfo, ',');
        }

        if (!isset($categoriesInfo)) {
            $categoriesInfo = ' ' . $this->translate('be.extension_info.none');
        }

        return $info . $categoriesInfo . '<br>';
    }

    /**
     * Info for compare list
     *
     * @param array $settings
     * @return string
     */
    protected function getCompareInfo(array $settings): string
    {
        $info = '<br>';
        $info .= $this->getPagePidInfo((int)$settings['pagePid']);

        $comparePage = (int)$settings['compareViewPid'];
        if ($comparePage) {
            $pageRecord = BackendUtility::getRecord(
                'pages',
                $comparePage,
                'title'
            );

            if ($pageRecord !== null) {
                $info .= sprintf(
                    '<b>%s</b>: %s<br>',
                    $this->translate('be.extension_info.compare_pid'),
                    $pageRecord['title']
                );
            }
        }

        return $info;
    }

    /**
     * Info for wish list
     *
     * @param array $settings
     * @return string
     */
    protected function getWishListInfo(array $settings): string
    {
        $info = '<br>';
        $info .= $this->getPagePidInfo((int)$settings['pagePid']);

        $info .= sprintf(
            '<b>%s</b>: %s<br>',
            $this->translate('flexform.enable_order_function'),
            $this->translate('be.extension_info.checkbox_' .
                ($settings['enableOrderFunction'] ? 'yes' : 'no'))
        );

        if ($settings['enableOrderFunction']) {
            $info .= sprintf(
                '<b>%s</b>: %s<br>',
                $this->translate('flexform.order_form_require_login'),
                $this->translate('be.extension_info.checkbox_' .
                    ($settings['orderFormRequireLogin'] ? 'yes' : 'no'))
            );
            $info .= sprintf(
                '<b>%s</b>: %s<br>',
                $this->translate('flexform.need_to_accept_order_terms'),
                $this->translate('be.extension_info.checkbox_' .
                    ($settings['needToAcceptOrderTerms'] ? 'yes' : 'no'))
            );

            if ($settings['needToAcceptOrderTerms']) {
                $info .= sprintf(
                    '<b>%s</b>: %s<br>',
                    $this->translate('flexform.page_terms_link'),
                    $this->getLinkInfo($settings['pageTermsLink'])
                );
            }

            $recipients = GeneralUtility::trimExplode("\n", $settings['orderRecipientsEmails'], true);
            $info .= sprintf(
                '<b>%s</b>: %s<br>',
                $this->translate('flexform.order_recipients_emails'),
                empty($recipients) ? $this->translate('flexform.no_recipients') : implode(', ', $recipients)
            );
        }

        return $info;
    }

    /**
     * Get short info about typolink
     *
     * @param string $typoLink
     * @return string
     */
    protected function getLinkInfo(string $typoLink): string
    {
        if (empty($typoLink)) {
            return $this->translate('be.extension_info.none');
        }

        if (GeneralUtility::isFirstPartOfStr($typoLink, 't3://page?uid=')) {
            $pageUid = (int)substr($typoLink, 14);
            $pageRecord = BackendUtility::readPageAccess($pageUid, '1=1');
            // Is this a real page
            if ($pageRecord['uid']) {
                return $pageRecord['_thePathFull'] . '[' . $pageRecord['uid'] . ']';
            }
        }

        return $typoLink;
    }

    /**
     * @param array $settings
     * @return string
     */
    protected function getCustomProductsListInfo(array $settings): string
    {
        $info = '<br>';

        // Pid
        $info .= $this->getPagePidInfo((int)$settings['pagePid']);

        // Mode
        $info .= sprintf(
            '<b>%s</b>: %s<br>',
            $this->translate('flexform.custom_products_list.mode'),
            $this->translate($settings['customProductsList']['mode'] === 'products' ?
                'flexform.custom_products_list.mode.products' : 'flexform.custom_products_list.mode.category')
        );

        if ($settings['customProductsList']['mode'] === 'category') {
            // Product categories
            $info .= $this->getCategoriesInfo(
                $this->translate('flexform.custom_products_list.products_categories'),
                $settings['customProductsList']['productsCategories'] ?? ''
            );

            // Limit
            $info .= sprintf(
                '<b>%s</b>: %s<br>',
                $this->translate('flexform.limit'),
                (int)$settings['limit'] ?: $this->translate('flexform.no_limit')
            );
        }

        // Selected products
        if ($settings['customProductsList']['mode'] === 'products') {
            $info .= $this->getProductsInfo(
                $this->translate('flexform.custom_products_list.products_to_show'),
                $settings['customProductsList']['productsToShow'] ?? ''
            );
        }

        return $info;
    }

    /**
     * Summary info for products
     *
     * @param string $title
     * @param string $products
     * @return string
     */
    protected function getProductsInfo(string $title, string $products): string
    {
        $categories = GeneralUtility::intExplode(',', $products, true);
        $info = '<b>' . $title . '</b>:';

        if (!empty($products)) {
            $productsInfo = '';
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
                'tx_pxaproductmanager_domain_model_product'
            );
            $statement = $queryBuilder
                ->select('uid', 'name')
                ->from('tx_pxaproductmanager_domain_model_product')
                ->where(
                    $queryBuilder->expr()->in(
                        'uid',
                        $queryBuilder->createNamedParameter($categories, Connection::PARAM_INT_ARRAY)
                    )
                )
                ->execute();
            while ($product = $statement->fetch()) {
                $productsInfo .= ', ' . $product['name'];
            }

            $productsInfo = ltrim($productsInfo, ',');
        }

        if (!isset($productsInfo)) {
            $productsInfo = ' ' . $this->translate('be.extension_info.none');
        }

        return $info . $productsInfo . '<br>';
    }

    /**
     * go through all settings and generate array
     *
     * @param $field
     * @param $value
     * @param $settings
     * @return void
     */
    protected function flexFormToArray($field, $value, &$settings)
    {
        $fieldNameParts = GeneralUtility::trimExplode('.', $field);
        if (count($fieldNameParts) > 1) {
            $name = $fieldNameParts[0];
            unset($fieldNameParts[0]);

            if (!isset($settings[$name])) {
                $settings[$name] = [];
            }

            $this->flexFormToArray(implode('.', $fieldNameParts), $value, $settings[$name]);
        } else {
            $settings[$fieldNameParts[0]] = $value;
        }
    }
}
