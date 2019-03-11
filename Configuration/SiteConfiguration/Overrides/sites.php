<?php

$GLOBALS['SiteConfiguration']['site']['columns']['productSingleViewFallbackPid'] = [
    'label' => 'Product SingleView Fallback PID (Redirects)',
    'config' => [
        'type' => 'input',
        'eval' => 'int',
    ],
];

$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] = str_replace(
    'base,',
    'base, productSingleViewFallbackPid, ',
    $GLOBALS['SiteConfiguration']['site']['types']['0']['showitem']
);
