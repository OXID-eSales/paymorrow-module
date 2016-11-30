<?php

/**
 * @package Paymorrow Gateway
 * @copyright (C) 2015 - Paymorrow GmbH
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/


abstract class AbstractPaymorrowClient
{

    private $endPoint;

    const SIGNATURE_HEADER = "Signature";

    /**
     * Sends request to paymorrow webservice
     *
     * @param $data array request data as associative array
     * @return array response data as associative array
     */
    public function sendRequest($data)
    {
        if (!isset($this->endPoint)) {
            return array('client_error' => 'END_POINT_NOT_SET');
        }

        $requestString = $this->buildRequest($data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->endPoint);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FAILONERROR, true); // TODO SET AS YOU WISH
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // TODO SET AS YOU WISH
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // TODO SET AS YOU WISH
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestString);
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
            'Content-Length: ' . strlen($requestString),
            'Connection: Close',
        );

        $headers[] = self::SIGNATURE_HEADER . ": " . $this->signRequest($requestString);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        $curlErrNo = curl_errno($curl);
        $curlError = curl_error($curl);

        if (!empty($curlError)) {
            return array('client_error' => 'FAILED_TO_CONNECT', 'details' => $curlError);
        }

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        $signature = $this->parseSignatureHeaderValue($header);
        $signatureCheck = $this->verifyResponse($body, $signature);
        if ($signatureCheck === false) {
            return array('client_error' => 'INVALID_RESPONSE_SIGNATURE');
        } else {
            return $this->parseResponseString($body);
        }
    }

    /**
     * parses signature from http header
     *
     * @param $httpHeader
     * @return string
     *
     */
    private function parseSignatureHeaderValue($httpHeader)
    {
        $headerPairs = explode("\r\n", $httpHeader);
        $signaturePair = null;
        foreach ($headerPairs as $header) {
            if (substr($header, 0, strlen(self::SIGNATURE_HEADER)) == self::SIGNATURE_HEADER) {
                $signaturePair = $header;
                break;
            }
        }

        if (is_null($signaturePair)) {
            return "";
        }

        list($key, $value) = explode(": ", $signaturePair, 2);

        return $value;
    }

    /**
     * builds request string from associative array
     *
     * @param $requestData array associative array with request data
     * @return string
     */
    private function buildRequest($requestData)
    {
        $requestString = "";
        foreach ($requestData as $key => $value) {
            $requestString .= $key . "=" . urlencode($value) . "&";
        }
        return $requestString;
    }

    /**
     * parses RAW response string to associative array
     *
     * @param $responseString string RAW response string
     * @return array associative array with response data
     */
    private function parseResponseString($responseString)
    {
        if (empty($responseString)) {
            return array('client_error' => 'RESPONSE_IS_EMPTY');
        }
        $responsePairs = explode("&", $responseString);
        $responseData = array();
        foreach ($responsePairs as $pair) {
            $keyvalue = explode("=", $pair);
            if (count($keyvalue) == 2) {
                $responseData[$keyvalue[0]] = urldecode($keyvalue[1]);
            }
        }

        if (count($responseData) == 0) {
            return array('client_error' => 'RESPONSE_HAS_INVALID_DATA');
        }
        return $responseData;
    }

    /**
     * Signs request by merchant private key
     *
     * @param $requestString string urlencoded request string for webservice
     * @return string
     */
    public abstract function signRequest($requestString);


    /**
     * Verifies paymorrow response according to given paymorrow public key
     *
     * @param $responseString string response raw string
     * @param $signature string signature
     * @return boolean true if signature is ok, false otherwise
     */
    public abstract function verifyResponse($responseString, $signature);

    /**
     * @param mixed $endPoint
     */
    public function setEndPoint($endPoint)
    {
        $this->endPoint = $endPoint;
    }

    /**
     * @return mixed
     */
    public function getEndPoint()
    {
        return $this->endPoint;
    }
}