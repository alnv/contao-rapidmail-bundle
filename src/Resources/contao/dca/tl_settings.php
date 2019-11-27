<?php

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{rapidmail_settings:hide},rapidmailUsername,rapidmailPassword';

$GLOBALS['TL_DCA']['tl_settings']['fields']['rapidmailUsername'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['rapidmailUsername'],
    'inputType' => 'text',
    'eval' => [
        'tl_class' => 'w50'
    ]
];
$GLOBALS['TL_DCA']['tl_settings']['fields']['rapidmailPassword'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['rapidmailPassword'],
    'inputType' => 'text',
    'eval' => [
        'tl_class' => 'w50'
    ]
];