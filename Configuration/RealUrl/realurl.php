<?php
call_user_func(function () {
    if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['theme_t3kit'])) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['theme_t3kit'] = [];
    }

    $configuration = &$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['theme_t3kit'];

    $configuration['fixedPostVars'][] = ['title' => 'Product manager', 'key' => '--div--'];

    $lookUpCategories = [];
    // 5 the deepest level
    for ($i = 0; $i < 5; $i++) {
        $lookUpCategories[] = [
            'GETvar' => 'tx_pxaproductmanager_pi1[' .
                \Pixelant\PxaProductManager\Controller\NavigationController::CATEGORY_ARG_START_WITH . $i . ']',
            'lookUpTable' => [
                'table' => 'sys_category',
                'id_field' => 'uid',
                'alias_field' => 'IF(path_segment!="",path_segment,title)',
                'addWhereClause' => ' AND NOT deleted',
                'useUniqueCache' => 1,
                'useUniqueCache_conf' => [
                    'strtolower' => 1,
                    'spaceCharacter' => '-',
                ],
                'autoUpdate' => 1,
                'expireDays' => 30,
                'languageGetVar' => 'L',
                'languageExceptionUids' => '',
                'languageField' => 'sys_language_uid',
                'transOrigPointerField' => 'l10n_parent',
                'enable404forInvalidAlias' => '1',
            ],
        ];
    }

    // Deep link configuration
    $configuration['fixedPostVars'][] = [
        'title' => 'Product page',
        'key' => 'pxa_product_manager_view',
        'configuration' => $lookUpCategories
    ];

    // Standard single view
    $configuration['fixedPostVars'][] = [
        'title' => 'Product only for single view',
        'key' => 'pxa_product_manager_single_view',
        'configuration' => [
            [
                'GETvar' => 'tx_pxaproductmanager_pi1[product]',
                'lookUpTable' => [
                    'table' => 'tx_pxaproductmanager_domain_model_product',
                    'id_field' => 'uid',
                    'alias_field' => 'IF(path_segment!="",path_segment,name)',
                    'addWhereClause' => ' AND NOT deleted',
                    'useUniqueCache' => 1,
                    'useUniqueCache_conf' => [
                        'strtolower' => 1,
                        'spaceCharacter' => '-',
                    ],
                    'autoUpdate' => 1,
                    'expireDays' => 30,
                    'languageGetVar' => 'L',
                    'languageExceptionUids' => '',
                    'languageField' => 'sys_language_uid',
                    'transOrigPointerField' => 'l10n_parent',
                    'enable404forInvalidAlias' => '1',
                ],
            ]
        ]
    ];

    $aliasField = 'IF(path_segment!="",path_segment,name)';
    // @codingStandardsIgnoreStart
    $enableSkuInUrl = (int)\Pixelant\PxaProductManager\Utility\ConfigurationUtility::getExtManagerConfigurationByPath('includeSkuInRealurlConfiguration');

    // @codingStandardsIgnoreEnd
    if ($enableSkuInUrl === 1) {
        $aliasField = sprintf(
            'CONCAT (%s, "-", sku)',
            $aliasField
        );
    }

    // Product postVarSet
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT']['product'] = [
        [
            'GETvar' => 'tx_pxaproductmanager_pi1[product]',
            'lookUpTable' => [
                'table' => 'tx_pxaproductmanager_domain_model_product',
                'id_field' => 'uid',
                'alias_field' => $aliasField,
                'addWhereClause' => ' AND NOT deleted',
                'useUniqueCache' => 1,
                'useUniqueCache_conf' => [
                    'strtolower' => 1,
                    'spaceCharacter' => '-',
                ],
                'autoUpdate' => 1,
                'expireDays' => 30,
                'languageGetVar' => 'L',
                'languageExceptionUids' => '',
                'languageField' => 'sys_language_uid',
                'transOrigPointerField' => 'l10n_parent',
                'enable404forInvalidAlias' => '1',
            ],
        ]
    ];
});
