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
 * Class Unit_Module_Core_OxpsPaymorrowRequestControllerProxyTest
 *
 * @see OxpsPaymorrowRequestControllerProxy
 */
class Unit_Module_Core_OxpsPaymorrowRequestControllerProxyTest extends OxidTestCase
{

    public function testPrepareOrder_callUserUpdateAndPaymentMethodResetThenMakeOrderVerifyCallAndReturnResponseJson()
    {
        $aData = array('key' => 'val');

        // Request controller mock
        $oRequestControllerMock = $this->getMock( 'RequestController', array('pmVerify') );
        $oRequestControllerMock->expects( $this->once() )->method( 'pmVerify' )->with( $this->equalTo( $aData ) )->will(
            $this->returnValue( array('response' => 'OK') )
        );

        // Logger mock
        $oLogMock = $this->getMock( 'OxpsPaymorrowLogger', array('__construct', 'logWithType') );
        $oLogMock->expects( $this->once() )->method( 'logWithType' )->with(
            $this->equalTo( $aData ),
            $this->equalTo( 'Proxy-prepareOrderPOST' )
        );
        oxRegistry::set( 'OxpsPaymorrowLogger', $oLogMock );

        // Response handler mock
        $oHandlerMock = $this->getMock(
            'OxpsPaymorrowResponseHandler',
            array('__construct', '__call', 'getErrorCodeFromResponseData')
        );
        $oHandlerMock->expects( $this->once() )->method( 'getErrorCodeFromResponseData' )
            ->with( $this->equalTo( array('response' => 'OK') ) )
            ->will( $this->returnValue( null ) );
        oxRegistry::set( 'OxpsPaymorrowResponseHandler', $oHandlerMock );

        // SUT mock
        $SUT = $this->getMock(
            'OxpsPaymorrowRequestControllerProxy',
            array('__construct', '__call', '_updateUserData', '_resetPaymentMethod', '_getRequestController')
        );
        $SUT->expects( $this->once() )->method( '_updateUserData' )->with( $this->equalTo( $aData ) );
        $SUT->expects( $this->once() )->method( '_resetPaymentMethod' )->with( $this->equalTo( $aData ) );
        $SUT->expects( $this->once() )->method( '_getRequestController' )->will(
            $this->returnValue( $oRequestControllerMock )
        );

        $this->assertSame( '{"response":"OK"}', $SUT->prepareOrder( $aData ) );
    }

    public function testPrepareOrder_responseErrorCode900AndSecondArgumentTrue_justReturnResponse()
    {
        $aData = array('key' => 'val', 'field' => 'value');

        // Request controller mock
        $oRequestControllerMock = $this->getMock( 'RequestController', array('pmVerify') );
        $oRequestControllerMock->expects( $this->once() )->method( 'pmVerify' )->with( $this->equalTo( $aData ) )->will(
            $this->returnValue( array('response' => 'ERROR', 'code' => 900) )
        );

        // Logger mock
        $oLogMock = $this->getMock( 'OxpsPaymorrowLogger', array('__construct', 'logWithType') );
        $oLogMock->expects( $this->once() )->method( 'logWithType' )->with(
            $this->equalTo( $aData ),
            $this->equalTo( 'Proxy-prepareOrderPOST' )
        );
        oxRegistry::set( 'OxpsPaymorrowLogger', $oLogMock );

        // Response handler mock
        $oHandlerMock = $this->getMock(
            'OxpsPaymorrowResponseHandler',
            array('__construct', '__call', 'getErrorCodeFromResponseData')
        );
        $oHandlerMock->expects( $this->once() )->method( 'getErrorCodeFromResponseData' )
            ->with( $this->equalTo( array('response' => 'ERROR', 'code' => 900) ) )
            ->will( $this->returnValue( 900 ) );
        oxRegistry::set( 'OxpsPaymorrowResponseHandler', $oHandlerMock );

        // Module mock
        $oModuleMock = $this->getMock( 'OxpsPaymorrowModule', array('__construct', 'updateSettings') );
        $oModuleMock->expects( $this->never() )->method( 'updateSettings' );
        oxRegistry::set( 'OxpsPaymorrowModule', $oModuleMock );

        // SUT mock
        $SUT = $this->getMock(
            'OxpsPaymorrowRequestControllerProxy',
            array('__construct', '__call', '_updateUserData', '_resetPaymentMethod', '_getRequestController')
        );
        $SUT->expects( $this->once() )->method( '_updateUserData' )->with( $this->equalTo( $aData ) );
        $SUT->expects( $this->once() )->method( '_resetPaymentMethod' )->with( $this->equalTo( $aData ) );
        $SUT->expects( $this->once() )->method( '_getRequestController' )->will(
            $this->returnValue( $oRequestControllerMock )
        );

        $this->assertSame( '{"response":"ERROR","code":900}', $SUT->prepareOrder( $aData, true ) );
    }


    public function testPrepareOrder_responseErrorCode900_triggerSettingsUpdateAndCallSelfRecursively()
    {
        $aData = array('key' => 'val', 'field' => 'value');

        // Request controller mock
        $oRequestControllerMock = $this->getMock( 'RequestController', array('pmVerify') );
        $oRequestControllerMock->expects( $this->exactly( 2 ) )->method( 'pmVerify' )
            ->with( $this->equalTo( $aData ) )
            ->will( $this->returnValue( array('response' => 'ERROR', 'code' => 900) ) );

        // Logger mock
        $oLogMock = $this->getMock( 'OxpsPaymorrowLogger', array('__construct', 'logWithType') );
        $oLogMock->expects( $this->exactly( 2 ) )->method( 'logWithType' )->with(
            $this->equalTo( $aData ),
            $this->equalTo( 'Proxy-prepareOrderPOST' )
        );
        oxRegistry::set( 'OxpsPaymorrowLogger', $oLogMock );

        // Response handler mock
        $oHandlerMock = $this->getMock(
            'OxpsPaymorrowResponseHandler',
            array('__construct', '__call', 'getErrorCodeFromResponseData')
        );
        $oHandlerMock->expects( $this->exactly( 2 ) )->method( 'getErrorCodeFromResponseData' )
            ->with( $this->equalTo( array('response' => 'ERROR', 'code' => 900) ) )
            ->will( $this->returnValue( 900 ) );
        oxRegistry::set( 'OxpsPaymorrowResponseHandler', $oHandlerMock );

        // Module mock
        $oModuleMock = $this->getMock( 'OxpsPaymorrowModule', array('__construct', 'updateSettings') );
        $oModuleMock->expects( $this->once() )->method( 'updateSettings' );
        oxRegistry::set( 'OxpsPaymorrowModule', $oModuleMock );

        // SUT mock
        $SUT = $this->getMock(
            'OxpsPaymorrowRequestControllerProxy',
            array('__construct', '__call', '_updateUserData', '_resetPaymentMethod', '_getRequestController')
        );
        $SUT->expects( $this->exactly( 2 ) )->method( '_updateUserData' )->with( $this->equalTo( $aData ) );
        $SUT->expects( $this->exactly( 2 ) )->method( '_resetPaymentMethod' )->with( $this->equalTo( $aData ) );
        $SUT->expects( $this->exactly( 2 ) )->method( '_getRequestController' )->will(
            $this->returnValue( $oRequestControllerMock )
        );

        $this->assertSame( '{"response":"ERROR","code":900}', $SUT->prepareOrder( $aData ) );
    }


    public function testValidatePendingOrder()
    {
        // Data provider mock
        $oDataProviderMock = $this->getMock( 'OxpsPaymorrowEshopDataProvider', array('collectEshopData') );
        $oDataProviderMock->expects( $this->once() )->method( 'collectEshopData' )
            ->will( $this->returnValue( array('merchantId' => 'test', 'request_id' => '123') ) );
        oxTestModules::addModuleObject( 'OxpsPaymorrowEshopDataProvider', $oDataProviderMock );

        // Session data mock
        $this->setSessionParam( 'pm_verify', array('pm_paymentMethod_name' => 'INVOICE') );

        // Request controller mock
        $aRequestData = array(
            'merchantId' => 'test',
            'request_id' => '123',
            'pm_paymentMethod_name' => 'INVOICE'
        );
        $oRequestControllerMock = $this->getMock( 'RequestController', array('pmVerify') );
        $oRequestControllerMock->expects( $this->once() )->method( 'pmVerify' )
            ->with( $this->equalTo( $aRequestData ) )
            ->will( $this->returnValue( array('pm_order_status' => 'ACCEPTED') ) );

        // Logger mock
        $oLogMock = $this->getMock( 'OxpsPaymorrowLogger', array('__construct', 'logWithType') );
        $oLogMock->expects( $this->once() )->method( 'logWithType' )->with(
            $this->equalTo( $aRequestData ),
            $this->equalTo( 'Proxy-prepareOrderPOST_reValidate' )
        );
        oxRegistry::set( 'OxpsPaymorrowLogger', $oLogMock );

        // Response handler mock
        $oResponse = $this->getMock( 'OxpsPaymorrowResponseHandler', array('__call', 'setResponse', 'wasAccepted') );
        $oResponse->expects( $this->once() )->method( 'setResponse' )
            ->with($this->equalTo( array('pm_order_status' => 'ACCEPTED') ));
        $oResponse->expects( $this->once() )->method( 'wasAccepted' )->will( $this->returnValue( true ) );
        oxRegistry::set( 'OxpsPaymorrowResponseHandler', $oResponse );

        // SUT mock
        /** @var OxpsPaymorrowRequestControllerProxy|PHPUnit_Framework_MockObject_MockObject $SUT */
        $SUT = $this->getMock(
            'OxpsPaymorrowRequestControllerProxy',
            array('__construct', '__call', '_getRequestController')
        );
        $SUT->expects( $this->once() )->method( '_getRequestController' )->will(
            $this->returnValue( $oRequestControllerMock )
        );

        $this->assertTrue( $SUT->validatePendingOrder() );
    }


    public function testGetBuiltRequestController_returnBuiltRequestControllerWithGatewaySet()
    {
        $SUT = $this->getProxyClass( 'OxpsPaymorrowRequestControllerProxy' );

        $oGatewayMock = $this->getMock( 'OxpsPaymorrowGateway' );

        $oOxid2PaymorrowMock = $this->getMock( 'OxpsOxid2Paymorrow', array('getBuiltPaymorrowRequestController') );
        $oOxid2PaymorrowMock->expects( $this->once() )->method( 'getBuiltPaymorrowRequestController' )->will(
            $this->returnValue( $oGatewayMock )
        );

        oxTestModules::addModuleObject( 'OxpsOxid2Paymorrow', $oOxid2PaymorrowMock );

        $this->assertSame( $oGatewayMock, $SUT->_getRequestController() );
    }


    public function testUpdateUserData_noUser_noUpdatesAreTriggered()
    {
        // User mock
        $oUserMock = $this->getMock(
            'OxpsPaymorrowOxUser',
            array(
                '__construct', 'load', 'getId', 'getSelectedAddress',
                'mapToProfileDataAndUpdateUser', 'mapShippingDataAndUpdateAddress'
            )
        );
        $oUserMock->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( '' ) );
        $oUserMock->expects( $this->never() )->method( 'getSelectedAddress' );
        $oUserMock->expects( $this->never() )->method( 'mapToProfileDataAndUpdateUser' );
        $oUserMock->expects( $this->never() )->method( 'mapShippingDataAndUpdateAddress' );

        // SUT mock
        $SUT = $this->getMock( 'OxpsPaymorrowRequestControllerProxy', array('__call', 'getUser') );
        $SUT->expects( $this->once() )->method( 'getUser' )->will( $this->returnValue( $oUserMock ) );

        $SUT->_updateUserData( array('pm_customer_firstName' => 'John') );
    }

    public function testUpdateUserData_userIsValid_callUserProfileUpdate()
    {
        $aData = array('pm_customer_firstName' => 'John');

        // User mock
        $oUserMock = $this->getMock(
            'OxpsPaymorrowOxUser',
            array(
                '__construct', 'load', 'getId', 'getSelectedAddress',
                'mapToProfileDataAndUpdateUser', 'mapShippingDataAndUpdateAddress'
            )
        );
        $oUserMock->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'Usr_ID' ) );
        $oUserMock->expects( $this->once() )->method( 'getSelectedAddress' )->will( $this->returnValue( null ) );
        $oUserMock->expects( $this->once() )->method( 'mapToProfileDataAndUpdateUser' )->with(
            $this->equalTo( $aData )
        );
        $oUserMock->expects( $this->never() )->method( 'mapShippingDataAndUpdateAddress' );

        // SUT mock
        $SUT = $this->getMock( 'OxpsPaymorrowRequestControllerProxy', array('__call', 'getUser') );
        $SUT->expects( $this->once() )->method( 'getUser' )->will( $this->returnValue( $oUserMock ) );

        $SUT->_updateUserData( $aData );
    }

    public function testUpdateUserData_billingAddressUsedAsShipping_onlyCallUserProfileUpdate()
    {
        $this->setSessionParam( 'blshowshipaddress', '' );

        $aData = array('pm_customer_firstName' => 'John');

        // Address mock
        $oAddress = $this->getMock( 'oxAddress', array('__construct', 'load', 'getId') );

        // User mock
        $oUserMock = $this->getMock(
            'OxpsPaymorrowOxUser',
            array(
                '__construct', 'load', 'getId', 'getSelectedAddress',
                'mapToProfileDataAndUpdateUser', 'mapShippingDataAndUpdateAddress'
            )
        );
        $oUserMock->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'Usr_ID' ) );
        $oUserMock->expects( $this->once() )->method( 'getSelectedAddress' )->will( $this->returnValue( $oAddress ) );
        $oUserMock->expects( $this->once() )->method( 'mapToProfileDataAndUpdateUser' )->with(
            $this->equalTo( $aData )
        );
        $oUserMock->expects( $this->never() )->method( 'mapShippingDataAndUpdateAddress' );

        // SUT mock
        $SUT = $this->getMock( 'OxpsPaymorrowRequestControllerProxy', array('__call', 'getUser') );
        $SUT->expects( $this->once() )->method( 'getUser' )->will( $this->returnValue( $oUserMock ) );

        $SUT->_updateUserData( $aData );
    }

    public function testUpdateUserData_shippingAddressAvailable_callUserProfileAndShippingAddressUpdates()
    {
        $this->setSessionParam( 'blshowshipaddress', '1' );

        $aData = array('pm_customer_firstName' => 'John');

        // Address mock
        $oAddress = $this->getMock( 'oxAddress', array('__construct', 'load', 'getId') );
        $oAddress->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'Addr_ID' ) );

        // User mock
        $oUserMock = $this->getMock(
            'OxpsPaymorrowOxUser',
            array(
                '__construct', 'load', 'getId', 'getSelectedAddress',
                'mapToProfileDataAndUpdateUser', 'mapShippingDataAndUpdateAddress'
            )
        );
        $oUserMock->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'Usr_ID' ) );
        $oUserMock->expects( $this->once() )->method( 'getSelectedAddress' )->will( $this->returnValue( $oAddress ) );
        $oUserMock->expects( $this->once() )->method( 'mapToProfileDataAndUpdateUser' )->with(
            $this->equalTo( $aData )
        );
        $oUserMock->expects( $this->once() )->method( 'mapShippingDataAndUpdateAddress' )->with(
            $this->equalTo( $aData )
        );

        // SUT mock
        $SUT = $this->getMock( 'OxpsPaymorrowRequestControllerProxy', array('__call', 'getUser') );
        $SUT->expects( $this->once() )->method( 'getUser' )->will( $this->returnValue( $oUserMock ) );

        $SUT->_updateUserData( $aData );
    }


    public function testResetPaymentMethod_noPaymentMethodInSession_notUpdateBasket()
    {
        // Session mock
        $oSessionMock = $this->getMock( 'oxSession', array('deleteVariable', 'getBasket') );
        $oSessionMock->expects( $this->never() )->method( 'deleteVariable' );
        $oSessionMock->expects( $this->never() )->method( 'getBasket' );
        oxRegistry::set( 'oxSession', $oSessionMock );
        $this->setSessionParam( 'paymentid', '' );

        /** @var OxpsPaymorrowRequestControllerProxy $SUT */
        $SUT = $this->getMock( 'OxpsPaymorrowRequestControllerProxy', array('__call') );
        $SUT->_resetPaymentMethod( array('paymentid' => 'invoice') );
    }

    public function testResetPaymentMethod_noPaymentMethodInArgumentArray_notUpdateBasket()
    {
        // Session mock
        $oSessionMock = $this->getMock( 'oxSession', array('deleteVariable', 'getBasket') );
        $oSessionMock->expects( $this->never() )->method( 'deleteVariable' );
        $oSessionMock->expects( $this->never() )->method( 'getBasket' );
        oxRegistry::set( 'oxSession', $oSessionMock );
        $this->setSessionParam( 'paymentid', 'cod' );

        /** @var OxpsPaymorrowRequestControllerProxy $SUT */
        $SUT = $this->getMock( 'OxpsPaymorrowRequestControllerProxy', array('__call') );
        $SUT->_resetPaymentMethod( array('id' => 'invoice') );
    }

    public function testResetPaymentMethod_sessionAndArgumentPaymentMethodsAreTheSame_notUpdateBasket()
    {
        // Session mock
        $oSessionMock = $this->getMock( 'oxSession', array('deleteVariable', 'getBasket') );
        $oSessionMock->expects( $this->never() )->method( 'deleteVariable' );
        $oSessionMock->expects( $this->never() )->method( 'getBasket' );
        oxRegistry::set( 'oxSession', $oSessionMock );
        $this->setSessionParam( 'paymentid', 'invoice' );

        /** @var OxpsPaymorrowRequestControllerProxy $SUT */
        $SUT = $this->getMock( 'OxpsPaymorrowRequestControllerProxy', array('__call') );
        $SUT->_resetPaymentMethod( array('paymentid' => 'invoice') );
    }

    public function testResetPaymentMethod_sessionAndArgumentPaymentMethodsDiffer_removePaymentDataFromSessionAndBasketAndUpdateBasket()
    {
        // Basket mock
        /** @var oxBasket $oBasketMock */
        $oBasketMock = $this->getMock( 'oxBasket', array('__construct', 'calculateBasket') );
        $oBasketMock->setPayment( 'invoice' );
        $oBasketMock->setCost( 'oxpayment', new oxPrice( 12345.67 ) );
        $oBasketMock->expects( $this->once() )->method( 'calculateBasket' )->with( $this->equalTo( true ) );

        // Session mock
        $oSessionMock = $this->getMock( 'oxSession', array('deleteVariable', 'getBasket') );
        $oSessionMock->expects( $this->once() )->method( 'deleteVariable' )->with( $this->equalTo( 'paymentid' ) );
        $oSessionMock->expects( $this->once() )->method( 'getBasket' )->will( $this->returnValue( $oBasketMock ) );
        oxRegistry::set( 'oxSession', $oSessionMock );
        $this->setSessionParam( 'paymentid', 'invoice' );

        /** @var OxpsPaymorrowRequestControllerProxy $SUT */
        $SUT = $this->getMock( 'OxpsPaymorrowRequestControllerProxy', array('__call') );
        $SUT->_resetPaymentMethod( array('paymentid' => 'cod') );

        $this->assertNull( $oBasketMock->getCosts( 'oxpayment' ) );
    }


    public function testGetRequestController_callForOxidToPaymorrowRequestControllerAndReturnIt()
    {
        // Request controller mock
        $oRequestControllerMock = $this->getMock( 'RequestController' );

        // Oxid2Paymorrow mock
        $oOxid2PaymorrowMock = $this->getMock( 'OxpsOxid2Paymorrow', array('getBuiltPaymorrowRequestController') );
        $oOxid2PaymorrowMock->expects( $this->once() )->method( 'getBuiltPaymorrowRequestController' )->will(
            $this->returnValue( $oRequestControllerMock )
        );

        oxTestModules::addModuleObject( 'OxpsOxid2Paymorrow', $oOxid2PaymorrowMock );

        // SUT mock
        $SUT = $this->getProxyClass( 'OxpsPaymorrowRequestControllerProxy' );

        $this->assertSame( $oRequestControllerMock, $SUT->_getRequestController() );
    }
}
