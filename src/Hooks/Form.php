<?php

namespace Alnv\ContaoRapidMailBundle\Hooks;

use Rapidmail\ApiClient\Client;


class Form {


    public function processFormData( $arrPost, $arrForm ) {

        $strRecipientlistId = '';
        $blnActiveRapidMail = false;
        $objFormFields = \Database::getInstance()->prepare('SELECT * FROM tl_form_field WHERE pid=?')->execute( $arrForm['id'] );

        if ( !$objFormFields->numRows ) {

            return null;
        }

        while ( $objFormFields->next() ) {

            if ( $objFormFields->sendToRapidMail && $arrPost[ $objFormFields->name ] ) {

                $blnActiveRapidMail = true;
                $strRecipientlistId = $objFormFields->rapidMailRecipientlistId;
                break;
            }
        }

        if ( !$blnActiveRapidMail || !$strRecipientlistId ) {

            return null;
        }

        if ( !\Config::get('rapidmailUsername') || !\Config::get('rapidmailPassword') || !$arrPost['email'] ) {

            return null;
        }

        $arrRapidmailConfig = [];
        $arrData = ['firstname','lastname','email','gender','email'];
        foreach ( $arrData as $strField ) {
            if ( isset( $arrPost[$strField] ) ) {
                $arrRapidmailConfig[$strField] = $arrPost[$strField];
            }
        }
        $arrRapidmailConfig['recipientlist_id'] = $strRecipientlistId;
        $objClient = new Client( \Config::get('rapidmailUsername'), \Config::get('rapidmailPassword') );
        $objRecipientsService = $objClient->recipients();
        $objRecipientsService->create(
            $arrRapidmailConfig,
            [
                'send_activationmail' => 'yes'
            ]
        );
    }
}