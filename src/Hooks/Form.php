<?php

namespace Alnv\ContaoRapidMailBundle\Hooks;

use Alnv\ContaoRapidMailBundle\Library\Rapidmail;

class Form
{

    public function processFormData($arrPost, $arrForm)
    {

        if (!$arrForm['useRapidmail']) {
            return null;
        }

        (new Rapidmail())->createRecipient($arrForm['id'], $arrPost);
    }
}