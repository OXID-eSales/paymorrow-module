<?php
/**
 * This file is part of the OXID module for Paymorrow payment.
 *
 * The OXID module for Paymorrow payment is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 *
 * The OXID eShop module for Paymorrow payment is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General
 * Public License for more details.
 *
 * Linking this library statically or dynamically with other modules is making a
 * combined work based on this library. Thus, the terms and conditions of the GNU
 * General Public License cover the whole combination.
 * As a special exception, the copyright holders of this library give you
 * permission to link this library with independent modules to produce an
 * executable, regardless of the license terms of these independent modules, and
 * to copy and distribute the resulting executable under terms of your choice,
 * provided that you also meet, for each linked independent module, the terms and
 * conditions of the license of that module. An independent module is a module
 * which is not derived from or based on this library. If you modify this library,
 * you may extend this exception to your version of the library, but you are not
 * obliged to do so. If you do not wish to do so, delete this exception statement
 * from your version.
 *
 * You should have received a copy of the GNU General Public License along with
 * the OXID module for Paymorrow payment. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Class Unit_Module_Core_OxpsPaymorrowEshopDataProviderTest
 *
 * @see OxpsPaymorrowEshopDataProvider
 */
class Unit_Module_Core_OxpsPaymorrowEshopDataProviderTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowEshopDataProvider
     */
    protected $SUT;


    /**
     * Set initial objects state.
     *
     * @return null|void
     */
    public function setUp()
    {
        parent::setUp();

        // SUT mock
        $this->SUT = $this->getMock( 'OxpsPaymorrowEshopDataProvider', array('getUser', '_getBrowserHeaders') );
        $this->SUT->expects( $this->any() )->method( '_getBrowserHeaders' )->will( $this->returnValue( '#headers' ) );

        // Paymorrow settings mock
        $oSettingsMock = $this->getMock(
            'OxpsPaymorrowSettings',
            array('__construct', 'getMerchantId', 'getMpiSignature', 'getPaymorrowOperationMode')
        );
        $oSettingsMock->expects( $this->any() )->method( 'getMerchantId' )->will( $this->returnValue( 'My_PM_ID' ) );
        $oSettingsMock->expects( $this->any() )->method( 'getMpiSignature' )->will( $this->returnValue( 'Sign_X' ) );
        $oSettingsMock->expects( $this->any() )->method( 'getPaymorrowOperationMode' )->will(
            $this->returnValue( 'VALIDATE' )
        );
        oxTestModules::addModuleObject( 'OxpsPaymorrowSettings', $oSettingsMock );
    }


    public function testCollectCommonData_returnMandatoryAuthenticationFieldsArray()
    {
        $this->SUT->expects( $this->any() )->method( 'getUser' )->will( $this->returnValue( $this->_getUserMock() ) );

        $aData = $this->SUT->collectCommonData();

        $this->assertTrue( is_array( $aData ) );

        $this->assertArrayHasKey( 'merchantId', $aData );
        $this->assertSame( 'My_PM_ID', $aData['merchantId'] );

        $this->assertArrayHasKey( 'mpiSignature', $aData );
        $this->assertSame( 'Sign_X', $aData['mpiSignature'] );

        $this->assertArrayHasKey( 'request_languageCode', $aData );
        $this->assertSame( 'en', $aData['request_languageCode'] );

        $this->assertArrayHasKey( 'request_id', $aData );
        $this->assertFalse( empty( $aData['request_id'] ) );
        $this->assertTrue( is_string( $aData['request_id'] ) );
    }


    public function testCollectEshopData_userDoesNotExist_returnNull()
    {
        $this->SUT->expects( $this->any() )->method( 'getUser' )->will(
            $this->returnValue( $this->_getUserMock( false ) )
        );

        $this->assertNull( $this->SUT->collectEshopData() );
    }

    public function testCollectEshopData_userExistsBillingAddressUsedAsShipping_returnCommonDataUserOrderAndDeviceData()
    {
        // Session mock
        $oSessionMock = $this->getMock( 'oxSession', array('__construct', 'getId', 'getBasket') );
        $oSessionMock->expects( $this->exactly( 2 ) )->method( 'getId' )->will(
            $this->returnValue( 'OXID_Session_ID' )
        );
        $oSessionMock->expects( $this->once() )->method( 'getBasket' )->will(
            $this->returnValue( $this->_getBasketMock() )
        );
        oxRegistry::set( 'oxSession', $oSessionMock );
        $this->setSessionParam( 'blshowshipaddress', '' );

        $this->SUT->expects( $this->any() )->method( 'getUser' )->will( $this->returnValue( $this->_getUserMock() ) );

        $aData = $this->SUT->collectEshopData( false );

        $this->assertTrue( is_array( $aData ) );

        $this->assertArrayHasKey( 'merchantId', $aData );
        $this->assertSame( 'My_PM_ID', $aData['merchantId'] );

        $this->assertArrayHasKey( 'mpiSignature', $aData );
        $this->assertSame( 'Sign_X', $aData['mpiSignature'] );

        $this->assertArrayHasKey( 'request_languageCode', $aData );
        $this->assertSame( 'en', $aData['request_languageCode'] );

        $this->assertArrayHasKey( 'request_id', $aData );
        $this->assertFalse( empty( $aData['request_id'] ) );
        $this->assertTrue( is_string( $aData['request_id'] ) );

        $this->assertArrayHasKey( 'addressEditing_disabled', $aData );
        $this->assertSame( 'N', $aData['addressEditing_disabled'] );

        $this->assertArrayHasKey( 'order_id', $aData );
        $this->assertFalse( empty( $aData['order_id'] ) );
        $this->assertTrue( is_string( $aData['order_id'] ) );

        $this->assertArrayHasKey( 'source', $aData );
        $this->assertSame( 'PAYMORROW_GATEWAY_JS', $aData['source'] );

        $this->assertArrayHasKey( 'operationMode', $aData );
        $this->assertSame( 'VALIDATE', $aData['operationMode'] );

        $this->assertArrayHasKey( 'order_grossAmount', $aData );
        $this->assertSame( 777.88, $aData['order_grossAmount'] );

        $this->assertArrayHasKey( 'order_vatAmount', $aData );
        $this->assertSame( 88.77, $aData['order_vatAmount'] );

        $this->assertArrayHasKey( 'order_currency', $aData );
        $this->assertSame( 'EUR', $aData['order_currency'] );

        $this->assertArrayHasKey( 'pm_item_1_data', $aData );
        $this->assertSame( 'data', $aData['pm_item_1_data'] );

        $this->assertArrayHasKey( 'pm_item_2_number', $aData );
        $this->assertSame( 1, $aData['pm_item_2_number'] );

        $this->assertArrayHasKey( 'customer_id', $aData );
        $this->assertSame( 'Usr_ID', $aData['customer_id'] );

        $this->assertArrayHasKey( 'customer_group', $aData );
        $this->assertSame( 'My Group, Another Group', $aData['customer_group'] );

        $this->assertArrayHasKey( 'customer_title', $aData );
        $this->assertSame( 'Mr', $aData['customer_title'] );

        $this->assertArrayHasKey( 'customer_gender', $aData );
        $this->assertSame( 'MALE', $aData['customer_gender'] );

        $this->assertArrayHasKey( 'customer_firstName', $aData );
        $this->assertSame( 'Example', $aData['customer_firstName'] );

        $this->assertArrayHasKey( 'customer_lastName', $aData );
        $this->assertSame( 'Smith', $aData['customer_lastName'] );

        $this->assertArrayHasKey( 'customer_phoneNumber', $aData );
        $this->assertSame( '+001234567890', $aData['customer_phoneNumber'] );

        $this->assertArrayHasKey( 'customer_mobileNumber', $aData );
        $this->assertSame( '+001234567891', $aData['customer_mobileNumber'] );

        $this->assertArrayHasKey( 'customer_email', $aData );
        $this->assertSame( 'smith@example.com', $aData['customer_email'] );

        $this->assertArrayHasKey( 'customer_dateOfBirth', $aData );
        $this->assertSame( '1922-02-22', $aData['customer_dateOfBirth'] );

        $this->assertArrayHasKey( 'customer_billingAddress_street', $aData );
        $this->assertSame( 'My Street st.', $aData['customer_billingAddress_street'] );

        $this->assertArrayHasKey( 'customer_billingAddress_houseNo', $aData );
        $this->assertSame( '48', $aData['customer_billingAddress_houseNo'] );

        $this->assertArrayHasKey( 'customer_billingAddress_postalCode', $aData );
        $this->assertSame( '12345', $aData['customer_billingAddress_postalCode'] );

        $this->assertArrayHasKey( 'customer_billingAddress_city', $aData );
        $this->assertSame( 'Freiburg', $aData['customer_billingAddress_city'] );

        $this->assertArrayHasKey( 'customer_billingAddress_country', $aData );
        $this->assertSame( 'DE', $aData['customer_billingAddress_country'] );

        $this->assertArrayHasKey( 'customer_shippingAddress_street', $aData );
        $this->assertSame( 'My Street st.', $aData['customer_shippingAddress_street'] );

        $this->assertArrayHasKey( 'customer_shippingAddress_houseNo', $aData );
        $this->assertSame( '48', $aData['customer_shippingAddress_houseNo'] );

        $this->assertArrayHasKey( 'customer_shippingAddress_postalCode', $aData );
        $this->assertSame( '12345', $aData['customer_shippingAddress_postalCode'] );

        $this->assertArrayHasKey( 'customer_shippingAddress_city', $aData );
        $this->assertSame( 'Freiburg', $aData['customer_shippingAddress_city'] );

        $this->assertArrayHasKey( 'customer_shippingAddress_country', $aData );
        $this->assertSame( 'DE', $aData['customer_shippingAddress_country'] );

        $this->assertArrayHasKey( 'customer_shippingAddress_company', $aData );
        $this->assertSame( 'X Inc.', $aData['customer_shippingAddress_company'] );

        $this->assertArrayHasKey( 'device_checkId', $aData );
        $this->assertSame( 'OXID_Session_ID', $aData['device_checkId'] );

        $this->assertArrayHasKey( 'client_browser_session_id', $aData );
        $this->assertSame( 'OXID_Session_ID', $aData['client_browser_session_id'] );

        $this->assertArrayHasKey( 'client_cookies_id', $aData );
        $this->assertSame( 'OXID_Session_ID', $aData['client_cookies_id'] );

        $this->assertArrayHasKey( 'client_ipAddress', $aData );
        $this->assertSame( '127.0.0.007', $aData['client_ipAddress'] );

        $this->assertArrayHasKey( 'client_browser_header', $aData );
        $this->assertSame( '#headers', $aData['client_browser_header'] );
    }

    public function testCollectEshopData_userExistsShippingAddressDiffers_returnCommonDataUserOrderAndDeviceData()
    {
        // Session mock
        $oSessionMock = $this->getMock( 'oxSession', array('__construct', 'getId', 'getBasket') );
        $oSessionMock->expects( $this->exactly( 2 ) )->method( 'getId' )->will(
            $this->returnValue( 'OXID_Session_ID' )
        );
        $oSessionMock->expects( $this->once() )->method( 'getBasket' )->will(
            $this->returnValue( $this->_getBasketMock() )
        );
        oxRegistry::set( 'oxSession', $oSessionMock );
        $this->setSessionParam( 'blshowshipaddress', '1' );

        $this->SUT->expects( $this->any() )->method( 'getUser' )->will( $this->returnValue( $this->_getUserMock() ) );

        $aData = $this->SUT->collectEshopData( false );

        $this->assertTrue( is_array( $aData ) );

        $this->assertArrayHasKey( 'customer_billingAddress_street', $aData );
        $this->assertSame( 'My Street st.', $aData['customer_billingAddress_street'] );

        $this->assertArrayHasKey( 'customer_shippingAddress_street', $aData );
        $this->assertSame( 'Other st.', $aData['customer_shippingAddress_street'] );

        $this->assertArrayHasKey( 'customer_shippingAddress_houseNo', $aData );
        $this->assertSame( '7', $aData['customer_shippingAddress_houseNo'] );

        $this->assertArrayHasKey( 'customer_shippingAddress_postalCode', $aData );
        $this->assertSame( '98765', $aData['customer_shippingAddress_postalCode'] );

        $this->assertArrayHasKey( 'customer_shippingAddress_city', $aData );
        $this->assertSame( 'Town', $aData['customer_shippingAddress_city'] );

        $this->assertArrayHasKey( 'customer_shippingAddress_country', $aData );
        $this->assertSame( 'DE', $aData['customer_shippingAddress_country'] );

        $this->assertArrayHasKey( 'customer_shippingAddress_additionalInfo', $aData );
        $this->assertSame( 'NFO', $aData['customer_shippingAddress_additionalInfo'] );

        $this->assertArrayHasKey( 'customer_shippingAddress_firstName', $aData );
        $this->assertSame( 'Mister', $aData['customer_shippingAddress_firstName'] );

        $this->assertArrayHasKey( 'customer_shippingAddress_lastName', $aData );
        $this->assertSame( 'Burmister', $aData['customer_shippingAddress_lastName'] );

        $this->assertArrayHasKey( 'customer_shippingAddress_company', $aData );
        $this->assertSame( 'My incorporated', $aData['customer_shippingAddress_company'] );

    }

    public function testCollectEshopData_argumentEmpty_logCompiledDataArray()
    {
        // Session mock
        $oSessionMock = $this->getMock( 'oxSession', array('__construct', 'getId', 'getBasket') );
        $oSessionMock->expects( $this->exactly( 2 ) )->method( 'getId' )->will(
            $this->returnValue( 'OXID_Session_ID' )
        );
        $oSessionMock->expects( $this->once() )->method( 'getBasket' )->will(
            $this->returnValue( $this->_getBasketMock() )
        );
        oxRegistry::set( 'oxSession', $oSessionMock );
        $this->setSessionParam( 'blshowshipaddress', '' );

        // Logger mock
        $oLoggerMock = $this->getMock( 'OxpsPaymorrowLogger', array('__construct', 'logWithType') );
        $oLoggerMock->expects( $this->once() )->method( 'logWithType' )->with(
            $this->isType( 'array' ), $this->equalTo( 'collectEshopData' )
        );
        oxRegistry::set( 'OxpsPaymorrowLogger', $oLoggerMock );

        $this->SUT->expects( $this->any() )->method( 'getUser' )->will( $this->returnValue( $this->_getUserMock() ) );

        $aData = $this->SUT->collectEshopData();

        $this->assertTrue( is_array( $aData ) );
    }

    public function testCollectEshopData_orderHistoryAvailable_alsoReturnOrderHistoryData()
    {
        // Session mock
        $oSessionMock = $this->getMock( 'oxSession', array('__construct', 'getId', 'getBasket') );
        $oSessionMock->expects( $this->exactly( 2 ) )->method( 'getId' )->will(
            $this->returnValue( 'OXID_Session_ID' )
        );
        $oSessionMock->expects( $this->once() )->method( 'getBasket' )->will(
            $this->returnValue( $this->_getBasketMock() )
        );
        oxRegistry::set( 'oxSession', $oSessionMock );
        $this->setSessionParam( 'blshowshipaddress', '' );

        // Order mock
        $oOrderMock = $this->getMock(
            'OxpsPaymorrowOxOrder',
            array(
                '__construct', 'load', 'getOrderDate', 'getTotalOrderSum',
                'getPaymentMethodName', 'getOrderStatus'
            )
        );
        $oOrderMock->expects( $this->once() )->method( 'getOrderDate' )->will(
            $this->returnValue( '2014-06-09' )
        );
        $oOrderMock->expects( $this->once() )->method( 'getTotalOrderSum' )->will(
            $this->returnValue( 789.89 )
        );
        $oOrderMock->expects( $this->once() )->method( 'getPaymentMethodName' )->will(
            $this->returnValue( 'Direct Debit' )
        );
        $oOrderMock->expects( $this->once() )->method( 'getOrderStatus' )->will(
            $this->returnValue( 'OK' )
        );

        // Order list mock
        /** @var oxList $oListMock */
        $oListMock = $this->getMock( 'oxList', array('__construct', '__get', 'count') );
        $oListMock->expects( $this->once() )->method( 'count' )->will( $this->returnValue( 1 ) );
        $oListMock->assign( array($oOrderMock) );

        // User mock
        $oUserMock = $this->_getUserMock();
        $oUserMock->expects( $this->once() )->method( 'getOrders' )->with( $this->equalTo( 10 ) )->will(
            $this->returnValue( $oListMock )
        );

        $this->SUT->expects( $this->any() )->method( 'getUser' )->will( $this->returnValue( $oUserMock ) );

        $aData = $this->SUT->collectEshopData( false );

        $this->assertTrue( is_array( $aData ) );

        $this->assertArrayHasKey( 'customer_history_1_orderDate', $aData );
        $this->assertSame( '2014-06-09', $aData['customer_history_1_orderDate'] );

        $this->assertArrayHasKey( 'customer_history_1_totalAmount', $aData );
        $this->assertSame( 789.89, $aData['customer_history_1_totalAmount'] );

        $this->assertArrayHasKey( 'customer_history_1_paymentMethod', $aData );
        $this->assertSame( 'Direct Debit', $aData['customer_history_1_paymentMethod'] );

        $this->assertArrayHasKey( 'customer_history_1_orderStatus', $aData );
        $this->assertSame( 'OK', $aData['customer_history_1_orderStatus'] );
    }


    public function testCollectConfirmData_collectEshopDataPlusTransactionAndVerificationDataThenLogsAndReturnsIt()
    {
        $aExpectedData = array(
            'eshop_data'                     => 1,
            'customer_email'                 => 'smith@example.com',
            'customer_lastName'              => 'Smith',
            'order_grossAmount'              => 888.77,
            'pm_order_transaction_id'        => 'trans-1011010',
            'order_id'                       => 'ORD-111',
            'verification_customer_email'    => 'smith@example.com',
            'verification_shippingHash'      => '#hash777',
            'verification_billingHash'       => '#hash777',
            'verification_order_grossAmount' => '888.77',
        );

        // Session mock
        $this->setSessionParam( 'pm_verify', array('pm_paymentMethod_name' => 'INVOICE') );
        $this->setSessionParam( 'pm_order_transaction_idINVOICE', 'trans-1011010' );
        $this->setSessionParam( 'pm_order_id', 'ORD-111' );

        // SUT mock
        $SUT = $this->getMock( 'OxpsPaymorrowEshopDataProvider', array('collectEshopData') );
        $SUT->expects( $this->once() )->method( 'collectEshopData' )->will(
            $this->returnValue(
                array(
                    'eshop_data'        => 1,
                    'customer_email'    => 'smith@example.com',
                    'customer_lastName' => 'Smith',
                    'order_grossAmount' => 888.77,
                )
            )
        );

        // Gateway mock
        $oGatewayMock = $this->getMock( 'OxpsPaymorrowGateway', array('getAddressHash') );
        $oGatewayMock->expects( $this->exactly( 2 ) )->method( 'getAddressHash' )
            ->with(
                $this->equalTo(
                    array(
                        'lastName' => 'Smith',
                        'street'   => '',
                        'houseNo'  => '',
                        'zip'      => '',
                    )
                )
            )
            ->will( $this->returnValue( '#hash777' ) );

        $oPaymorrow2OxidMock = $this->getMock( 'OxpsOxid2Paymorrow', array('getPaymorrowGateway') );
        $oPaymorrow2OxidMock->expects( $this->once() )->method( 'getPaymorrowGateway' )->will(
            $this->returnValue( $oGatewayMock )
        );
        oxRegistry::set( 'OxpsOxid2Paymorrow', $oPaymorrow2OxidMock );

        // Logger mock
        $oLoggerMock = $this->getMock( 'OxpsPaymorrowLogger', array('logWithType') );
        $oLoggerMock->expects( $this->once() )->method( 'logWithType' )->with(
            $this->equalTo( $aExpectedData ),
            $this->equalTo( 'collectConfirmData' )
        );
        oxRegistry::set( 'OxpsPaymorrowLogger', $oLoggerMock );

        $this->assertSame( $aExpectedData, $SUT->collectConfirmData() );
    }


    public function testPrintPmData_eshopDataEmpty_returnJsonArrayWithAllPrintKeysAndEmptyStringsAsValues()
    {
        $this->setSessionParam( 'pm_init_data', null );

        $SUT = $this->getMock( 'OxpsPaymorrowEshopDataProvider', array('collectEshopData') );
        $SUT->expects( $this->once() )->method( 'collectEshopData' )->will( $this->returnValue( array() ) );

        $this->assertSame(
            json_encode(
                array(
                    'phone'               => '',
                    'mobile'              => '',
                    'session_id'          => '',
                    'cookie_id'           => '',
                    'firstName'           => '',
                    'lastName'            => '',
                    'dob'                 => '',
                    'gender'              => '',
                    'email'               => '',
                    'street'              => '',
                    'houseNumber'         => '',
                    'locality'            => '',
                    'postalCode'          => '',
                    'country'             => '',
                    'shippingStreet'      => '',
                    'shippingHouseNumber' => '',
                    'shippingLocality'    => '',
                    'shippingPostalCode'  => '',
                    'shippingCountry'     => '',
                    'shippingFirstName'   => '',
                    'shippingLastName'    => '',
                    'orderAmount'         => '',
                    'langcode'            => '',
                    'client_ip'           => '',
                    'currencyCode'        => '',
                )
            ),
            $SUT->printPmData()
        );
    }

    public function testPrintPmData_eshopDataIsValid_returnJsonArrayWithEshopDataMApperdToPrintKeys()
    {
        $this->setSessionParam( 'pm_init_data', null );

        $SUT = $this->getMock( 'OxpsPaymorrowEshopDataProvider', array('collectEshopData') );
        $SUT->expects( $this->once() )->method( 'collectEshopData' )->will(
            $this->returnValue(
                array(
                    'merchantId'                          => 'oxid_ws',
                    'mpiSignature'                        => 'Oxid-5.1.4_1.0.0',
                    'request_languageCode'                => 'en',
                    'request_id'                          => '535f5d2b9bfde',
                    'addressEditing_disabled'             => 'N',
                    'order_id'                            => '66b0749a2cb638f956d178cde7766abe',
                    'source'                              => 'PAYMORROW_GATEWAY_JS',
                    'operationMode'                       => 'VALIDATE',
                    'order_grossAmount'                   => '791.1',
                    'order_vatAmount'                     => '126.31',
                    'order_currency'                      => 'EUR',
                    'lineItem_1_quantity'                 => '1',
                    'lineItem_1_articleId'                => 'b56369b1fc9d7b97f9c5fc343b349ece',
                    'lineItem_1_name'                     => 'Kite CORE GTS',
                    'lineItem_1_unitPriceGross'           => '791.1',
                    'lineItem_1_grossAmount'              => '791.1',
                    'lineItem_1_vatAmount'                => '126.31',
                    'lineItem_1_vatRate'                  => '19',
                    'customer_id'                         => '73093235d848e2002781e977c7e3496c',
                    'customer_title'                      => 'MR',
                    'customer_gender'                     => 'MALE',
                    'customer_firstName'                  => 'John',
                    'customer_lastName'                   => 'Doe',
                    'customer_phoneNumber'                => '',
                    'customer_mobileNumber'               => '+49654546654',
                    'customer_email'                      => 'john.doe@example.com',
                    'customer_dateOfBirth'                => '1980-02-19',
                    'customer_billingAddress_street'      => 'Some st.',
                    'customer_billingAddress_houseNo'     => '7',
                    'customer_billingAddress_postalCode'  => '12345',
                    'customer_billingAddress_city'        => 'City One',
                    'customer_billingAddress_country'     => 'DE',
                    'customer_shippingAddress_street'     => 'Other st.',
                    'customer_shippingAddress_houseNo'    => '12-45',
                    'customer_shippingAddress_city'       => 'Town X',
                    'customer_shippingAddress_postalCode' => '12345',
                    'customer_shippingAddress_country'    => 'DE',
                    'customer_shippingAddress_firstName'  => 'Joe',
                    'customer_shippingAddress_lastName'   => 'Doe',
                    'device_checkId'                      => '1fj1vlo4sv8vfd6vejuudunsa2',
                    'client_browser_session_id'           => '1fj1vlo4sv8vfd6vejuudunsa2',
                    'client_cookies_id'                   => '1fj1vlo4sv8vfd6vejuudunsa2',
                    'client_ipAddress'                    => '192.168.159.1',
                    'client_browser_header'               => 'IEhvc3Q6IHZtCiBVc2VyLUFnZW50OiBNb3ppbGxhLzUuMCAoV2luZG93cyBOVCA2LjI7IFdPVzY0OyBydjoyOC4wKSBHZWNrby8yMDEwMDEwMSBGaXJlZm94LzI4LjAKIEFjY2VwdDogdGV4dC9odG1sLGFwcGxpY2F0aW9uL3hodG1sK3htbCxhcHBsaWNhdGlvbi94bWw7cT0wLjksKi8qO3E9MC44CiBBY2NlcHQtTGFuZ3VhZ2U6IGVuLVVTLGVuO3E9MC41CiBBY2NlcHQtRW5jb2Rpbmc6IGd6aXAsIGRlZmxhdGUKIFJlZmVyZXI6IGh0dHA6Ly92bS9wYXltb3Jyb3cvaW5kZXgucGhwP2NsPWRldGFpbHMmY25pZD0wZjRmYjAwODA5Y2VjOWFhMDkxMGFhOWM4ZmUzNjc1MSZhbmlkPWI1NjM2OWIxZmM5ZDdiOTdmOWM1ZmMzNDNiMzQ5ZWNlJmxpc3R0eXBlPWxpc3QmJiZsYW5nPTEKIENvb2tpZTogbGFuZ3VhZ2U9MTsgc2lkPTFmajF2bG80c3Y4dmZkNnZlanV1ZHVuc2EyOyBzaWRfa2V5PW94aWQ7IHNob3dsaW5rc29uY2U9MQogQ29ubmVjdGlvbjoga2VlcC1hbGl2ZQo=',
                )
            )
        );

        $this->assertSame(
            json_encode(
                array(
                    'phone'               => '',
                    'mobile'              => '+49654546654',
                    'session_id'          => '1fj1vlo4sv8vfd6vejuudunsa2',
                    'cookie_id'           => '1fj1vlo4sv8vfd6vejuudunsa2',
                    'firstName'           => 'John',
                    'lastName'            => 'Doe',
                    'dob'                 => '1980-02-19',
                    'gender'              => 'MALE',
                    'email'               => 'john.doe@example.com',
                    'street'              => 'Some st.',
                    'houseNumber'         => '7',
                    'locality'            => 'City One',
                    'postalCode'          => '12345',
                    'country'             => 'DE',
                    'shippingStreet'      => 'Other st.',
                    'shippingHouseNumber' => '12-45',
                    'shippingLocality'    => 'Town X',
                    'shippingPostalCode'  => '12345',
                    'shippingCountry'     => 'DE',
                    'shippingFirstName'   => 'Joe',
                    'shippingLastName'    => 'Doe',
                    'orderAmount'         => '791.1',
                    'langcode'            => 'en',
                    'client_ip'           => '192.168.159.1',
                    'currencyCode'        => 'EUR',
                )
            ),
            $SUT->printPmData()
        );
    }

    public function testPrintPmData_sessionInitDataSet_returnJsonArrayWithSessionDataAdded()
    {
        $this->setSessionParam(
            'pm_init_data',
            array(
                'paymentMethod_name'        => 'INVOICE',
                'paymentMethod_status'      => 'DECLINED',
                'paymentMethod_declineType' => 'DONT_COMUNICATE'
            )
        );

        $SUT = $this->getMock( 'OxpsPaymorrowEshopDataProvider', array('collectEshopData') );
        $SUT->expects( $this->once() )->method( 'collectEshopData' )->will( $this->returnValue( array() ) );

        $this->assertSame(
            json_encode(
                array(
                    'phone'                     => '',
                    'mobile'                    => '',
                    'session_id'                => '',
                    'cookie_id'                 => '',
                    'firstName'                 => '',
                    'lastName'                  => '',
                    'dob'                       => '',
                    'gender'                    => '',
                    'email'                     => '',
                    'street'                    => '',
                    'houseNumber'               => '',
                    'locality'                  => '',
                    'postalCode'                => '',
                    'country'                   => '',
                    'shippingStreet'            => '',
                    'shippingHouseNumber'       => '',
                    'shippingLocality'          => '',
                    'shippingPostalCode'        => '',
                    'shippingCountry'           => '',
                    'shippingFirstName'         => '',
                    'shippingLastName'          => '',
                    'orderAmount'               => '',
                    'langcode'                  => '',
                    'client_ip'                 => '',
                    'currencyCode'              => '',
                    'paymentMethod_name'        => 'INVOICE',
                    'paymentMethod_status'      => 'DECLINED',
                    'paymentMethod_declineType' => 'DONT_COMUNICATE',
                )
            ),
            $SUT->printPmData()
        );
    }


    /**
     * Create and return user mock object.
     *
     * @return PHPUnit_Framework_MockObject_MockObject|OxpsPaymorrowOxUser|oxUser
     */
    protected function _getUserMock( $blExists = true )
    {
        $oUserMock = $this->getMock(
            'OxpsPaymorrowOxUser',
            array(
                '__construct', 'load', 'getUserPaymorrowRequestLanguage', 'getSelectedAddress', 'exists',
                'getUserPaymorrowUserBasketFromSession', 'getId', 'getUserPaymorrowSalutation',
                'getCustomerPaymorrowCustomerNumber', 'getUserPaymorrowGender',
                'getUserPaymorrowFirstName', 'getUserPaymorrowLastName', 'getUserPaymorrowPhone',
                'getUserPaymorrowMobilePhone', 'getUserPaymorrowEmail', 'getUserPaymorrowDateOfBirth',
                'getUserPaymorrowStreet', 'getUserPaymorrowStreetNumber', 'getUserPaymorrowZipCode',
                'getUserPaymorrowCity', 'getUserPaymorrowCountry', 'getUserPaymorrowIpAddress', 'getUserGroupNames',
                'getOrders'
            )
        );

        $oUserMock->expects( $this->any() )->method( 'getUserPaymorrowRequestLanguage' )->will(
            $this->returnValue( 'en' )
        );
        $oUserMock->expects( $this->any() )->method( 'exists' )->will( $this->returnValue( (bool) $blExists ) );
        $oUserMock->expects( $this->any() )->method( 'getUserPaymorrowUserBasketFromSession' )->will(
            $this->returnValue( $this->_getBasketMock() )
        );
        $oUserMock->expects( $this->any() )->method( 'getCustomerPaymorrowCustomerNumber' )->will(
            $this->returnValue( 'Usr_ID' )
        );
        $oUserMock->expects( $this->any() )->method( 'getUserPaymorrowStreet' )->will(
            $this->returnValue( 'My Street st.' )
        );
        $oUserMock->expects( $this->any() )->method( 'getUserPaymorrowIpAddress' )->will(
            $this->returnValue( '127.0.0.007' )
        );
        $oUserMock->expects( $this->any() )->method( 'getUserPaymorrowSalutation' )->will( $this->returnValue( 'Mr' ) );
        $oUserMock->expects( $this->any() )->method( 'getUserPaymorrowGender' )->will( $this->returnValue( 'MALE' ) );
        $oUserMock->expects( $this->any() )->method( 'getUserPaymorrowFirstName' )->will(
            $this->returnValue( 'Example' )
        );
        $oUserMock->expects( $this->any() )->method( 'getUserPaymorrowLastName' )->will(
            $this->returnValue( 'Smith' )
        );
        $oUserMock->expects( $this->any() )->method( 'getUserPaymorrowPhone' )->will(
            $this->returnValue( '+001234567890' )
        );
        $oUserMock->expects( $this->any() )->method( 'getUserPaymorrowMobilePhone' )->will(
            $this->returnValue( '+001234567891' )
        );
        $oUserMock->expects( $this->any() )->method( 'getUserPaymorrowEmail' )->will(
            $this->returnValue( 'smith@example.com' )
        );
        $oUserMock->expects( $this->any() )->method( 'getUserPaymorrowDateOfBirth' )->will(
            $this->returnValue( '1922-02-22' )
        );
        $oUserMock->expects( $this->any() )->method( 'getUserPaymorrowStreetNumber' )->will(
            $this->returnValue( '48' )
        );
        $oUserMock->expects( $this->any() )->method( 'getUserPaymorrowZipCode' )->will( $this->returnValue( '12345' ) );
        $oUserMock->expects( $this->any() )->method( 'getUserPaymorrowCity' )->will( $this->returnValue( 'Freiburg' ) );
        $oUserMock->expects( $this->any() )->method( 'getUserPaymorrowCountry' )->will( $this->returnValue( 'DE' ) );

        $oAddressMock                         = $this->getMock( 'oxAddress', array('__construct', 'load') );
        $oAddressMock->oxaddress__oxstreet    = new oxField( 'Other st.' );
        $oAddressMock->oxaddress__oxstreetnr  = new oxField( '7' );
        $oAddressMock->oxaddress__oxcity      = new oxField( 'Town' );
        $oAddressMock->oxaddress__oxzip       = new oxField( '98765' );
        $oAddressMock->oxaddress__oxcountryid = new oxField( 'OXID_DE' );
        $oAddressMock->oxaddress__oxaddinfo   = new oxField( 'NFO' );
        $oAddressMock->oxaddress__oxfname     = new oxField( 'Mister' );
        $oAddressMock->oxaddress__oxlname     = new oxField( 'Burmister' );
        $oAddressMock->oxaddress__oxcompany   = new oxField( 'My incorporated' );

        $oUserMock->expects( $this->any() )->method( 'getSelectedAddress' )->will(
            $this->returnValue( $oAddressMock )
        );

        $oUserMock->expects( $this->any() )->method( 'getUserGroupNames' )->will(
            $this->returnValue( 'My Group, Another Group' )
        );

        $oUserMock->oxuser__oxcompany = new oxField( 'X Inc.' );

        return $oUserMock;
    }

    /**
     * Create and return basket mock object.
     *
     * @return PHPUnit_Framework_MockObject_MockObject|OxpsPaymorrowOxBasket|oxBasket
     */
    protected function _getBasketMock()
    {
        $oBasketMock = $this->getMock(
            'OxpsPaymorrowOxBasket',
            array(
                '__construct', 'getPaymorrowTotalAmount', 'getPaymorrowTotalVatAmount', 'getPaymorrowBasketCurrency',
                'getPaymorrowBasketLineItems'
            )
        );

        $oBasketMock->expects( $this->any() )->method( 'getPaymorrowTotalAmount' )->will(
            $this->returnValue( 777.88 )
        );
        $oBasketMock->expects( $this->any() )->method( 'getPaymorrowTotalVatAmount' )->will(
            $this->returnValue( 88.77 )
        );
        $oBasketMock->expects( $this->any() )->method( 'getPaymorrowBasketCurrency' )->will(
            $this->returnValue( 'EUR' )
        );
        $oBasketMock->expects( $this->any() )->method( 'getPaymorrowBasketLineItems' )->will(
            $this->returnValue( array('pm_item_1_data' => 'data', 'pm_item_2_number' => 1) )
        );

        return $oBasketMock;
    }
}
