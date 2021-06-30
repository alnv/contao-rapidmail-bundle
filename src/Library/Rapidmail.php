<?php

namespace Alnv\ContaoRapidMailBundle\Library;

class Rapidmail {

    public function getAccessData($strFormId=null) {

        $arrReturn = [
            'user' => \Config::get('rmUsername') ?: \Config::get('rapidmailUsername'),
            'password' => \Config::get('rmPassword') ?: \Config::get('rapidmailPassword')
        ];

        if (!$strFormId) {
            return $arrReturn;
        }

        $objForm = \FormModel::findByPk($strFormId);

        if (!$objForm) {
            return $arrReturn;
        }

        if (!$objForm->useRapidmail) {
            return $arrReturn;
        }

        $arrReturn['user'] = $objForm->rmUsername ?: $arrReturn['user'];
        $arrReturn['password'] = $objForm->rmPassword ?: $arrReturn['password'];

        return $arrReturn;
    }

    protected function getRapidOptions($strFormId, $arrSubmit=[]) {

        $arrOptions = [
            'recipientlist_ids' => [],
            'send_activationmail' => 'no'
        ];

        $objForm = \FormModel::findByPk($strFormId);
        if (!$objForm) {
            return $arrOptions;
        }

        $arrOptions['recipientlist_ids'] = \StringUtil::deserialize($objForm->rmRecipientlists, true);
        $arrOptions['send_activationmail'] = $objForm->rmSendActivationMail ? 'yes' : 'no';

        $objFormFields = \FormFieldModel::findByPid($strFormId);
        if (!$objFormFields) {
            return $arrOptions;
        }
        while ($objFormFields->next()) {
            if ($objFormFields->sendToRapidMail) {
                if ($objFormFields->rapidMailRecipientlistId && $arrSubmit[$objFormFields->name] && !in_array($objFormFields->rapidMailRecipientlistId, $arrOptions['recipientlist_ids'])) {
                    $arrOptions['recipientlist_ids'][] = $objFormFields->rapidMailRecipientlistId;
                }
            }
        }

        return $arrOptions;
    }

    public function getRecipientlist($strFormId=null) {

        $arrReturn = [];
        $arrAccessData = $this->getAccessData($strFormId);
        $objClient = new \Rapidmail\ApiClient\Client($arrAccessData['user'], $arrAccessData['password']);
        $objListService = $objClient->recipientlists();

        foreach ($objListService->query() as $objList) {
            $arrData = $objList->toArray();
            $arrReturn[$arrData['id']] = $arrData['name'] . ' (ID: '.$arrData['id'].')';
        }

        return $arrReturn;
    }

    public function getDefaultAttributesBySubmitData($arrSubmit) {

        $arrReturn = [];

        if (!is_array($arrSubmit) || empty($arrSubmit)) {
            return $arrReturn;
        }

        $arrAttributes = ['firstname','lastname','gender','email', 'zip', 'title'];

        foreach ($arrSubmit as $strField => $varValue) {

            if (\Validator::isEmail($varValue)) {
                $arrReturn['email'] = $varValue;
            }

            if (in_array($strField, $arrAttributes)) {
                $arrReturn[$strField] = $varValue;
            }
        }

        return $arrReturn;
    }

    public function createRecipient($strFormId, $arrSubmit) {

        $arrAccessData = $this->getAccessData($strFormId);
        $arrOptions = $this->getRapidOptions($strFormId, $arrSubmit);
        $arrAttributes = $this->getDefaultAttributesBySubmitData($arrSubmit);

        foreach ($arrOptions['recipientlist_ids'] as $strRecipientListId) {
            $arrAttributes['recipientlist_id'] = $strRecipientListId;
            try {
                $objClient = new \Rapidmail\ApiClient\Client($arrAccessData['user'], $arrAccessData['password']);
                $objRecipientsService = $objClient->recipients();
                $objRecipientsService->create($arrAttributes,
                    [
                        'send_activationmail' => $arrOptions['send_activationmail']
                    ]
                );
            } catch (\Exception $objError) {
                \System::log($objError->getMessage(), __METHOD__, TL_ERROR);
            }
        }
    }
}