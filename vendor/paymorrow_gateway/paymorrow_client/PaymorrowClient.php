<?php

/**
 * @package Paymorrow Gateway
 * @copyright (C) 2015 - Paymorrow GmbH
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

require_once('AbstractPaymorrowClient.php');

class PaymorrowClient extends AbstractPaymorrowClient
{
    private $privateKeyBytes;
    private $paymorrowPublicKey;

    /**
     * Signs request by merchant private key
     *
     * @param $requestString string urlencoded request string for webservice
     * @return string
     */
    public function signRequest($requestString)
    {
        if (!isset($this->privateKeyBytes)) {
            return array('client_error' => 'PRIVATE_KEY_NOT_SET');
        }


		$priv_key = $this->privateKeyBytes;
        $privkeyid = openssl_get_privatekey($priv_key);
        openssl_sign($requestString, $signature, $privkeyid);
        return bin2hex($signature);
    }

    /**
     * Verifies paymorrow response according to given paymorrow public key
     *
     * @param $responseString string response raw string
     * @param $signature string signature
     * @return boolean true if signature is ok, false otherwise
     */
    public function verifyResponse($responseString, $signature)
    {
        if (!isset($this->paymorrowPublicKey)) {
            return array('client_error' => 'PUBLIC_KEY_NOT_SET');
        }

		$fp = fopen($this->paymorrowPublicKey, "r");
        $pub_key = fread($fp, filesize($this->paymorrowPublicKey));
        fclose($fp);
        $pubkeyid = openssl_get_publickey($pub_key);
        $ok = openssl_verify($responseString, hex2bin($signature), $pubkeyid);

        if($ok == 1){
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param mixed $privateKey
     */
    public function setPrivateKey($privateKey)
    {
        if (!empty($privateKey)) {
            $fp = fopen($privateKey, "r");
            $this->privateKeyBytes = fread($fp, filesize($privateKey));
            fclose($fp);
        }
    }

    /**
     * @param mixed $paymorrowPublicKey
     */
    public function setPaymorrowPublicKey($paymorrowPublicKey)
    {
        $this->paymorrowPublicKey = $paymorrowPublicKey;
    }

    /**
     * @return mixed
     */
    public function getPaymorrowPublicKey()
    {
        return $this->paymorrowPublicKey;
    }

    /**
     * @param mixed $privateKeyBytes
     */
    public function setPrivateKeyBytes($privateKeyBytes)
    {
        $this->privateKeyBytes = $privateKeyBytes;
    }

}

if (!function_exists('hex2bin')) {
    function hex2bin($data) {
        static $old;
        if ($old === null) {
            $old = version_compare(PHP_VERSION, '5.2', '<');
        }
        $isobj = false;
        if (is_scalar($data) || (($isobj = is_object($data)) && method_exists($data, '__toString'))) {
            if ($isobj && $old) {
                ob_start();
                echo $data;
                $data = ob_get_clean();
            }
            else {
                $data = (string) $data;
            }
        }
        else {
            trigger_error(__FUNCTION__.'() expects parameter 1 to be string, ' . gettype($data) . ' given', E_USER_WARNING);
            return;//null in this case
        }
        $len = strlen($data);
        if ($len % 2) {
            trigger_error(__FUNCTION__.'(): Hexadecimal input string must have an even length', E_USER_WARNING);
            return false;
        }
        if (strspn($data, '0123456789abcdefABCDEF') != $len) {
            trigger_error(__FUNCTION__.'(): Input string must be hexadecimal string', E_USER_WARNING);
            return false;
        }
        return pack('H*', $data);
    }
}
