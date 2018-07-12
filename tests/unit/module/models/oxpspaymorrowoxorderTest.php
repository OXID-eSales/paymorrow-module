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
 * Class Unit_Module_Models_OxpsPaymorrowOxOrderTest
 *
 * @see OxpsPaymorrowOxOrder
 */
class Unit_Module_Models_OxpsPaymorrowOxOrderTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowOxOrder|PHPUnit_Framework_MockObject_MockObject
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
        $this->SUT = $this->getMock(
            'OxpsPaymorrowOxOrder',
            array(
                '__construct', 'load', '_wasPaymorrowPaymentUsed',
                '_OxpsPaymorrowOxOrder_validatePayment_parent',
                '_OxpsPaymorrowOxOrder_finalizeOrder_parent'
            )
        );
    }


    public function test_savePaymorrowTemporaryOrderIdToSession_shouldReturnSavedValue()
    {
        $sTempOrderToSession = 'akNGJJ@*(#*(Lkawfawf';
        $this->SUT->savePaymorrowTemporaryOrderIdToSession( $sTempOrderToSession );

        $this->assertEquals( $sTempOrderToSession, $this->SUT->getPaymorrowTemporaryOrderIdFromSession() );
    }


    public function test_getPaymorrowOxUserPaymentReloaded_shouldReturnInstanceOfOxPayment()
    {
        $this->SUT->oxorder__oxpaymentid = new oxField( 'awfawfr4g' );

        $this->assertTrue( $this->SUT->getPaymorrowOxUserPaymentReloaded() instanceof oxUserPayment );
    }


    public function test_getPaymorrowOrderNumber_shouldReturnSetOrderNumber()
    {
        $iOrderNumber                  = 332;
        $this->SUT->oxorder__oxordernr = new oxField( $iOrderNumber );

        $this->assertEquals( $iOrderNumber, $this->SUT->getPaymorrowOrderNumber() );
    }

    /**
     * @throws oxSystemComponentException
     */
    public function testValidatePayment_errorCoderInParentCall_onlyReturnTheErrorCode()
    {
        $oBasketMock = new oxBasket();

        /** @var OxpsPaymorrowOxPayment|oxPayment|PHPUnit_Framework_MockObject_MockObject $oPayment */
        $oPayment = $this->getMock( 'oxPayment', array('__construct', 'load', 'isPaymorrowActiveAndMapped') );
        $oPayment->expects( $this->never() )->method( 'load' );
        $oPayment->expects( $this->never() )->method( 'isPaymorrowActiveAndMapped' );
        oxTestModules::addModuleObject( 'oxPayment', $oPayment );

        /** @var OxpsPaymorrowRequestControllerProxy|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->getMock(
            'OxpsPaymorrowRequestControllerProxy',
            array('__call', 'validatePendingOrder')
        );
        $oGateway->expects( $this->never() )->method( 'validatePendingOrder' );
        oxTestModules::addModuleObject( 'OxpsPaymorrowRequestControllerProxy', $oGateway );

        $this->SUT->expects( $this->once() )->method( '_OxpsPaymorrowOxOrder_validatePayment_parent' )
            ->with( $this->equalTo( $oBasketMock ) )
            ->will( $this->returnValue( 13 ) );

        $this->assertSame( 13, $this->SUT->validatePayment( $oBasketMock ) );
    }

    /**
     * @throws oxSystemComponentException
     */
    public function testValidatePayment_paymentMethodNotLoaded_returnParentCallResult()
    {
        $oBasketMock = new oxBasket();
        $oBasketMock->setPayment('super_invoice');

        /** @var OxpsPaymorrowOxPayment|oxPayment|PHPUnit_Framework_MockObject_MockObject $oPayment */
        $oPayment = $this->getMock( 'oxPayment', array('__construct', 'load', 'isPaymorrowActiveAndMapped') );
        $oPayment->expects( $this->once() )->method( 'load' )
            ->with( 'super_invoice' )
            ->will( $this->returnValue( false ) );
        $oPayment->expects( $this->never() )->method( 'isPaymorrowActiveAndMapped' );
        oxTestModules::addModuleObject( 'oxPayment', $oPayment );

        /** @var OxpsPaymorrowRequestControllerProxy|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->getMock(
            'OxpsPaymorrowRequestControllerProxy',
            array('__call', 'validatePendingOrder')
        );
        $oGateway->expects( $this->never() )->method( 'validatePendingOrder' );
        oxTestModules::addModuleObject( 'OxpsPaymorrowRequestControllerProxy', $oGateway );

        $this->SUT->expects( $this->once() )->method( '_OxpsPaymorrowOxOrder_validatePayment_parent' )
            ->with( $this->equalTo( $oBasketMock ) )
            ->will( $this->returnValue( null ) );

        $this->assertNull( $this->SUT->validatePayment( $oBasketMock ) );
    }

    /**
     * @throws oxSystemComponentException
     */
    public function testValidatePayment_paymentMethodIsNotOneOfPaymorrow_returnParentCallResult()
    {
        $oBasketMock = new oxBasket();
        $oBasketMock->setPayment('super_invoice');

        /** @var OxpsPaymorrowOxPayment|oxPayment|PHPUnit_Framework_MockObject_MockObject $oPayment */
        $oPayment = $this->getMock( 'oxPayment', array('__construct', 'load', 'isPaymorrowActiveAndMapped') );
        $oPayment->expects( $this->once() )->method( 'load' )
            ->with( 'super_invoice' )
            ->will( $this->returnValue( true ) );
        $oPayment->expects( $this->once() )->method( 'isPaymorrowActiveAndMapped' )
            ->will( $this->returnValue( false ) );
        oxTestModules::addModuleObject( 'oxPayment', $oPayment );

        /** @var OxpsPaymorrowRequestControllerProxy|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->getMock(
            'OxpsPaymorrowRequestControllerProxy',
            array('__call', 'validatePendingOrder')
        );
        $oGateway->expects( $this->never() )->method( 'validatePendingOrder' );
        oxTestModules::addModuleObject( 'OxpsPaymorrowRequestControllerProxy', $oGateway );

        $this->SUT->expects( $this->once() )->method( '_OxpsPaymorrowOxOrder_validatePayment_parent' )
            ->with( $this->equalTo( $oBasketMock ) )
            ->will( $this->returnValue( null ) );

        $this->assertNull( $this->SUT->validatePayment( $oBasketMock ) );
    }

    /**
     * @throws oxSystemComponentException
     */
    public function testValidatePayment_pendingPaymentIsNotValid_returnInvalidPaymentErrorCode()
    {
        $oBasketMock = new oxBasket();
        $oBasketMock->setPayment('super_invoice');

        /** @var OxpsPaymorrowOxPayment|oxPayment|PHPUnit_Framework_MockObject_MockObject $oPayment */
        $oPayment = $this->getMock( 'oxPayment', array('__construct', 'load', 'isPaymorrowActiveAndMapped') );
        $oPayment->expects( $this->once() )->method( 'load' )
            ->with( 'super_invoice' )
            ->will( $this->returnValue( true ) );
        $oPayment->expects( $this->once() )->method( 'isPaymorrowActiveAndMapped' )
            ->will( $this->returnValue( true ) );
        oxTestModules::addModuleObject( 'oxPayment', $oPayment );

        /** @var OxpsPaymorrowRequestControllerProxy|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->getMock(
            'OxpsPaymorrowRequestControllerProxy',
            array('__call', 'validatePendingOrder')
        );
        $oGateway->expects( $this->once() )->method( 'validatePendingOrder' )
            ->will( $this->returnValue( false ) );
        oxTestModules::addModuleObject( 'OxpsPaymorrowRequestControllerProxy', $oGateway );

        $this->SUT->expects( $this->once() )->method( '_OxpsPaymorrowOxOrder_validatePayment_parent' )
            ->with( $this->equalTo( $oBasketMock ) )
            ->will( $this->returnValue( null ) );

        $this->assertSame( 5, $this->SUT->validatePayment( $oBasketMock ) );
    }

    /**
     * @throws oxSystemComponentException
     */
    public function testValidatePayment_pendingPaymentValid_returnParentCallResult()
    {
        $oBasketMock = new oxBasket();
        $oBasketMock->setPayment('super_invoice');

        /** @var OxpsPaymorrowOxPayment|oxPayment|PHPUnit_Framework_MockObject_MockObject $oPayment */
        $oPayment = $this->getMock( 'oxPayment', array('__construct', 'load', 'isPaymorrowActiveAndMapped') );
        $oPayment->expects( $this->once() )->method( 'load' )
            ->with( 'super_invoice' )
            ->will( $this->returnValue( true ) );
        $oPayment->expects( $this->once() )->method( 'isPaymorrowActiveAndMapped' )
            ->will( $this->returnValue( true ) );
        oxTestModules::addModuleObject( 'oxPayment', $oPayment );

        /** @var OxpsPaymorrowRequestControllerProxy|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->getMock(
            'OxpsPaymorrowRequestControllerProxy',
            array('__call', 'validatePendingOrder')
        );
        $oGateway->expects( $this->once() )->method( 'validatePendingOrder' )
            ->will( $this->returnValue( true ) );
        oxTestModules::addModuleObject( 'OxpsPaymorrowRequestControllerProxy', $oGateway );

        $this->SUT->expects( $this->once() )->method( '_OxpsPaymorrowOxOrder_validatePayment_parent' )
            ->with( $this->equalTo( $oBasketMock ) )
            ->will( $this->returnValue( null ) );

        $this->assertNull( $this->SUT->validatePayment( $oBasketMock ) );
    }


    public function testFinalizeOrder_orderStatusNotOk_justReturnParentCallResult()
    {
        $oBasketMock = new oxBasket();
        $oUserMock   = $this->getMock( 'oxUser', array('__construct') );

        $this->SUT->oxorder__oxtransstatus = new oxField( 'ERROR' );
        $this->SUT->expects( $this->once() )->method( '_OxpsPaymorrowOxOrder_finalizeOrder_parent' )
            ->with( $this->equalTo( $oBasketMock ), $this->equalTo( $oUserMock ), $this->equalTo( false ) )
            ->will( $this->returnValue( 777 ) );
        $this->SUT->expects( $this->never() )->method( '_wasPaymorrowPaymentUsed' );

        $this->assertSame( 777, $this->SUT->finalizeOrder( $oBasketMock, $oUserMock ) );
    }

    public function testFinalizeOrder_orderStatusOkButWasPayedNotWithPaymorrow_justReturnParentCallResult()
    {
        $oBasketMock = new oxBasket();
        $oUserMock   = $this->getMock( 'oxUser', array('__construct') );

        $this->SUT->oxorder__oxtransstatus = new oxField( 'OK' );
        $this->SUT->expects( $this->once() )->method( '_OxpsPaymorrowOxOrder_finalizeOrder_parent' )
            ->with( $this->equalTo( $oBasketMock ), $this->equalTo( $oUserMock ), $this->equalTo( false ) )
            ->will( $this->returnValue( 777 ) );
        $this->SUT->expects( $this->once() )->method( '_wasPaymorrowPaymentUsed' )
            ->with( $this->equalTo( $oBasketMock ) )
            ->will( $this->returnValue( false ) );

        $this->assertSame( 777, $this->SUT->finalizeOrder( $oBasketMock, $oUserMock ) );
    }

    public function testFinalizeOrder_orderStatusIsOkAndPayedNotWithPaymorrow_collectOrderIdUpdateDataLogItExecuteCallAndReturnParent()
    {
        $oBasketMock = new oxBasket();
        $oUserMock   = $this->getMock( 'oxUser', array('__construct') );

        $this->SUT->oxorder__oxtransstatus = new oxField( 'OK' );
        $this->SUT->oxorder__oxordernr     = new oxField( '001' );
        $this->SUT->setId( 'some_hash_abc001' );
        $this->SUT->expects( $this->once() )->method( '_OxpsPaymorrowOxOrder_finalizeOrder_parent' )
            ->with( $this->equalTo( $oBasketMock ), $this->equalTo( $oUserMock ), $this->equalTo( true ) )
            ->will( $this->returnValue( 1 ) );
        $this->SUT->expects( $this->once() )->method( '_wasPaymorrowPaymentUsed' )
            ->with( $this->equalTo( $oBasketMock ) )
            ->will( $this->returnValue( true ) );

        $this->setSessionParam('pm_verify', array('pm_paymentMethod_name' => 'SDD') );
        $this->setSessionParam('pm_order_transaction_idSDD', 'tra-MP-key-001' );

        $aExpectedData = array(
            'pm_order_transaction_id' => 'tra-MP-key-001',
            'order_id'                => 'some_hash_abc001',
            'new_order_id'            => '001'
        );

        // Logger mock
        $oLoggerMock = $this->getMock( 'OxpsPaymorrowLogger', array('__construct', 'logWithType') );
        $oLoggerMock->expects( $this->once() )->method( 'logWithType' )->with(
            $this->equalTo( $aExpectedData ), $this->equalTo( 'finalizeOrder' )
        );
        oxRegistry::set( 'OxpsPaymorrowLogger', $oLoggerMock );

        // Requests controller proxy mock
        $oGatewayProxyMock = $this->getMock( 'OxpsPaymorrowRequestControllerProxy', array('updateOrderId') );
        $oGatewayProxyMock->expects( $this->once() )->method( 'updateOrderId' )->with(
            $this->equalTo( $aExpectedData )
        );
        oxTestModules::addModuleObject( 'OxpsPaymorrowRequestControllerProxy', $oGatewayProxyMock );

        $this->assertSame( 1, $this->SUT->finalizeOrder( $oBasketMock, $oUserMock, true ) );
    }


    public function testWasPaymorrowPaymentUsed_noPaymentLoadByBasketPaymentId_returnFalse()
    {
        // SUT mock
        $SUT = $this->getProxyClass( 'OxpsPaymorrowOxOrder' );

        // Basket mock
        $oBasketMock = $this->getMock( 'oxBasket', array('getPaymentId') );

        // Payment mock
        $oPaymentMock = $this->getMock(
            'OxpsPaymorrowOxPayment', array('__construct', 'load', 'isPaymorrowActiveAndMapped')
        );
        $oPaymentMock->expects( $this->once() )->method( 'load' )->with( $this->equalTo( null ) )->will(
            $this->returnValue( false )
        );
        $oPaymentMock->expects( $this->never() )->method( 'isPaymorrowActiveAndMapped' );

        oxTestModules::addModuleObject( 'OxpsPaymorrowOxPayment', $oPaymentMock );

        $this->assertFalse( $SUT->_wasPaymorrowPaymentUsed( $oBasketMock ) );
    }

    public function testWasPaymorrowPaymentUsed_paymentMethodUsedWasNotPaymorrow_returnFalse()
    {
        // SUT mock
        $SUT = $this->getProxyClass( 'OxpsPaymorrowOxOrder' );

        // Basket mock
        $oBasketMock = $this->getMock( 'oxBasket', array('getPaymentId') );
        $oBasketMock->expects( $this->once() )->method( 'getPaymentId' )->will( $this->returnValue( 'invoice' ) );

        // Payment mock
        $oPaymentMock = $this->getMock(
            'OxpsPaymorrowOxPayment', array('__construct', 'load', 'isPaymorrowActiveAndMapped')
        );
        $oPaymentMock->expects( $this->once() )->method( 'load' )->with( $this->equalTo( 'invoice' ) )->will(
            $this->returnValue( true )
        );
        $oPaymentMock->expects( $this->once() )->method( 'isPaymorrowActiveAndMapped' )->will(
            $this->returnValue( false )
        );

        oxTestModules::addModuleObject( 'OxpsPaymorrowOxPayment', $oPaymentMock );

        $this->assertFalse( $SUT->_wasPaymorrowPaymentUsed( $oBasketMock ) );
    }

    public function testWasPaymorrowPaymentUsed_paymentMethodUsedWasPaymorrow_returnTrue()
    {
        // SUT mock
        $SUT = $this->getProxyClass( 'OxpsPaymorrowOxOrder' );

        // Basket mock
        $oBasketMock = $this->getMock( 'oxBasket', array('getPaymentId') );
        $oBasketMock->expects( $this->once() )->method( 'getPaymentId' )->will( $this->returnValue( 'dds' ) );

        // Payment mock
        $oPaymentMock = $this->getMock(
            'OxpsPaymorrowOxPayment', array('__construct', 'load', 'isPaymorrowActiveAndMapped')
        );
        $oPaymentMock->expects( $this->once() )->method( 'load' )->with( $this->equalTo( 'dds' ) )->will(
            $this->returnValue( true )
        );
        $oPaymentMock->expects( $this->once() )->method( 'isPaymorrowActiveAndMapped' )->will(
            $this->returnValue( true )
        );

        oxTestModules::addModuleObject( 'OxpsPaymorrowOxPayment', $oPaymentMock );

        $this->assertTrue( $SUT->_wasPaymorrowPaymentUsed( $oBasketMock ) );
    }


    public function testGetOrderDate_nothingSet_returnEmptyString()
    {
        $this->assertSame( '', $this->SUT->getOrderDate() );
    }

    public function testGetOrderDate_orderDateIsSet_returnTheSetValue()
    {
        $this->SUT->oxorder__oxorderdate = new oxField( '2014-06-09 06:09:14' );

        $this->assertSame( '2014-06-09', $this->SUT->getOrderDate() );
    }


    public function testGetPaymentMethodIde_nothingSet_returnEmptyString()
    {
        $this->assertSame( '', $this->SUT->getPaymentMethodId() );
    }

    public function testGetPaymentMethodId_paymentIdIsSet_returnTheSetValue()
    {
        $this->SUT->oxorder__oxpaymenttype = new oxField( 'oxinvoice' );

        $this->assertSame( 'oxinvoice', $this->SUT->getPaymentMethodId() );
    }


    public function testGetPaymentMethodName_noPaymentMethodLoaded_returnEmptyString()
    {
        // Payment mock
        $oPaymentMock = $this->getMock( 'oxPayment', array('__construct', 'load', 'getTitle') );
        $oPaymentMock->expects( $this->once() )->method( 'load' )->with( '' )->will( $this->returnValue( false ) );
        $oPaymentMock->expects( $this->never() )->method( 'getTitle' );

        oxTestModules::addModuleObject( 'oxPayment', $oPaymentMock );

        $this->assertSame( '', $this->SUT->getPaymentMethodName() );
    }

    public function testGetPaymentMethodName_paymentMethodLoaded_returnItsTitle()
    {
        $this->SUT->oxorder__oxpaymenttype = new oxField( 'oxinvoice' );

        // Payment mock
        $oPaymentMock = $this->getMock( 'oxPayment', array('__construct', 'load', 'getTitle') );
        $oPaymentMock->expects( $this->once() )->method( 'load' )->with( 'oxinvoice' )->will(
            $this->returnValue( true )
        );
        $oPaymentMock->expects( $this->once() )->method( 'getTitle' )->will( $this->returnValue( 'Invoice' ) );

        oxTestModules::addModuleObject( 'oxPayment', $oPaymentMock );

        $this->assertSame( 'Invoice', $this->SUT->getPaymentMethodName() );
    }


    public function testGetPaymorrowOrderStatus_noStatusSet_returnEmptyString()
    {
        $this->assertSame( '', $this->SUT->getOrderStatus() );
    }

    public function testGetPaymorrowOrderStatus_statusSet_returnTheSetValue()
    {
        $this->SUT->oxorder__oxtransstatus = new oxField( 'OK' );

        $this->assertSame( 'OK', $this->SUT->getOrderStatus() );
    }
}
