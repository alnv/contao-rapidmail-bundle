<?php

namespace Alnv\ContaoRapidMailBundle\Hooks;

class Form {

    public function processFormData($arrPost, $arrForm) {

        if (!$arrForm['useRapidmail']) {
            return null;
        }

        (new \Alnv\ContaoRapidMailBundle\Library\Rapidmail())->createRecipient($arrForm['id'], $arrPost);
    }
}