<?php

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeSet;
use Pixelant\PxaProductManager\Domain\Model\DTO\Demand;
use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use Pixelant\PxaProductManager\Domain\Model\Order;
use Pixelant\PxaProductManager\Domain\Model\OrderConfiguration;
use Pixelant\PxaProductManager\Domain\Model\OrderFormField;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Service\Mail\OrderMailService;
use Pixelant\PxaProductManager\Utility\ConfigurationUtility;
use Pixelant\PxaProductManager\Utility\MainUtility;
use Pixelant\PxaProductManager\Utility\ProductUtility;
use Pixelant\PxaProductManager\Validation\ValidatorResolver;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\VisibilityAspect;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package pxa_product_manager
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class ProductController extends AbstractController
{
    /**
     * Add JS labels for each action
     */
    public function initializeAction()
    {
        $this->getFrontendLabels();

        // Set the pagePid using the flexform -> typoscript -> 0 priority
        $this->settings['pagePid'] = $this->settings['pagePid'] ?: ConfigurationUtility::getSettingsByPath('pagePid');
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $category = $this->determinateCategory(
            MainUtility::getActiveCategoryFromRequest()
        );

        if ($category) {
            /** @var QueryResultInterface $subCategories */
            $subCategories = $this->categoryRepository->findByParent(
                $category,
                $this->getOrderingsForCategories()
            );

            if ($subCategories->count() === 0 || $this->settings['showCategoriesWithProducts']) {
                $this->settings['demandCategories'] = [$category->getUid()];

                $demand = $this->createDemandFromSettings($this->settings);
                $products = $this->productRepository->findDemanded($demand);
            }

            $this->view->assignMultiple([
                'subCategories' => $subCategories,
                'category' => $category
            ]);
        }

        // add navigation if enabled in list view
        if ($this->settings['showNavigationListView']) {
            $this->view->assign('treeData', $this->getNavigationTree());
        }

        $this->view->assign('products', $products ?? []);
    }

    /**
     * lazy view action
     *
     * @return void
     */
    public function lazyListAction()
    {
        $this->settings['demandCategories'] = $this->getDemandCategories(
            GeneralUtility::intExplode(',', $this->settings['allowedCategories'], true),
            GeneralUtility::intExplode(',', $this->settings['excludeCategories'], true)
        );

        $demand = $this->createDemandFromSettings($this->settings);

        if (!empty($this->settings['filters'])) {
            $filtersUids = GeneralUtility::intExplode(',', $this->settings['filters'], true);
            $filters = $this->sortQueryResultsByUidList(
                $this->filterRepository->findByUidList(
                    $filtersUids
                ),
                $filtersUids
            );

            if ((int)$this->settings['hideFilterOptionsNoResult'] === 1) {
                // @codingStandardsIgnoreStart
                list($availableOptions, $availableCategories, $productsNoLimitCount) = $this->getAvailableFilterOptionsAndCountProductFromDemand(
                    $demand
                );
                // @codingStandardsIgnoreEnd
            }
        }

        $countResults = $productsNoLimitCount ?? $this->countDemanded($demand);

        $limit = (int)$this->settings['limit'];

        if ($uid = (int)$this->configurationManager->getContentObject()->data['uid']) {
            $storagePid = ProductUtility::getStoragePidForPlugin($uid);
        }

        $this->view->assignMultiple([
            'demandCategories' => implode(',', $this->settings['demandCategories']),
            'ajaxUrl' => $this->getLazyLoadingUrl(),
            'storagePid' => $storagePid ?? '',
            'lazyLoadingStop' => ($limit === 0 || $limit >= $countResults) ? 1 : 0,
            'filters' => $filters ?? [],
            'availableOptionsList' => implode(',', $availableOptions ?? []),
            'availableCategoriesList' => implode(',', $availableCategories ?? []),
        ]);
    }

    /**
     * action show
     *
     * @param \Pixelant\PxaProductManager\Domain\Model\Product $product
     * @return void
     */
    public function showAction(Product $product = null)
    {
        $isPreviewMode = false;
        if ($product === null) {
            // If preview product provided
            $product = $this->getPreviewProduct();
            $isPreviewMode = true;
        }

        // No product found handling
        if ($product !== null) {
            // Add constant with current product UID to header
            GeneralUtility::makeInstance(PageRenderer::class)->addJsInlineCode(
                'pxaproductmanager_current_product_uid',
                'const pxaproductmanager_current_product_uid=' . $product->getUid()
            );

            // check if categories have a custom single view template set
            if ($product->getCategories()->count() > 0) {
                /**
                 * @TODO how to define which template to use if multiple categories
                 */
                foreach ($product->getCategories() as $category) {
                    if (!empty($category->getSingleViewTemplate())) {
                        /** @noinspection PhpUndefinedMethodInspection */
                        $this->view->setTemplate($category->getSingleViewTemplate());
                    }
                }
            }

            // add navigation if enabled in list view
            if ($this->settings['showNavigationListView']
                && !$this->settings['hideNavigationListViewOnDetailMode']
            ) {
                $this->view->assign('treeData', $this->getNavigationTree());
            }

            // if product have more than one category - build canonical url to main (first) category
            if ((int)$this->settings['disableProductCanonicalUrl'] === 0
                && $product->getCategories()->count() > 1
            ) {
                $this->buildProductCanonicalUrl($product);
            }

            $this->view->assignMultiple([
                'product' => $product,
                'additionalButtons' => $this->getProductAdditionalButtons($product, []),
                'category' => MainUtility::getActiveCategoryFromRequest()
            ]);
        } else {
            $this->handleNoProductFoundError();
        }
    }

    /**
     * Wish list cart
     */
    public function wishListCartAction()
    {
        // Nothing to do. Products are counted by JS to make action cacheable
    }

    /**
     * Wish list of products
     *
     * @param bool $sendOrder
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function wishListAction(bool $sendOrder = false)
    {
        // Select the checkout system to use
        $checkoutToUse = $this->settings['wishList']['checkoutSystem']
            ?: ConfigurationUtility::getExtManagerConfigurationByPath('checkoutSystem') ?: 'default';

        $checkOutSystems = ConfigurationUtility::getCheckoutSystems();
        $checkout = $checkOutSystems[$checkoutToUse] ?: $checkOutSystems['default'];

        // Add after checkout system selected slot
        $this->signalSlotDispatcher->dispatch(__CLASS__, 'AfterCheckoutSystemSelected', [&$checkout, $this]);

        // If order form enabled
        if ($this->isOrderFormAllowed()) {
            /** @var OrderConfiguration $orderConfiguration */
            $orderConfiguration = $this->orderConfigurationRepository->findByUid(
                (int)$this->settings['orderFormConfiguration']
            );

            $this->view->assign('orderConfiguration', $orderConfiguration);

            if ($sendOrder && $orderConfiguration !== null) {
                try {
                    $orderProducts = $this->request->getArgument('orderProducts');
                    $values = $this->request->getArgument('orderFields');

                    if ($this->validateOrderFields($orderConfiguration, $values)) {
                        $order = $this->createAndSaveOrder(
                            $orderConfiguration,
                            $this->getOrderProductsQuantityForSerialization($orderProducts)
                        );
                        $this->sendOrderEmail($order, $orderConfiguration);
                        $this->redirect('finishOrder');
                    }
                } catch (NoSuchArgumentException $exception) {
                    // orderProducts and orderFields are required to send email
                }
            }
        }

        if ($this->request->hasArgument('orderProducts')) {
            $orderState = $this->request->getArgument('orderProducts');
        } else {
            $orderState = ProductUtility::getOrderState();
        }

        $this->view->assignMultiple([
            'checkout' => $checkout,
            'products' => $this->getProductsFromCookieList(ProductUtility::WISH_LIST_COOKIE_NAME),
            'orderProducts' => $orderState ?? [],
            'sendOrder' => $sendOrder
        ]);
    }

    /**
     * Finish order text
     * Show some successful texts
     */
    public function finishOrderAction()
    {
        ProductUtility::cleanOngoingOrderInfo();
    }

    /**
     * Compare list cart
     */
    public function compareListCartAction()
    {
        // Nothing to do. Products are counted by JS to make action cacheable
    }

    /**
     * Compare list of products
     */
    public function comparePreViewAction()
    {
        $compareList = MainUtility::getTSFE()->fe_user->getKey('ses', ProductUtility::COMPARE_LIST_SESSION_NAME)
            ?? [];

        $this->view->assign(
            'products',
            $this->getProductByUidsList($compareList)
        );
    }

    /**
     * Create compare view for products
     */
    public function compareViewAction()
    {
        $compareList = MainUtility::getTSFE()->fe_user->getKey('ses', ProductUtility::COMPARE_LIST_SESSION_NAME)
            ?? [];

        $products = $this->getProductByUidsList($compareList);
        $productAttributeSets = [];

        /** @var Product $product */
        foreach ($products as $product) {
            /** @var AttributeSet $attributesGroupedBySet */
            foreach ($product->getAttributesGroupedBySets() as $attributesGroupedBySet) {
                if (!array_key_exists($attributesGroupedBySet->getUid(), $productAttributeSets)) {
                    $productAttributeSets[$attributesGroupedBySet->getUid()] = [
                        'attributeSet' => $attributesGroupedBySet
                    ];
                }
            }
        }

        foreach ($productAttributeSets as &$attributeSet) {
            $attributeSet['attributesListDiff'] = $this->generateAttributesDiffDataForProducts(
                $products,
                $attributeSet['attributeSet']
            );
        }

        $this->view
            ->assign(
                'products',
                $this->getProductByUidsList($compareList)
            )
            ->assign(
                'diffData',
                $productAttributeSets
            );
    }

    /**
     * promotion list action
     *
     * @return void
     */
    public function promotionListAction()
    {
        $this->settings['demandCategories'] = $this->getDemandCategories(
            GeneralUtility::intExplode(',', $this->settings['allowedCategories'], true)
        );

        $demand = $this->createDemandFromSettings($this->settings);

        $products = $this->productRepository->findDemanded($demand);

        $this->view->assign('products', $products);
    }

    /**
     * No found page
     */
    public function notFoundAction()
    {
    }

    /**
     * action grouped list
     *
     * @return void
     */
    public function groupedListAction()
    {
        $groupedList = [];
        $excludeCategories = GeneralUtility::intExplode(',', $this->settings['excludeCategories'], true);

        $category = $this->determinateCategory(
            MainUtility::getActiveCategoryFromRequest()
        );

        if ($category !== null) {
            // if showCategoriesWithProducts, display products in just this category, not recursive
            if ($this->settings['showCategoriesWithProducts']) {
                $this->settings['demandCategories'] = [$category->getUid()];

                $demand = $this->createDemandFromSettings($this->settings);
                $products = $this->productRepository->findDemanded($demand);
            }

            /** @var QueryResultInterface $subCategories */
            $subCategories = $this->categoryRepository->findByParent(
                $category,
                $this->getOrderingsForCategories()
            );

            if ($subCategories->count() > 0) {
                $groupedListIndex = 0;
                $duplicateCategories = [];

                foreach ($subCategories as $index => $subCategory) {
                    $subCategoryUid = $subCategory->getUid();

                    if (in_array($subCategoryUid, $excludeCategories, true)) {
                        // excluded category, unset
                        array_push($duplicateCategories, $index);
                    } else {
                        $subCategoryCategories = $this->categoryRepository->findByParent(
                            $subCategory
                        );

                        // if category doesn't have any sub categories, fetch products
                        if ($subCategoryCategories->count() === 0) {
                            $this->settings['demandCategories'] = [$subCategoryUid];
                            $demand = $this->createDemandFromSettings($this->settings);
                            $subCategoryProducts = $this->productRepository->findDemanded($demand);

                            if ($subCategoryProducts->count() > 0) {
                                // if category has products it will be displayed differently,
                                // remove from "browse" categories
                                array_push($duplicateCategories, $index);
                                // add to grouped list instead
                                $groupedList[$groupedListIndex]['category'] = $subCategory;
                                $groupedList[$groupedListIndex]['products'] = $subCategoryProducts;
                                $groupedList[$groupedListIndex]['categoryAttributes'] = 0;
                                if ($subCategoryProducts->count() > 0) {
                                    $groupedList[$groupedListIndex]['categoryAttributes'] =
                                        $subCategoryProducts->current()->getAttributes()->count();
                                }
                                $groupedListIndex++;
                            }
                        }
                    }
                }

                // remove dublicate categories (added to groupedList)
                if (!empty($duplicateCategories)) {
                    foreach ($duplicateCategories as $index) {
                        unset($subCategories[$index]);
                    }
                }
            }

            $this->view->assignMultiple([
                'category' => $category,
                'products' => $products ?? [],
                'subCategories' => $subCategories,
            ]);
        }

        $this->view->assign('groupedList', $groupedList ?? []);
    }

    /**
     * List of custom products
     *
     * @return void
     */
    public function customProductsListAction()
    {
        $mode = $this->settings['customProductsList']['mode'];
        $products = [];

        // Products mode
        if ($mode === 'products') {
            $productsList = GeneralUtility::trimExplode(
                ',',
                $this->settings['customProductsList']['productsToShow'],
                true
            );
            $products = $this->getProductByUidsList($productsList);
        }

        // Category mode
        if ($mode === 'category') {
            $categories = GeneralUtility::trimExplode(
                ',',
                $this->settings['customProductsList']['productsCategories'],
                true
            );
            $this->view->assign('categories', $this->categoryRepository->findByUidList($categories));

            // Get products
            if (!empty($this->settings['customProductsList']['productsToShowWithinCategories'])) {
                $productsList = GeneralUtility::trimExplode(
                    ',',
                    $this->settings['customProductsList']['productsToShowWithinCategories'],
                    true
                );
                $products = $this->getProductByUidsList($productsList);
            } else {
                $products = $this->productRepository->findProductsByCategories(
                    $categories,
                    false,
                    ['tstamp' => QueryInterface::ORDER_DESCENDING],
                    'or',
                    (int)$this->settings['limit']
                );
            }
        }

        $this->view->assign('products', $products);
    }

    /**
     * Check if order form is allowed
     *
     * @return bool
     */
    protected function isOrderFormAllowed(): bool
    {
        $requireLogin = (int)$this->settings['orderFormRequireLogin'] === 1;

        return !$requireLogin || $this->isUserLoggedIn();
    }

    /**
     * Check if user is logged in. Wrapper for tests
     * @return bool
     */
    protected function isUserLoggedIn()
    {
        return MainUtility::isFrontendLogin();
    }

    /**
     * Send emails with order
     *
     * @param Order $order
     * @param OrderConfiguration $orderConfiguration
     * @return void
     */
    protected function sendOrderEmail(Order $order, OrderConfiguration $orderConfiguration)
    {
        $adminTemplate = $this->settings['wishList']['orderForm']['adminEmailTemplatePath'] ?? '';
        $userTemplate = $this->settings['wishList']['orderForm']['userEmailTemplatePath'] ?? '';

        /** @var OrderMailService $orderMailService */
        $orderMailService = GeneralUtility::makeInstance(OrderMailService::class);
        $orderMailService
            ->setSenderName($this->settings['email']['senderName'])
            ->setSenderEmail($this->settings['email']['senderEmail']);

        // Send email to admins
        if (!empty($orderConfiguration->getAdminEmails())) {
            $orderMailService
                ->generateMailBody($adminTemplate, $order)
                ->setSubject($this->translate('fe.adminEmail.orderForm.subject'))
                ->setReceivers($orderConfiguration->getAdminEmailsArray())
                ->send();
        }


        // Send email to user if enabled
        if ($orderConfiguration->isEnabledEmailToUser()) {
            $email = $orderConfiguration->getUserEmailFromFormFields();
            if (!empty($email)) {
                $orderMailService
                    ->generateMailBody($userTemplate, $order)
                    ->setSubject($this->translate('fe.userEmail.orderForm.subject'))
                    ->setReceivers([$email])
                    ->send();
            }
        }
    }

    /**
     * Save order
     *
     * @param OrderConfiguration $orderConfiguration
     * @param array $orderProducts
     * @return Order
     */
    protected function createAndSaveOrder(OrderConfiguration $orderConfiguration, array $orderProducts): Order
    {
        $order = $this->objectManager->get(Order::class);

        $order->setOrderFields($this->getOrderFormFieldsForSerialization($orderConfiguration));

        $products = $this->productRepository->findProductsByUids(array_keys($orderProducts));
        /** @var Product $product */
        foreach ($products as $product) {
            $order->addProduct($product);
            $pid = $product->getPid();
        }

        $productsQuantityData = ProductUtility::orderProductsToProductQuantityData($orderProducts, $products);

        $order->setProductsQuantity($productsQuantityData);

        if ($orderConfiguration->getFrontendUser() !== null) {
            $order->setFeUser($orderConfiguration->getFrontendUser());
        }

        if (isset($pid)) {
            $order->setPid($pid);
        }

        $this->signalSlotDispatcher->dispatch(
            __CLASS__,
            'AfterOrderCreatedBeforeSaving',
            [$order, $productsQuantityData, $orderProducts, $orderConfiguration, $this]
        );

        $this->orderRepository->add($order);

        return $order;
    }

    /**
     * Prepare order fields for serialization
     *
     * @param OrderConfiguration $orderConfiguration
     * @return array
     */
    protected function getOrderFormFieldsForSerialization(OrderConfiguration $orderConfiguration): array
    {
        $orderFields = [];
        /** @var OrderFormField $formField */
        foreach ($orderConfiguration->getFormFields() as $formField) {
            $orderFields[$formField->getName()] = [
                'value' => $formField->getValueAsText(),
                'type' => $formField->getType(),
                'label' => $formField->getLabel()
            ];
        }

        return $orderFields;
    }

    /**
     * Prepare order products for serialization
     *
     * @param array $orderProducts
     * @return array
     */
    protected function getOrderProductsQuantityForSerialization(array $orderProducts): array
    {
        $processedOrderProducts = [];

        foreach ($orderProducts as $productUid => $productQuantity) {
            $productUid = (int)$productUid;
            $productQuantity = (int)$productQuantity;

            if ($productUid && $productQuantity) {
                $processedOrderProducts[$productUid] = $productQuantity;
            }
        }

        return $processedOrderProducts;
    }

    /**
     * Return false if fields fail validation. Also will add error messages to fields configuration
     *
     * @param OrderConfiguration $orderConfiguration
     * @param array $values
     * @return bool
     */
    protected function validateOrderFields(OrderConfiguration $orderConfiguration, array $values): bool
    {
        $isValid = true;
        $validationResolver = $this->getValidatorResolver();

        /** @var OrderFormField $field */
        foreach ($orderConfiguration->getFormFields() as $field) {
            if ($field->isStatic()) {
                continue;
            }

            $value = trim($values[$field->getUid()] ?? '');
            $field->setValue($value);

            foreach ($field->getValidationRulesArray() as $validationRule) {
                $validator = $validationResolver->createValidator($validationRule);
                if (!$validator->validate($value)) {
                    $isValid = false;
                    $field->addError($validator->getErrorMessage());
                }
            }
        }

        return $isValid;
    }

    /**
     * Generate difference data for all products and attribute sets
     *
     * @param array $products
     * @param AttributeSet $attributeSet
     * @return array
     */
    protected function generateAttributesDiffDataForProducts(array $products, AttributeSet $attributeSet)
    {
        $diffData = [];

        /** @var Attribute $attribute */
        foreach ($attributeSet->getAttributes() as $attribute) {
            if (!$attribute->isShowInCompare()
                || GeneralUtility::inList(
                    $this->settings['ignoreAttributeTypesInCompareView'],
                    $attribute->getType()
                )
            ) {
                continue;
            }

            $diffData[$attribute->getUid()] = $this->getDiffValuesForProductsSingleAttribute(
                $products,
                $attribute
            );
        }

        return $diffData;
    }

    /**
     * Get difference between products attribute
     *
     * @param array $products
     * @param Attribute $attribute
     * @return array
     */
    protected function getDiffValuesForProductsSingleAttribute(array $products, Attribute $attribute)
    {
        $diffData = [
            'label' => $attribute->getLabel() ?: $attribute->getName()
        ];
        $attributesList = [];
        $tempValues = [];

        /** @var Product $product */
        foreach ($products as $product) {
            $singleAttribute = '';

            /** @var Attribute $pAttribute */
            foreach ($product->getAttributes() as $pAttribute) {
                if ($pAttribute->getUid() === $attribute->getUid()) {
                    $singleAttribute = $pAttribute;
                }
            }

            $attributesList[] = $singleAttribute;

            if (is_object($singleAttribute)) {
                switch ($singleAttribute->getType()) {
                    case Attribute::ATTRIBUTE_TYPE_DROPDOWN:
                    case Attribute::ATTRIBUTE_TYPE_MULTISELECT:
                        $tempValues[] = implode(',', $singleAttribute->getValue());
                        break;
                    case Attribute::ATTRIBUTE_TYPE_DATETIME:
                        if (is_object($singleAttribute->getValue())) {
                            /** @var \DateTime $date */
                            $date = $singleAttribute->getValue();
                            $tempValues[] = $date->format('%d-%m-%Y');
                        } else {
                            $tempValues[] = '';
                        }
                        break;
                    default:
                        $tempValues[] = $singleAttribute->getValue();
                }
            } else {
                $tempValues[] = '';
            }
        }

        $diffData['attributesList'] = $attributesList;
        $diffData['isDifferent'] = (count(array_unique($tempValues)) !== 1);

        return $diffData;
    }

    /**
     * Create demand object
     *
     * @param array $settings
     * @param string $class
     * @return Demand
     */
    protected function createDemandFromSettings(
        array $settings,
        string $class = null
    ): DemandInterface {
        $class = $class ??
            (!empty($settings['demandClass'])
                ? $settings['demandClass']
                : 'Pixelant\\PxaProductManager\\Domain\\Model\\DTO\\Demand');

        /** @var Demand $demand */
        $demand = GeneralUtility::makeInstance($class);
        if (!$demand instanceof Demand) {
            throw new \UnexpectedValueException(
                sprintf(
                // @codingStandardsIgnoreStart
                    'Demand object must instance of "Pixelant\\PxaProductManager\\Domain\\Model\\DTO\\Demand", but instance of "%s" given.',
                    // @codingStandardsIgnoreEnd
                    $class
                ),
                1539161115399
            );
        }

        if (!empty($settings['demandCategories'])) {
            $demand->setCategories($settings['demandCategories']);
        }
        if (!empty($settings['allowedCategoriesMode'])) {
            $demand->setCategoryConjunction($settings['allowedCategoriesMode']);
        }
        if (isset($settings['limit'])) {
            $demand->setLimit((int)$settings['limit']);
        }
        if (isset($settings['offSet'])) {
            $demand->setOffSet((int)$settings['offSet']);
        }
        if (isset($settings['filters']) && is_array($settings['filters'])) {
            $demand->setFilters($settings['filters']);
        }
        if (!empty($settings['includeDiscontinued'])) {
            $demand->setIncludeDiscontinued((bool)$settings['includeDiscontinued']);
        }

        // set orderings
        if (!empty($settings['orderProductBy'])) {
            $demand->setOrderBy($settings['orderProductBy']);
        }
        if (!empty($settings['orderProductDirection'])) {
            $demand->setOrderDirection($settings['orderProductDirection']);
        }
        if (!empty($settings['orderByAllowed'])) {
            $demand->setOrderByAllowed($settings['orderByAllowed']);
        }

        $this->signalSlotDispatcher->dispatch(__CLASS__, 'AfterDemandCreationBeforeReturn', [$demand, $settings]);

        return $demand;
    }

    /**
     * Count results for demand
     *
     * @param Demand $demand
     * @return int
     */
    protected function countDemanded(Demand $demand)
    {
        // Count all products
        // reset limit
        $demand = clone ($demand);
        $demand->setOffSet(0);
        $demand->setLimit(0);

        return $this->productRepository->countByDemand($demand);
    }

    /**
     * Url for Ajax lazy loading
     *
     * @return string
     */
    protected function getLazyLoadingUrl()
    {
        $uri = $this->controllerContext->getUriBuilder();
        $uri->reset()
            ->setTargetPageUid(MainUtility::getTSFE()->id)
            ->setTargetPageType($this->settings['lazyLoading']['pageType'])
            ->setCreateAbsoluteUri(true);

        return $uri->buildFrontendUri();
    }

    /**
     * Add canonical url for product single view
     * Make sure any other plugin adding it
     *
     * @param Product $product
     */
    protected function buildProductCanonicalUrl(Product $product)
    {
        $arguments = MainUtility::buildLinksArguments($product, $product->getFirstCategory());

        $uriBuilder = $this->controllerContext->getUriBuilder();
        $uriBuilder
            ->reset()
            ->setTargetPageUid($this->settings['pageUid'] ?: MainUtility::getTSFE()->id)
            ->setArguments($arguments)
            ->setCreateAbsoluteUri(true);

        $url = $uriBuilder->buildFrontendUri();

        // add only absolute links
        if (!empty($url) && StringUtility::beginsWith($url, 'http')) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->response->addAdditionalHeaderData(
                '<link rel="canonical" href="' . $url . '">'
            );
        }
    }

    /**
     * Get list of products by cookie list
     *
     * @param string $cookieName
     * @param int $excludeProduct
     * @param int $limit
     * @return array
     */
    protected function getProductsFromCookieList($cookieName, int $excludeProduct = 0, int $limit = 0)
    {
        $productUids = array_key_exists($cookieName, $_COOKIE)
            ? GeneralUtility::intExplode(',', $_COOKIE[$cookieName], true)
            : [];

        $products = $this->getProductByUidsList($productUids, $excludeProduct);

        if ($limit && count($products) > $limit) {
            $products = array_slice($products, 0, $limit);
        }

        return $products;
    }

    /**
     * Get product by uids list in same order
     *
     * @param array $productsUids
     * @param int $excludeProduct
     * @return array
     */
    protected function getProductByUidsList(array $productsUids, int $excludeProduct = 0)
    {
        // remove product from list
        if ($excludeProduct && in_array($excludeProduct, $productsUids)) {
            $keys = array_keys($productsUids, $excludeProduct);
            foreach ($keys as $key) {
                unset($productsUids[$key]);
            }
        }

        $products = $this->productRepository->findProductsByUids($productsUids);
        if (is_object($products)) {
            $products = $this->sortQueryResultsByUidList($products, $productsUids);
        }

        return $products;
    }

    /**
     * Error handling if no product entry is found
     *
     * @return void
     */
    protected function handleNoProductFoundError()
    {
        // If configured, show custom message instead of standard 404
        if ((int)$this->settings['enableMessageInsteadOfPage404'] !== 0) {
            $this->forward('notFound');
        } else {
            MainUtility::getTSFE()->pageNotFoundAndExit('No product entry found.');
        }
    }

    /**
     * @return ValidatorResolver
     */
    protected function getValidatorResolver(): ValidatorResolver
    {
        return GeneralUtility::makeInstance(ValidatorResolver::class);
    }

    /**
     * @param Product $product
     * @param array $buttons
     * @return array
     */
    protected function getProductAdditionalButtons(Product $product, array $buttons = []): array
    {
        /**
         * Generate additional buttons
         * Should an array that follows this structure
         * [
         *     [
         *         'name' => 'Do something',
         *         'link' => 'https://www.example.com',
         *         'classes' => [],
         *         'order' => '100'
         *     ],
         *     [
         *         'name' => 'Buy',
         *         'link' => 'https://www.test.net',
         *         'classes' => ['beauty', 'clarence'],
         *         'order' => '20'
         *     ],
         * ]
         *
         * name - button text
         * link - button link
         * classes - array of additional button classes
         * order - used to specify buttons order
         */

        // Add a signal slot so other extension could add additional buttons
        $this->signalSlotDispatcher->dispatch(__CLASS__, 'BeforeProcessingAdditionalButtons', [$product, &$buttons]);

        // Process
        foreach ($buttons as &$button) {
            $button['classes'] = empty($button['classes']) ? '' : implode(' ', $button['classes']);
        }
        unset($button);

        // Sort
        usort($buttons, function ($a, $b) {
            return $a['order'] - $b['order'];
        });

        return $buttons;
    }

    /**
     * Get preview product if argument exist
     *
     * @return null|Product
     */
    protected function getPreviewProduct()
    {
        if ($this->request->hasArgument('product_preview')) {
            $productPreview = (int)$this->request->getArgument('product_preview');
            if ($productPreview > 0) {
                if (isset($this->settings['showHiddenRecords'])
                    && (int)$this->settings['showHiddenRecords'] === 1
                ) {
                    $this->allowHiddenRecords();
                    $product = $this->productRepository->findByUid($productPreview, false);
                } else {
                    $product = $this->productRepository->findByUid($productPreview);
                }
            }
        }

        return $product ?? null;
    }

    /**
     * Allow hidden content
     */
    protected function allowHiddenRecords()
    {
        if (MainUtility::isBelowTypo3v9()) {
            MainUtility::getTSFE()->showHiddenRecords = true;
        } else {
            $context = GeneralUtility::makeInstance(Context::class);
            /** @var VisibilityAspect $visibilityAspect */
            $visibilityAspect = $context->getAspect('visibility');

            $newVisibilityAspect = GeneralUtility::makeInstance(
                VisibilityAspect::class,
                $visibilityAspect->includeHiddenPages(),
                true,
                $visibilityAspect->includeDeletedRecords()
            );

            $context->setAspect('visibility', $newVisibilityAspect);
        }
    }
}
