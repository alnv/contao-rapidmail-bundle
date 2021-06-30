<?php

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{rm_settings:hide},rmUsername,rmPassword';

$GLOBALS['TL_DCA']['tl_settings']['fields']['rmUsername'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['rmUsername'],
    'inputType' => 'text',
    'eval' => [
        'tl_class' => 'w50'
    ]
];
$GLOBALS['TL_DCA']['tl_settings']['fields']['rmPassword'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['rmPassword'],
    'inputType' => 'text',
    'eval' => [
        'tl_class' => 'w50'
    ]
];