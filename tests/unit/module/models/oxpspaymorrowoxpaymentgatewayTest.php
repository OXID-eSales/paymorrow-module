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
 * Class Unit_Module_Models_OxpsPaymorrowOxPaymentGatewayTest
 *
 * @see OxpsPaymorrowOxPaymentGateway
 */
class Unit_Module_Models_OxpsPaymorrowOxPaymentGatewayTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowOxPaymentGateway|oxPaymentGateway
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
            'OxpsPaymorrowOxPaymentGateway',
            array('_setSessionInitData', '_OxpsPaymorrowOxPaymentGateway_executePayment_parent')
        );
    }


    /**
     * Mocking Proxy Class to test Protected methods
     *
     * IDE - might underline methods with RED color, PLEASE IGNORE
     *
     * @param array $aParams
     *
     * @return OxpsPaymorrowOxPaymentGateway|oxUserPayment
     */
    protected function _getProxySUT( array $aParams = array() )
    {
        return $this->getProxyClass( 'OxpsPaymorrowOxPaymentGateway', $aParams );
    }


    /**
     * @param array $aMethods
     *
     * @return oxOrder|OxpsPaymorrowOxOrder
     */
    protected function _getOrderMock( array $aMethods = array() )
    {
        return $this->getMock(
            'OxpsPaymorrowOxOrder',
            array_merge( array('load', 'init', '__construct', 'savePaymorrowTemporaryOrderIdToSession'), $aMethods )
        );
    }


    public function test_executePayment_shouldCallParentWhenUserPaymentIsNotPaymorrow()
    {
        $oOrder = $this->_getOrderMock();

        /** @var  $oUserPayment oxUserPayment */
        $oUserPayment = $this->getMock(
            'oxuserpayment',
            array('__construct', 'load', 'init', 'isUserPaymentPaymorrowMethod')
        );

        $oUserPayment->expects( $this->once() )
            ->method( 'isUserPaymentPaymorrowMethod' )
            ->will( $this->returnValue( false ) );

        $this->SUT->setPaymentParams( $oUserPayment );

        $this->SUT->expects( $this->once() )
            ->method( '_OxpsPaymorrowOxPaymentGateway_executePayment_parent' )
            ->will( $this->returnValue( false ) );

        $this->assertFalse( $this->SUT->executePayment( 92322, $oOrder ) );

    }

    public function test_executePayment_shouldConfirmOrderToPaymorrowServicesAndCallParent()
    {
        $this->setSessionParam('pm_verify', array('pm_paymentMethod_name' => 'SDD'));

        $oOrder = $this->_getOrderMock(array('getId'));
        $oOrder->expects($this->once())->method('getId')->will($this->returnValue('#ORD-123'));

        /** @var  $oUserPayment oxUserPayment */
        $oUserPayment = $this->getMock(
            'oxuserpayment',
            array('__construct', 'load', 'init', 'isUserPaymentPaymorrowMethod')
        );

        $oUserPayment->expects($this->once())
            ->method('isUserPaymentPaymorrowMethod')
            ->will($this->returnValue(true));

        $this->SUT->setPaymentParams($oUserPayment);

        $oPmRequestControllerMock = $this->getMock(
            'OxpsPaymorrowRequestControllerProxy',
            array('init', '__construct', 'load', 'confirmOrder')
        );
        $oPmRequestControllerMock->expects($this->once())->method('confirmOrder')->with('SDD', '#ORD-123');
        oxTestModules::addModuleObject("OxpsPaymorrowRequestControllerProxy", $oPmRequestControllerMock);

        $this->SUT->expects($this->once())
            ->method('_OxpsPaymorrowOxPaymentGateway_executePayment_parent')
            ->will($this->returnValue(false));

        $this->assertFalse($this->SUT->executePayment(92322, $oOrder));
    }


    public function test_executePayment_shouldReturnFalseWhenIsPaymentMappedToPaymorrowMethodAndResponseHandlerHasErrors()
    {
        $oOrder = $this->_getOrderMock();

        /** @var  $oUserPayment oxUserPayment */
        $oUserPayment = $this->getMock(
            'oxuserpayment',
            array('__construct', 'load', 'init', 'isUserPaymentPaymorrowMethod')
        );

        $oUserPayment->expects( $this->once() )
            ->method( 'isUserPaymentPaymorrowMethod' )
            ->will( $this->returnValue( true ) );

        $this->SUT->setPaymentParams( $oUserPayment );


        $oPmRequestControllerMock = $this->getMock(
            'OxpsPaymorrowRequestControllerProxy',
            array('init', '__construct', 'load', 'confirmOrder')
        );

        oxTestModules::addModuleObject( "OxpsPaymorrowRequestControllerProxy", $oPmRequestControllerMock );


        $oPmResponseHandlerMock = $this->getMock(
            'OxpsPaymorrowResponseHandler',
            array('load', '__construct', 'init', 'hasErrors')
        );
        $oPmResponseHandlerMock->expects( $this->once() )
            ->method( 'hasErrors' )
            ->will( $this->returnValue( true ) );

        oxTestModules::addModuleObject( "OxpsPaymorrowResponseHandler", $oPmResponseHandlerMock );

        $this->assertFalse( $this->SUT->executePayment( 92322, $oOrder ) );
    }


    public function testHandleOrderResponseErrors_inResponseOrderWasNotDeclinedAndHasNoError_nothingIsSavedToSession()
    {
        $SUT = $this->getMock( 'OxpsPaymorrowOxPaymentGateway', array('_setSessionInitData') );
        $SUT->expects( $this->never() )->method( '_setSessionInitData' );

        $oResponseHandlerMock = $this->getMock(
            'OxpsPaymorrowResponseHandler',
            array('wasDeclined', 'getDeclinationDataFromResponse', 'setErrorCode', 'getErrorDataFromResponse')
        );
        $oResponseHandlerMock->expects( $this->once() )->method( 'wasDeclined' )->will( $this->returnValue( false ) );
        $oResponseHandlerMock->expects( $this->never() )->method( 'getDeclinationDataFromResponse' );
        $oResponseHandlerMock->expects( $this->never() )->method( 'setErrorCode' );
        $oResponseHandlerMock->expects( $this->once() )->method( 'getErrorDataFromResponse' )->will(
            $this->returnValue( array() )
        );

        $SUT->_handleOrderResponseErrors( $oResponseHandlerMock );
    }

    public function testHandleOrderResponseErrors_inResponseOrderWasDeclined_saveDeclinationDataToSession()
    {
        $SUT = $this->getMock( 'OxpsPaymorrowOxPaymentGateway', array('_setSessionInitData') );
        $SUT->expects( $this->once() )->method( '_setSessionInitData' )->with(
            array('paymentMethod_status' => 'DECLINED')
        );

        $oResponseHandlerMock = $this->getMock(
            'OxpsPaymorrowResponseHandler',
            array('wasDeclined', 'getDeclinationDataFromResponse', 'setErrorCode', 'getErrorDataFromResponse')
        );
        $oResponseHandlerMock->expects( $this->once() )->method( 'wasDeclined' )->will( $this->returnValue( true ) );
        $oResponseHandlerMock->expects( $this->once() )->method( 'getDeclinationDataFromResponse' )->will(
            $this->returnValue( array('paymentMethod_status' => 'DECLINED') )
        );
        $oResponseHandlerMock->expects( $this->once() )->method( 'setErrorCode' )->with( $this->equalTo( null ) );
        $oResponseHandlerMock->expects( $this->once() )->method( 'getErrorDataFromResponse' )->will(
            $this->returnValue( array() )
        );

        $SUT->_handleOrderResponseErrors( $oResponseHandlerMock );
    }

    public function testHandleOrderResponseErrors_inResponseOrderWasDeclinedAndHadErrors_saveDeclinationAndErrorDataToSession()
    {
        $SUT = $this->getMock( 'OxpsPaymorrowOxPaymentGateway', array('_setSessionInitData') );
        $SUT->expects( $this->once() )->method( '_setSessionInitData' )->with(
            array(
                'paymentMethod_status' => 'DECLINED',
                'error_1_code'         => 'BAD_ORDER'
            )
        );

        $oResponseHandlerMock = $this->getMock(
            'OxpsPaymorrowResponseHandler',
            array('wasDeclined', 'getDeclinationDataFromResponse', 'setErrorCode', 'getErrorDataFromResponse')
        );
        $oResponseHandlerMock->expects( $this->once() )->method( 'wasDeclined' )->will( $this->returnValue( true ) );
        $oResponseHandlerMock->expects( $this->once() )->method( 'getDeclinationDataFromResponse' )->will(
            $this->returnValue( array('paymentMethod_status' => 'DECLINED') )
        );
        $oResponseHandlerMock->expects( $this->once() )->method( 'setErrorCode' )->with( $this->equalTo( null ) );
        $oResponseHandlerMock->expects( $this->once() )->method( 'getErrorDataFromResponse' )->will(
            $this->returnValue( array('error_1_code' => 'BAD_ORDER') )
        );

        $SUT->_handleOrderResponseErrors( $oResponseHandlerMock );
    }

    public function testHandleOrderResponseErrors_inResponseOrderWasNotDeclinedButHasNoError_saveErrorsDataToSession()
    {
        $SUT = $this->getMock( 'OxpsPaymorrowOxPaymentGateway', array('_setSessionInitData') );
        $SUT->expects( $this->once() )->method( '_setSessionInitData' )->with( array('error_1_code' => 'BAD_ORDER') );

        $oResponseHandlerMock = $this->getMock(
            'OxpsPaymorrowResponseHandler',
            array('wasDeclined', 'getDeclinationDataFromResponse', 'setErrorCode', 'getErrorDataFromResponse')
        );
        $oResponseHandlerMock->expects( $this->once() )->method( 'wasDeclined' )->will( $this->returnValue( false ) );
        $oResponseHandlerMock->expects( $this->never() )->method( 'getDeclinationDataFromResponse' );
        $oResponseHandlerMock->expects( $this->once() )->method( 'setErrorCode' )->with( $this->equalTo( null ) );
        $oResponseHandlerMock->expects( $this->once() )->method( 'getErrorDataFromResponse' )->will(
            $this->returnValue( array('error_1_code' => 'BAD_ORDER') )
        );

        $SUT->_handleOrderResponseErrors( $oResponseHandlerMock );
    }
}
