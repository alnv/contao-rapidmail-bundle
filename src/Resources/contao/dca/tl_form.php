<?php

use Contao\DataContainer;
use Alnv\ContaoRapidMailBundle\Library\Rapidmail;

$GLOBALS['TL_DCA']['tl_form']['palettes']['__selector__'][] = 'useRapidmail';
$GLOBALS['TL_DCA']['tl_form']['palettes']['default'] .= ';{rm_settings:hide},useRapidmail';
$GLOBALS['TL_DCA']['tl_form']['subpalettes']['useRapidmail'] = 'rmUsername,rmPassword,rmSendActivationMail,rmRecipientlists';

$GLOBALS['TL_DCA']['tl_form']['fields']['useRapidmail'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_form']['useRapidmail'],
    'inputType' => 'checkbox',
    'eval' => [
        'tl_class' => 'clr',
        'submitOnChange' => true
    ],
    'sql' => "char(1) NOT NULL default ''"
];
$GLOBALS['TL_DCA']['tl_form']['fields']['rmUsername'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_form']['rmUsername'],
    'inputType' => 'text',
    'eval' => [
        'tl_class' => 'w50',
        'maxlength' => 255,
        'mandatory' => true
    ],
    'sql' => "varchar(255) NOT NULL default ''"
];
$GLOBALS['TL_DCA']['tl_form']['fields']['rmPassword'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_form']['rmPassword'],
    'inputType' => 'text',
    'eval' => [
        'tl_class' => 'w50',
        'maxlength' => 255,
        'mandatory' => true
    ],
    'sql' => "varchar(255) NOT NULL default ''"
];
$GLOBALS['TL_DCA']['tl_form']['fields']['rmSendActivationMail'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_form']['rmSendActivationMail'],
    'inputType' => 'checkbox',
    'eval' => [
        'tl_class' => 'clr',
        'submitOnChange' => true
    ],
    'sql' => "char(1) NOT NULL default ''"
];
$GLOBALS['TL_DCA']['tl_form']['fields']['rmRecipientlists'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_form']['rmRecipientlists'],
    'inputType' => 'checkbox',
    'eval' => [
        'multiple' => true,
        'tl_class' => 'clr'
    ],
    'options_callback' => function (DataContainer $objDataContainer) {
        return (new Rapidmail())->getRecipientlist($objDataContainer->activeRecord->id);
    },
    'sql' => "blob NULL"
];