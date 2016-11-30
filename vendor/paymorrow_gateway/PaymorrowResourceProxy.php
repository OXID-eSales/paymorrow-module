<?php

/**
 * @package Paymorrow Gateway
 * @copyright (C) 2015 - Paymorrow GmbH
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

class PaymorrowResourceProxy {

    private $endPointUrl;
    private $merchantId;

    public function getResource($path, $session_id = null)
    {
        if ($session_id == null) {
            $url = sprintf('%s/%s%s', $this->endPointUrl, $this->merchantId, $path);
        } else {
            $url = sprintf('%s/%s%s?session_id=%s', $this->endPointUrl, $this->merchantId, $path, $session_id);
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,            $url);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($curl, CURLOPT_TIMEOUT,        120);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($curl, CURLOPT_FAILONERROR,    true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST,           false);

        $responseBody = curl_exec($curl);
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);

        curl_close($curl);

        return array('contentType' => $contentType, 'body' => $responseBody);
    }

    /**
     * @param mixed $endPointUrl
     */
    public function setEndPointUrl($endPointUrl)
    {
        $this->endPointUrl = $endPointUrl;
    }

    /**
     * @param mixed $merchantId
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
    }
}