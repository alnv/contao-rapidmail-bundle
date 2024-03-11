<?php

namespace Alnv\ContaoRapidMailBundle\Library;

use Contao\Config;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\FormModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Validator;
use Contao\FormFieldModel;
use Psr\Log\LogLevel;
use Rapidmail\ApiClient\Client;
use Contao\Message;

class Rapidmail
{

    public function getAccessData($strFormId = null): array
    {

        $arrReturn = [
            'user' => Config::get('rmUsername') ?: Config::get('rapidmailUsername'),
            'password' => Config::get('rmPassword') ?: Config::get('rapidmailPassword')
        ];

        if (!$strFormId) {
            return $arrReturn;
        }

        $objForm = FormModel::findByPk($strFormId);

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

    protected function getRapidOptions($strFormId, $arrSubmit = []): array
    {

        $arrOptions = [
            'recipientlist_ids' => [],
            'send_activationmail' => 'no'
        ];

        $objForm = FormModel::findByPk($strFormId);
        if (!$objForm) {
            return $arrOptions;
        }

        $arrOptions['recipientlist_ids'] = StringUtil::deserialize($objForm->rmRecipientlists, true);
        $arrOptions['send_activationmail'] = $objForm->rmSendActivationMail ? 'yes' : 'no';

        $objFormFields = FormFieldModel::findByPid($strFormId);
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

    public function getRecipientlist($strFormId = null): array
    {

        $arrReturn = [];
        $arrAccessData = $this->getAccessData($strFormId);

        if (!$arrAccessData['user'] || !$arrAccessData['password']) {
            return $arrReturn;
        }

        try {
            $objClient = new Client($arrAccessData['user'], $arrAccessData['password']);
            $objListService = $objClient->recipientlists();
            foreach ($objListService->query() as $objList) {
                $arrData = $objList->toArray();
                $arrReturn[$arrData['id']] = $arrData['name'] . ' (ID: ' . $arrData['id'] . ')';
            }
        } catch (\Exception $objError) {
            Message::addError($objError->getMessage());
        }

        return $arrReturn;
    }

    public function getDefaultAttributesBySubmitData($arrSubmit): array
    {

        $arrReturn = [];

        if (!is_array($arrSubmit) || empty($arrSubmit)) {
            return $arrReturn;
        }

        $arrAttributes = ['firstname', 'lastname', 'gender', 'email', 'zip', 'title'];

        foreach ($arrSubmit as $strField => $varValue) {

            if (Validator::isEmail($varValue)) {
                $arrReturn['email'] = $varValue;
            }

            if (in_array($strField, $arrAttributes)) {
                $arrReturn[$strField] = $varValue;
            }
        }

        return $arrReturn;
    }

    public function createRecipient($strFormId, $arrSubmit): void
    {

        $arrAccessData = $this->getAccessData($strFormId);
        $arrOptions = $this->getRapidOptions($strFormId, $arrSubmit);
        $arrAttributes = $this->getDefaultAttributesBySubmitData($arrSubmit);

        foreach ($arrOptions['recipientlist_ids'] as $strRecipientListId) {
            $arrAttributes['recipientlist_id'] = $strRecipientListId;
            try {
                $objClient = new Client($arrAccessData['user'], $arrAccessData['password']);
                $objRecipientsService = $objClient->recipients();
                $objRecipientsService->create($arrAttributes,
                    [
                        'send_activationmail' => $arrOptions['send_activationmail']
                    ]
                );
            } catch (\Exception $objError) {
                System::getContainer()
                    ->get('monolog.logger.contao')
                    ->log(LogLevel::ERROR, $objError->getMessage(), ['contao' => new ContaoContext(__CLASS__ . '::' . __FUNCTION__)]);
            }
        }
    }
}