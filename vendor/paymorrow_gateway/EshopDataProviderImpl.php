<?php

/**
 * @package Paymorrow Gateway
 * @copyright (C) 2015 - Paymorrow GmbH
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

require_once('EshopDataProvider.php');

class EshopDataProviderImpl implements EshopDataProvider {

	private $merchantId = "oxid_ws";
	private $mpiSignature = "Oxid123";
	private $requestLanguageCode = "de";
	private static $requestId = 0;
	private static $orderId = 0;
	
    public function collectCommonData()
    {
        $data = array();

        $data['merchantId'] = $this->merchantId;
        $data['mpiSignature'] = $this->mpiSignature;
        $data['request_languageCode'] = $this->requestLanguageCode;
        $data['request_id'] = self::$requestId++;

        return $data;
    }

	/**
	 * INTEGRATION - this method needs to collect all mandatory and optional data fields
	 *               that has to be sent to Paymorrow WS and are available in the eshop system
	 */
	public function collectEshopData() {
        $data = $this->collectCommonData();

        $data['order_id'] = $this->getOrderId();
        $data['source'] = "PAYMORROW_GATEWAY_JS";
        $data['operationMode'] = "RISK_CHECK";

		//INTEGRATION - connect to real order, customer and system data
		//
		// For details about the parameters please see API specification
		//

		// order data
		$data['order_grossAmount'] = "198.7";
		$data['order_vatAmount'] = "31.7";
		$data['order_currency'] = "EUR";
		 
		//Every order item needs to be set according to examples below including shipping and payment fee if exists 
		$data['lineItem_1_quantity'] = "1"; 
		$data['lineItem_1_articleId'] = "a-0001"; 
		$data['lineItem_1_name'] = "mp3 player"; 
		$data['lineItem_1_type'] = "GOODS"; // GOODS for all articles, VOUCHER or SHIPPING or PAYMENT_FEE 
		$data['lineItem_1_category'] = "ELECTRONICS"; // free text for your categories 
		$data['lineItem_1_unitPriceGross'] = "119.0"; 
		$data['lineItem_1_grossAmount'] = "119.0"; 
		$data['lineItem_1_vatAmount'] = "19.0"; 
		$data['lineItem_1_vatRate'] = "19.00"; 
		 
		$data['lineItem_2_quantity'] = "1"; 
		$data['lineItem_2_articleId'] = "a-0002"; 
		$data['lineItem_2_name'] = "CD player"; 
		$data['lineItem_2_type'] = "GOODS"; 
		$data['lineItem_2_category'] = "ELECTRONICS"; 
		$data['lineItem_2_unitPriceGross'] = "79.7"; 
		$data['lineItem_2_grossAmount'] = "79.7"; 
		$data['lineItem_2_vatAmount'] = "12.73"; 
		$data['lineItem_2_vatRate'] = "19.00"; 

		//Customer data
		$data['customer_id'] = "oxid_cust"; // Your unique ID for this customer - optional

		$data['customer_gender'] = $_SESSION['eshopData_gender'];
		$data['customer_firstName'] = $_SESSION['eshopData_firstName'];
		$data['customer_lastName'] = $_SESSION['eshopData_lastName'];
		$data['customer_phoneNumber'] = $_SESSION['eshopData_phoneNumber'];
		$data['customer_mobileNumber'] = $_SESSION['eshopData_mobileNumber'];

		$data['customer_billingAddress_street'] = $_SESSION['eshopData_billingAddressStreet'];
		$data['customer_billingAddress_houseNo'] = $_SESSION['eshopData_billingAddressHouseNo'];
		$data['customer_billingAddress_city'] = $_SESSION['eshopData_billingAddressCity'];
		$data['customer_billingAddress_postalCode'] = $_SESSION['eshopData_billingAddressPostalCode'];
		$data['customer_billingAddress_country'] = $_SESSION['eshopData_billingAddressCountryCode'];

		$data['customer_shippingAddress_street'] = $_SESSION['eshopData_shippingAddressStreet'];
		$data['customer_shippingAddress_houseNo'] = $_SESSION['eshopData_shippingAddressHouseNo'];
		$data['customer_shippingAddress_city'] = $_SESSION['eshopData_shippingAddressCity'];
		$data['customer_shippingAddress_postalCode'] = $_SESSION['eshopData_shippingAddressPostalCode'];
		$data['customer_shippingAddress_country'] = $_SESSION['eshopData_shippingAddressCountryCode'];
        $data['customer_shippingAddress_firstName'] = $_SESSION['eshopData_shippingAddressFirstName']; //optional
        $data['customer_shippingAddress_lastName'] = $_SESSION['eshopData_shippingAddressLastName']; //optional

		$data['customer_email'] = $_SESSION['eshopData_email'];
		$data['customer_dateOfBirth'] = $_SESSION['eshopData_dateOfBirth'];

//      $data['customer_gender'] = "MALE"; // MALE or FEMALE - if not set it will be asked by JS
		//$data['customer_firstName'] = "Paul";
//		$data['customer_firstName'] = "decline";
//		$data['customer_lastName'] = "Novymedik";
//		$data['customer_lastName'] = "decline";
//		$data['customer_mobileNumber'] = "+49 170 002 198"; // if not set, will be asked by JS
		//$data['customer_email'] = "pmtst@gmx.de";
		//$data['customer_dateOfBirth'] = "1968-02-15"; // if not set, will be asked by JS

//		$data['customer_billingAddress_street'] = "Neuwerkstrasse 10";
		// $data['customer_billingAddress_houseNo'] = "10";  // optional if part of street 
//		$data['customer_billingAddress_postalCode'] = "99084";
//		$data['customer_billingAddress_city'] = "Erfurt";
//        $data['customer_billingAddress_country'] = "DE";

		//shipping address is optional if billing and shipping are the same 
//		$data['customer_shippingAddress_street'] = "Neuwerkstrasse 10";
		// $data['customer_shippingAddress_houseNo'] = "50"; 
//		$data['customer_shippingAddress_postalCode'] = "99084";
//		$data['customer_shippingAddress_city'] = "Erfurt";
//        $data['customer_shippingAddress_country'] = "DE";

		//        $data['customer_shippingAddress_firstName'] = "Jaja"; //optional 
		//        $data['customer_shippingAddress_lastName'] = "Paja"; //optional 

		//Customer history data 
		$data['customer_group'] = "EXISTING"; // optional - Please see comment in the documentation
		 
		 
		// Please DON'T change unless instructed by Paymorrow Support 
		$data['device_checkId'] = "oxid-" . session_id(); 
		$data['client_browser_session_id'] = session_id(); 
		$data['client_cookies_id'] = session_id(); 
		$data['diffAddressesEditing_disabled'] = false;

		$data['client_ipAddress'] = $_SERVER['REMOTE_ADDR']; // only change in case you are using proxy or similar 
		//and $_SERVER['REMOTE_ADDR'] does not contain the client_ip 
		 
		$data['client_browser_header'] = $this->getBrowserHeaders();  		
		// END of INTEGRATION code


        return $data;
    }

	/**
	 * INTEGRATION - valida data at the time of order confirmation needs to be set
	 *               to prove that confirmed order isn't different then prepared order
	 */
	public function collectConfirmData() {
        $data = $this->collectCommonData();
		
		$data['order_id'] = $this->getOrderId(); // It has to be valid business order id sent to customer
		
		// INTEGRATION - use data stored in eshop session valid at the time of order confirmation  
		$shippingAddress = array(
                'lastName' => 'customer_lastName',// if the last name is not set in collectEshopData-customer_shippingAddress_lastName just skip this line
                'street'   => 'customer_shippingAddress_street',
                'houseNo'  => 'customer_shippingAddress_houseNo', // if houseNo is part of street just skipp this line
                'zip'      => 'customer_shippingAddress_postalCode'
            );
		$billingAddress = array(
                'lastName' => 'customer_lastName', 
                'street'   => 'customer_billingAddress_street',
                'houseNo'  => 'customer_billingAddress_houseNo', // if houseNo is part of street just skipp this line
                'zip'      => 'customer_billingAddress_postalCode'
            );
		
		$data['verification_customer_email'] = $_SESSION['eshopData_email'];
		$data['verification_order_grossAmount'] = "198.7";
		
		//Please DON'T change this here 
		$data['verification_billingHash'] = PaymorrowGateway::addressHash($billingAddress); 
		$data['verification_shippingHash'] = PaymorrowGateway::addressHash($shippingAddress); 
		// END of INTEGRATION code

		return $data;
	}

	/**
	 * should return Order ID of that order
     * often the final (human readable) order id is generated after storing the order and not yet available
     * for that purpose this generates a random Order ID that will be updated later once the final order ID is available
	*/
    private function getOrderId() {
        // returns unique order id
        srand(time());
        $val = rand();

        return "oxid-" . $val;
    }

	/**
     * DON'T EDIT  print string which contains all needed data for Paymorrow JS
	 */
    public function printPmData()
    {
        $data = $this->collectEshopData();

        $sessionId = $this->findInArray($data, 'client_browser_session_id', NULL);
        $cookieId = $this->findInArray($data, 'client_cookies_id', NULL);
        $langcode = $this->findInArray($data, 'request_languageCode', NULL);
        $clientIp = $this->findInArray($data, 'client_ipAddress', NULL);

        $firstName = $this->findInArray($data, 'customer_firstName', NULL);
        $lastName = $this->findInArray($data, 'customer_lastName', NULL);

        $phone = $this->findInArray($data, 'customer_phoneNumber', NULL);
        $mobile = $this->findInArray($data, 'customer_mobileNumber', NULL);
        $dob = $this->findInArray($data, 'customer_dateOfBirth', NULL);
        $gender = $this->findInArray($data, 'customer_gender', NULL);
        $email = $this->findInArray($data, 'customer_email', NULL);
        $orderAmount = $this->findInArray($data, 'order_grossAmount', NULL);
        $currencyCode = $this->findInArray($data, 'order_currency', NULL);

        $billingStreet = $this->findInArray($data, 'customer_billingAddress_street', NULL);
        $billingHouseNo = $this->findInArray($data, 'customer_billingAddress_houseNo', NULL);
        $billingLocality = $this->findInArray($data, 'customer_billingAddress_city', NULL);
        $billingPostalCode = $this->findInArray($data, 'customer_billingAddress_postalCode', NULL);
        $billingCountryCode = $this->findInArray($data, 'customer_billingAddress_country', NULL);

        $shippingStreet = $this->findInArray($data, 'customer_shippingAddress_street', $billingStreet);
        $shippingHouseNo = $this->findInArray($data, 'customer_shippingAddress_houseNo', $billingHouseNo);
        $shippingLocality = $this->findInArray($data, 'customer_shippingAddress_city', $billingLocality);
        $shippingPostalCode = $this->findInArray($data, 'customer_shippingAddress_postalCode', $billingPostalCode);
        $shippingCountryCode = $this->findInArray($data, 'customer_shippingAddress_country', $billingCountryCode);
        $shippingFirstName = $this->findInArray($data, 'customer_shippingAddress_firstName', NULL);
        $shippingLastName = $this->findInArray($data, 'customer_shippingAddress_lastName', NULL);

        $diffAddressesEditDisabled = $this->findInArray($data, 'diffAddressesEditing_disabled', NULL);

        $arr = array(
            'phone' => $phone,
            'mobile' => $mobile,
            'session_id' => $sessionId,
            'cookie_id' => $cookieId,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'dob' => $dob,
            'gender' => $gender,
            'email' => $email,
            'street' => $billingStreet,
            'houseNumber' => $billingHouseNo,
            'locality' => $billingLocality,
            'postalCode' => $billingPostalCode,
            'country' => $billingCountryCode,
            'shippingStreet' => $shippingStreet,
            'shippingHouseNumber' => $shippingHouseNo,
            'shippingLocality' => $shippingLocality,
            'shippingPostalCode' => $shippingPostalCode,
            'shippingCountry' => $shippingCountryCode,
            'shippingFirstName' => $shippingFirstName,
            'shippingLastName' => $shippingLastName,
            'orderAmount' => $orderAmount,
            'currencyCode' => $currencyCode,
            'langcode' => $langcode,
            'client_ip' => $clientIp,
            'diffAddressesEditing_disabled' => $diffAddressesEditDisabled
        );

        echo json_encode($arr);
    }

    private function findInArray($arr, $key, $default)
    {
        $val = $default;

        if (array_key_exists($key, $arr)) {
            $val = $arr[$key];
        }

        return $val;
    }
	
	private function getBrowserHeaders() {
		if(!function_exists('apache_request_headers')) {
			function apache_request_headers() {
				$headers = array();
				foreach($_SERVER as $key => $value) {
					if(substr($key, 0, 5) == 'HTTP_') {
						$headers[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
					}
				}
				return $headers;
			}
		}
		$headers = apache_request_headers();
		$headerStr = '';
		foreach ($headers as $header => $value) {
			$headerStr = $headerStr . " $header: $value\n";
		}
	
		$headerBase64 = base64_encode($headerStr);	
		
		return $headerBase64;
	}
}
