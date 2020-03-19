<?php

$GLOBALS['SiteConfiguration']['site']['columns']['pxapm_singleViewPid'] = [
    'label' => 'Single view page (required for redirects)',
    'config' => [
        'type' => 'input',
        'eval' => 'int',
        'size' => 5,
    ],
];

$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .= ',--div--;Product manager,pxapm_singleViewPid,';
