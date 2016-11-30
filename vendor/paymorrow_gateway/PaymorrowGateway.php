<?php

/**
 * @package Paymorrow Gateway
 * @copyright (C) 2015 - Paymorrow GmbH
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

require_once 'CertificateGenerator.php';

/**
 * List of settings stored into session: $_SESSION['pm_order_id'], $_SESSION['pm_order_transaction_idINVOICE'],
 * $_SESSION['pm_order_transaction_idSDD'], $_SESSION['pm_responseINVOICE'], $_SESSION['pm_responseSDD'],
 * $_SESSION["pm_verify"]
 */

class PaymorrowGateway
{
    const PM_PREFIX = "pm_";
    private $pmClient;

    private $endPointUrl;

    private $responseHandler;

    private $eshopDataProvider;

    public function prepareOrder($data)
    {
        $this->validateInputParameters($data);

        $eshopData = null;
        if (!is_null($this->eshopDataProvider)) {
            $eshopData = $this->eshopDataProvider->collectEshopData();
        }

        $data = $this->mergeEshopDataWithRequestData($eshopData, $data);
		$orderTransactionName = 'pm_order_transaction_id' . $data['paymentMethod_name'];
		
        if (isset($_SESSION[$orderTransactionName])) {
            $data['pm_order_transaction_id'] = $_SESSION[$orderTransactionName];
            $data['prepareOrder_requestType'] = 'UPDATED';
        } else {
            unset($data['pm_order_transaction_id']);
            $data['prepareOrder_requestType'] = 'INITIAL';
        }

        $this->pmClient->setEndPoint($this->endPointUrl . 'prepareOrder');

        $responseData = $this->sendRequest($data);

        if (!is_null($this->responseHandler)) {
            if ($this->isResponseOK($responseData)) {
                $this->responseHandler->handlePrepareOrderResponseOK($responseData);
            } else {
                $this->responseHandler->handlePrepareOrderResponseError($responseData);
            }
        }

        $pmResponseName = "pm_response" . $data['paymentMethod_name'];
        $_SESSION[$pmResponseName] = $responseData;

        if (isset($responseData['pm_order_transaction_id'])) {
            $_SESSION[$orderTransactionName] = $responseData['pm_order_transaction_id'];
        }
		
		if ($this->isResponseDeclined($responseData)) {
		    $this->cleanSessionData($data['paymentMethod_name']);
		}

        if (isset($responseData['order_id'])) {
            $_SESSION['pm_order_id'] = $responseData['order_id'];
        }

        return $this->prepareResponseData($responseData);
    }

    /**
    * $paymentMethod - possible values: 'INVOICE', 'SDD'
    */	
    public function confirmOrder($paymentMethod, $order_id = null)
    {
		// prepare data for order confirmation
        $requestData = array();
		if (!is_null($this->eshopDataProvider)) {
            $requestData = $this->eshopDataProvider->collectConfirmData();
        }

		$orderTransactionName = 'pm_order_transaction_id' . $paymentMethod;

		if (isset($_SESSION[$orderTransactionName])) {
			$requestData['pm_order_transaction_id'] = $_SESSION[$orderTransactionName];
		}

		// Overwrite order_id by the new one provided for confirmation
		if (!is_null($order_id) && isset($requestData['order_id'])) {
			$requestData['order_id'] = $order_id;
		}

        $this->pmClient->setEndPoint($this->endPointUrl . 'confirmOrder');
        $responseData = $this->sendRequest($requestData);

        if ($this->isResponseOK($responseData)) {
            $this->cleanSessionData("SDD");
            $this->cleanSessionData("INVOICE");

            if (isset($_SESSION['pm_order_id'])) {
                unset($_SESSION['pm_order_id']);
			}

            if (isset($_SESSION["pm_verify"])) {
                unset($_SESSION["pm_verify"]);
		    }
		}

        if (!is_null($this->responseHandler)) {
            if ($this->isResponseOK($responseData)) {
                $this->responseHandler->handleConfirmOrderResponseOK($responseData);
            } else {
                $this->responseHandler->handleConfirmOrderResponseError($responseData);
            }
        }

        return $responseData;
    }

    private function cleanSessionData($paymentMethod) {
        $orderTransactionName = 'pm_order_transaction_id' . $paymentMethod;

        if (isset($_SESSION[$orderTransactionName])) {
            unset($_SESSION[$orderTransactionName]);
        }

        $pmResponseName = "pm_response" . $paymentMethod;
        if (isset($_SESSION[$pmResponseName])) {
            unset($_SESSION[$pmResponseName]);
        }

    }

    /**
     * Generates private key and public certificate, submits certificate to paymorrow service.
     *
     * @param $certData
     * @param $requestData
     * @return mixed
     * Array:
     * <ul>
     *  <li>'privateKey' => generated private key</li>
     *  <li>'merchantCertificate' => generated certificate</li>
     *  <li>'merchantCertificateConfirmationPdf' => PDF confirmation letter returned from service request</li>
     *  <li>'keyId' => identifier of your (merchant's) certificate for communication with Paymorrow</li>
     *  <li>'timestamp' => date and time when the certificate was succesfully uploaded</li>
     *  <li>'paymorrowCertificate' => Paymorrow certificate returned from service request</li>
     * </ul>
     */
    public function submitCertificate($certData, $requestData)
    {
        // generate private key and public certificate
        $gen = new CertificateGenerator();
        $merchantGeneratedKeys = $gen->generateCertificate($certData);
        $privateKey = $merchantGeneratedKeys['privateKey'];
        $merchantCertificate = $merchantGeneratedKeys['certificate'];

        if (!is_null($this->eshopDataProvider)) {
            $commonData = $this->eshopDataProvider->collectCommonData();
            $requestData = array_merge($commonData, $requestData);
        }

        $this->pmClient->setEndPoint($this->endPointUrl . 'submitCertificate');
        $this->pmClient->setPrivateKeyBytes($privateKey);

        $requestData["merchantCertificate"] = base64_encode($merchantGeneratedKeys['certificate']);;

        $response =  $this->sendRequest($requestData);

        if (!is_null($this->responseHandler)) {
            $this->responseHandler->handleSubmitCertificateResponse($response);
        }

        $response['privateKey'] = $privateKey;
        $response['merchantCertificate'] = $merchantCertificate;
        $response['paymorrowCertificate'] = base64_decode($response['paymorrowCertificate']);

        return $response;
    }

    public function isAlive($data)
    {
        $this->endPoint = $data['endPointUrl'];
        // prepare data for order confirmation
        $requestData = array();
        $requestData = array_merge($requestData, $data);
        unset($requestData['endPointUrl']);

//        if (!is_null($this->eshopDataProvider)) {
//            $requestData = $this->eshopDataProvider->collectCommonData();
//        }

        $this->pmClient->setPrivateKeyBytes("");
        $this->pmClient->setEndPoint($this->endPointUrl . 'isAlive');
        $response = $this->sendRequest($requestData);

        return $response;
    }


    private function mergeEshopDataWithRequestData($eshopData, $data)
    {
        $outputData = array();

        // filter blacklisted fields
        $filterOutKeys = array( 'customer_id', 'customer_group', 'customer_history' );
        if (!is_null($data)) {
            $data = array_diff_key($data, array_flip($filterOutKeys));
        }

        if (!is_null($eshopData)) {
            foreach ($eshopData as $key => $value) {
                $outputData[$key] = $value;
            }
        }

        if (!is_null($data)) {
            foreach ($data as $key => $value) {
                if (self::pmStartsWith($key, "pm_")) {
                    $outputData[substr($key, 3)] = $value;
                }
            }
        }

        return $outputData;
    }

    protected function prepareResponseData($data)
    {
		// select right parameters which can be used in payment method setup (use clientData)
		$filteredData = $this->filterClientData($data);
        
        // add PM_PREFIX to data keys
        $outputData = array();

        if (!is_null($filteredData)) {
            foreach ($filteredData as $key => $value) {
                $newKey = PaymorrowGateway::PM_PREFIX . $key;
                $outputData[$newKey] = $value;
            }
        }

        return $outputData;
    }

    private function validateInputParameters($data)
    {
        // here you can validate request parameters
    }


    /**
     * @param mixed $pmClient
     */
    public function setPmClient($pmClient)
    {
        $this->pmClient = $pmClient;
    }

	/**
     * 
     */
    public function getPmClient()
    {
        return $this->pmClient;
    }

    public function setEndPointUrl($endPointUrl)
    {
        $this->endPointUrl = $endPointUrl;
    }
	
    public function getEndPointUrl()
    {
        return $this->endPointUrl;
    }	

    /**
     * @param $responseData
     * @return bool
     */
    public function isResponseOK($responseData)
    {
        return isset($responseData['response_status'])
        && $responseData['response_status'] === 'OK'
        && ((isset($responseData['order_status']) && ($responseData['order_status'] === 'ACCEPTED'
                || $responseData['order_status'] === 'VALIDATED'
                || $responseData['order_status'] === 'ACCEPTED_CONFIRMED')));
    }

    /**
     * @param $responseData
     * @return bool
     */
    public function isResponseDeclined($responseData)
    {
        return isset($responseData['response_status'])
        && $responseData['response_status'] === 'OK'
        && ((isset($responseData['order_status']) && ($responseData['order_status'] === 'DECLINED'
                || $responseData['order_status'] === 'DECLINED_FINAL')));
    }


    private function sendRequest($data)
    {
        return $this->pmClient->sendRequest($data);
    }

    /**
     * @param mixed $responseHandler
     */
    public function setResponseHandler($responseHandler)
    {
        $this->responseHandler = $responseHandler;
    }

    /**
     * @param mixed $eshopDataProvider
     */
    public function setEshopDataProvider($eshopDataProvider)
    {
        $this->eshopDataProvider = $eshopDataProvider;
    }

    /**
     * @param $data
     * @return new array containing only those fields from $data that are enumerated in $data['clientData']
     */
    private function filterClientData($data)
    {
        $result = array();
        if (isset($data['clientData'])) {
            $fields = explode(',', $data['clientData']);
            foreach ($fields as $field) {
                if (!empty($field)) {
                    if (isset($data[$field])) {
                        $result[$field] = $data[$field];
                    }
                }
            }
        } else {
            // when error appears - sent it
            $result = $data;
        }
        return $result;
    }
	/**
	 * @param $address array with following mandatory fields : firstName, lastName, street, houseNo, zip
	 * @return string hash of the address
	 */
	public static function addressHash($address)
	{
		$s = $address['lastName'];
		$s .= $address['street'];
		$s .= $address['houseNo'];
		$s .= $address['zip'];
		return md5($s);
	}

	/**
	* address1 should be shipping address from customer profile
	* address2 should be shipping address from paymorrow (customer_shippingAddress_* from prepareOrder response)
	* @param $address1 array with following mandatory fields : firstName, lastName, street, city, zip; optional fields are: houseNo
	* @param $address2 array with following mandatory fields : firstName, lastName, street, city, zip; optional fields are: houseNo
	* @return boolean
	*/
	public static function isSimilarAddress($address1, $address2) {
        $zip1 = $address1['zip'];
        $zip2 = $address2['zip'];
        $zip1 = str_replace(' ', '', $zip1);
        $zip2 = str_replace(' ', '', $zip2);
        $part1 = substr($zip1, 0, 2);
        $part2 = substr($zip2, 0, 2);
        return $part1 === $part2;
    }


    public static function pmStartsWith($haystack, $needle)
    {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }

    public static function pmEndsWith($haystack, $needle)
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    public static function pmCopyData($array, $name)
    {
        $res = NULL;
        if (isset($array[$name])) {
            $res = $array[$name];
        }

        return $res;
    }
}


