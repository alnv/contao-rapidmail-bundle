<?php

$GLOBALS['TL_DCA']['tl_form_field']['palettes']['__selector__'][] = 'sendToRapidMail';
$GLOBALS['TL_DCA']['tl_form_field']['subpalettes']['sendToRapidMail'] = 'rapidMailRecipientlistId';

$GLOBALS['TL_DCA']['tl_form_field']['palettes']['checkbox'] = str_replace( 'mandatory', 'mandatory,sendToRapidMail', $GLOBALS['TL_DCA']['tl_form_field']['palettes']['checkbox'] );
$GLOBALS['TL_DCA']['tl_form_field']['fields']['sendToRapidMail'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_form_field']['sendToRapidMail'],
    'inputType' => 'checkbox',
    'eval' => [
        'submitOnChange' => true,
        'tl_class' => 'clr'
    ],
    'exclude' => true,
    'sql' => "char(1) NOT NULL default ''"
];
$GLOBALS['TL_DCA']['tl_form_field']['fields']['rapidMailRecipientlistId'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_form_field']['rapidMailRecipientlistId'],
    'inputType' => 'select',
    'eval' => [
        'chosen' => true,
        'tl_class'=>'w50',
        'maxlength' => 255,
        'multiple' => false,
        'includeBlankOption' => true
    ],
    'options_callback' => function (\DataContainer $objDataContainer) {
        return (new \Alnv\ContaoRapidMailBundle\Library\Rapidmail())->getRecipientlist($objDataContainer->activeRecord->pid);
    },
    'exclude' => true,
    'sql' => "varchar(255) NOT NULL default ''"
];