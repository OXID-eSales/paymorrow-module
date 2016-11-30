<?php

/**
 * @package Paymorrow Gateway
 * @copyright (C) 2015 - Paymorrow GmbH
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

interface PaymorrowWsResponseHandler
{
    public function handlePrepareOrderResponseOK($responseData);

    public function handlePrepareOrderResponseError($responseData);

    public function handleConfirmOrderResponseOK($responseData);

    public function handleConfirmOrderResponseError($responseData);

    public function handleSubmitCertificateResponse($responseData);
}