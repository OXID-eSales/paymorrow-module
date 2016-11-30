
<?php

/**
 * @package Paymorrow Gateway
 * @copyright (C) 2015 - Paymorrow GmbH
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

require_once '../paymorrow_client/PaymorrowClient.php';
require_once '../PaymorrowGateway.php';
require_once '../PaymorrowWsResponseHandler.php';
require_once '../EshopDataProvider.php';

class PaymorrowGatewayTest extends PHPUnit_Framework_TestCase
{

    private $gateway;
    private $pmClientMock;
    private $responseHandlerMock;
    private $eshopDataProviderMock;

    protected function setUp()
    {
        $this->gateway = new PaymorrowGateway();

        $this->pmClientMock = $this->getMock('PaymorrowClient');
        $this->gateway->setPmClient($this->pmClientMock);
        $this->gateway->setEndPointUrl(null);

        $this->responseHandlerMock = $this->getMock('PaymorrowWsResponseHandler',
            array('handlePrepareOrderResponseOK',
                'handlePrepareOrderResponseError',
                'handleConfirmOrderResponseOK',
                'handleConfirmOrderResponseError',
                'handleSubmitCertificateResponse'));
        $this->gateway->setResponseHandler($this->responseHandlerMock);

        $this->eshopDataProviderMock = $this->getMock("EshopDataProvider",
            array('collectEshopData', 'collectConfirmData', 'printPmData'));
    }

    public function testCopyData()
    {
        $array = array();

        $value = "value";
        $key = "key";
        $array[$key] = $value;

        $result = copyData($array, $key);
        $this->assertEquals($result, $value);
    }

    public function testCopyData_notSet()
    {
        $result = copyData(array(), "key");
        $this->assertEquals($result, null, "Result should be null.");
    }

    public function testStartsWith()
    {
        $this->assertTrue(PaymorrowGateway::pmStartsWith("Hamburger", "Hamburg"));
        $this->assertFalse(PaymorrowGateway::pmStartsWith("Hamburg", "Hamburger"));
        $this->assertTrue(PaymorrowGateway::pmStartsWith("text", ""));
        $this->assertTrue(PaymorrowGateway::pmStartsWith("", ""));
        $this->assertFalse(PaymorrowGateway::endsWith("", "any pattern"));
        $this->assertFalse(PaymorrowGateway::pmStartsWith("A text", "text"));
    }

    public function testEndsWith()
    {
        $this->assertFalse(PaymorrowGateway::pmEndsWith("aaa", "aaaa"));
        $this->assertTrue(PaymorrowGateway::pmEndsWith("aaa", "aaa"));
        $this->assertTrue(PaymorrowGateway::pmEndsWith("text", ""));
        $this->assertTrue(PaymorrowGateway::pmEndsWith("", ""));
        $this->assertFalse(PaymorrowGateway::pmEndsWith("", "any pattern"));
        $this->assertTrue(PaymorrowGateway::pmEndsWith("A text", "text"));
    }

    /**
     * Initial request -> pm_order_transaction_id and response data are saved into session.
     */
    public function testPrepareOrder_initialRequest()
    {
        $requestData = array();
        $requestDataUpdated = array('prepareOrder_requestType' => 'INITIAL');
        $id = 123456;
        $pmTxnId = 'pm_order_transaction_id';
        $response = array($pmTxnId => $id, 'order_id' => '111abc');

        $this->pmClientMock->expects($this->once())
            ->method('sendRequest')
            ->with($this->equalTo($requestDataUpdated))
            ->will($this->returnValue($response));

        $this->gateway->setResponseHandler(null); // no need to handle response in this test
        $this->gateway->prepareOrder($requestData);

        $this->assertEquals($_SESSION[$pmTxnId], $id, "Correct pm_order_transaction_id should be stored in session.");
        $this->assertEquals($_SESSION['pm_response'], $response, "Response data should be stored in session.");
    }

    /**
     * Initial request -> pm_order_transaction_id and response data are saved into session.
     */
    public function testPrepareOrder_updatedRequest()
    {
        $id = 123456;
        $pmTxnId = 'pm_order_transaction_id';
        $_SESSION[$pmTxnId] = $id;

        $requestData = array();
        $requestDataUpdated = array($pmTxnId => $id, 'prepareOrder_requestType' => 'UPDATED');

        $this->pmClientMock->expects($this->once())
            ->method('sendRequest')
            ->with($this->equalTo($requestDataUpdated))
            ->will($this->returnValue(null));

        $this->gateway->setResponseHandler(null); // no need to handle response in this test
        $this->gateway->prepareOrder($requestData);
    }

    public function handlersDataProvider_prepareOrder()
    {
        return array(
            // prepared order Not valid request:  error "100" - missing required field
            array('PrepareOrder1.txt', 'handlePrepareOrderResponseError'),
            // Not valid request:  error "401"
            array('PrepareOrder2.txt', 'handlePrepareOrderResponseError'),
            // Valid request: error simulate
            array('PrepareOrder3.txt', 'handlePrepareOrderResponseError'),
            // Valid request: operationMode=VALIDATE
            array('PrepareOrder4.txt', 'handlePrepareOrderResponseOK'),
            // Valid request: requestType=UPDATED
            array('PrepareOrder5.txt', 'handlePrepareOrderResponseOK'),
            // Valid request: operationMode=VALIDATE
            array('PrepareOrder6.txt', 'handlePrepareOrderResponseOK'),
            // Valid request: operationMode=RISK_CHECK
            array('PrepareOrder7.txt', 'handlePrepareOrderResponseOK'),
            // Valid request: operationMode=RISK_PRECHECK
            array('PrepareOrder8.txt', 'handlePrepareOrderResponseOK'),
            // Valid request: order declined
            array('PrepareOrder9.txt', 'handlePrepareOrderResponseError'),
            // Not valid request direct debit
            array('PrepareOrder10.txt', 'handlePrepareOrderResponseError'),
            // Valid request direct debit: operationMode=RISK_CHECK
            array('PrepareOrder11.txt', 'handlePrepareOrderResponseOK'),
            // Valid request direct debit: operationMode=RISK_CHECK-IBAN
            array('PrepareOrder12.txt', 'handlePrepareOrderResponseOK'),
            // Valid request direct debit: operationMode=VALIDATE
            array('PrepareOrder13.txt', 'handlePrepareOrderResponseOK')
        );
    }

    /**
     * Receiving a response is mocked by loading response from file. Then it is checked whether expected response
     * handler was called.
     *
     * @dataProvider handlersDataProvider_prepareOrder
     */
    public function testHandlerCalled_prepareOrder($fileName, $expectedMethodCalled)
    {
        $responseData = $this->readData('response/' . $fileName);

        $this->pmClientMock->expects($this->once())
            ->method('sendRequest')
            ->will($this->returnValue($responseData));

        $this->responseHandlerMock->expects($this->once())
            ->method($expectedMethodCalled)
            ->with($this->equalTo($responseData));

        $this->gateway->prepareOrder(null);
    }

    public function handlersDataProvider_confirmOrder()
    {
        return array(
            // confirm order accepted
            array('ConfirmOrderAccepted.txt', 'handleConfirmOrderResponseOK'),
            // confirm order declined
            array('ConfirmOrderDeclined.txt', 'handleConfirmOrderResponseError'),
            // confirm order error
            array('ConfirmOrderError.txt', 'handleConfirmOrderResponseError'),
        );
    }

    /**
     * Receiving a response is mocked by loading response from file. Then it is checked whether expected response
     * handler was called.
     *
     * @dataProvider handlersDataProvider_confirmOrder
     */
    public function testHandlerCalled_confirmOrder($fileName, $expectedMethodCalled)
    {
        $responseData = $this->readData('response/' . $fileName);

        $this->pmClientMock->expects($this->once())
            ->method('sendRequest')
            ->will($this->returnValue($responseData));

        $this->responseHandlerMock->expects($this->once())
            ->method($expectedMethodCalled)
            ->with($this->equalTo($responseData));

        $this->gateway->confirmOrder(null);
    }

    public function testPrepareOrder_mergeEshopData()
    {
        $this->gateway->setEshopDataProvider($this->eshopDataProviderMock);

        $eshopData = $this->readData('request/PrepareOrder6.txt');
        $this->assertEquals($eshopData['customer_billingAddress_postalCode'], 73492);

        $jsData = array('pm_customer_billingAddress_postalCode' => 12345);
        $jsData['another_field'] = 'foo'; // field without 'pm_' prefix is thrown away

        $mergedData = $this->readData('request/PrepareOrder6.txt');
        $mergedData['pm_customer_billingAddress_postalCode'] = 12345;

        $this->eshopDataProviderMock->expects($this->once())
            ->method('collectEshopData')
            ->will($this->returnValue($eshopData));

        $this->pmClientMock->expects($this->once())
            ->method('sendRequest')
            ->will($this->returnValue($mergedData)); // checks data was merged

        $this->gateway->prepareOrder($jsData);
    }

    public function testFilterClientData()
    {
        // response contains line:
        // clientData=customer_shippingAddress_city,customer_shippingAddress_country
        $responseData = $this->readData('response/ConfirmOrderAccepted.txt');

        $this->pmClientMock->expects($this->once())
            ->method('sendRequest')
            ->will($this->returnValue($responseData));

        $filteredData = $this->gateway->confirmOrder(null);

        $this->assertEquals(2, count($filteredData), "Unexpected number of fields in response after filtering.");
        $this->assertEquals('Rainau', $filteredData['pm_customer_shippingAddress_city'], "Unexpected field value.");
        $this->assertEquals('DE', $filteredData['pm_customer_shippingAddress_country'], "Unexpected field value.");
    }

    public function filterigDataProvider()
    {
        return array(
            array('PrepareOrder12.txt'), // response contains no 'clientData' field
            array('PrepareOrder13.txt') // response contains empty 'clientData' field
        );
    }

    /**
     * @dataProvider filterigDataProvider
     */
    public function testFilterClientData_empty($fileName)
    {
        $responseData = $this->readData('response/' . $fileName);

        $this->pmClientMock->expects($this->once())
            ->method('sendRequest')
            ->will($this->returnValue($responseData));

        $filteredData = $this->gateway->prepareOrder(null);

        $this->assertEquals(0, count($filteredData), "No data should remain in result after filtering.");
    }

    public function testSubmitCertificate()
    {
        // collect data for certificate generation
        $certData = array(
            "countryName" => 'CZ',
            "stateOrProvinceName" => 'CZ',
            "localityName" => 'Prague',
            "organizationName" => 'My Org.',
            "organizationalUnitName" => 'My Org. Unit.',
            "commonName" => 'Frantisek Dobrota',
            "emailAddress" => 'test@example.com'
        );
        // initialization code from Paymorrow
        $requestData["initializationCode"] = 'abc';

        $responseData = array(
            'keyId' => '1234356',
            'timestamp' => ''
        );

        $this->pmClientMock->expects($this->once())
            ->method('sendRequest')
            ->will($this->returnValue($responseData));

        $result = $this->gateway->submitCertificate($certData, $requestData);

        $this->assertArrayHasKey('privateKey', $result);
        $this->assertArrayHasKey('merchantCertificate', $result);
        $this->assertArrayHasKey('keyId', $result);
        $this->assertArrayHasKey('timestamp', $result);
        $this->assertArrayHasKey('merchantCertificateConfirmationPdf', $result);
        $this->assertArrayHasKey('paymorrowCertificate', $result);

    }

    public function addressHashDataProvider()
    {
        return array(
            array($this->createAddress('Müller', 'Bahnhofstraße', '123', '55996', '_'), '8f4bef04c7f11cd556723d707f6d43e3')
        );
    }

    /**
     * @dataProvider addressHashDataProvider
     */
    public function testAddressHash($addr, $hash)
    {
        $this->assertEquals($hash, PaymorrowGateway::addressHash($addr));
    }

    public function similarAddressDataProvider()
    {
        return array(
            array($this->createAddress("_", "_", "_", "55 000", "_"),
                $this->createAddress("_", "_", "_", "55 000", "_")),

            array($this->createAddress("_", "_", "_", "55 000", "_"),
                $this->createAddress("_", "_", "_", "55 123", "_")),

            array($this->createAddress("_", "_", "_", "09385", "_"),
                $this->createAddress("_", "_", "_", "09385", "_")),

            array($this->createAddress("_", "_", "_", " 671  1  7", "_"),
                $this->createAddress("_", "_", "_", "67117", "_"))

        );
    }

    public function similarAddressDataProvider_false()
    {
        return array(
            array($this->createAddress("_", "_", "_", "12 000", "_"),
                $this->createAddress("_", "_", "_", "55 000", "_")),

            array($this->createAddress("_", "_", "_", "01326", "_"),
                $this->createAddress("_", "_", "_", "001326", "_")),

            array($this->createAddress("_", "_", "_", "01326", "_"),
                $this->createAddress("_", "_", "_", "1326", "_")),
        );
    }

    /**
     * @dataProvider similarAddressDataProvider
     */
    public function testIsSimilarAddress($addr1, $addr2)
    {
        $this->assertTrue(PaymorrowGateway::isSimilarAddress($addr1, $addr2));
    }

    /**
     * @dataProvider similarAddressDataProvider_false
     */
    public function testIsSimilarAddress_false($addr1, $addr2)
    {
        $this->assertFalse(PaymorrowGateway::isSimilarAddress($addr1, $addr2));
    }

    private function readData($file)
    {
        $fileContent = file("./data/" . $file);
        foreach ($fileContent as $line) {
            $keyvalue = explode("=", $line, 2);
            if (count($keyvalue) == 2) {
                $data[$keyvalue[0]] = trim($keyvalue[1]);
            }
        }
        return $data;
    }

    private function createAddress($ln, $street, $house, $zip, $city)
    {
        return array(
            'lastName' => $ln,
            'street' => $street,
            'houseNo' => $house,
            'zip' => $zip,
            'city' => $city
        );
    }
}