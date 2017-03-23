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
 * Class Unit_Module_Controllers_OxpsPaymorrowOrderTest
 *
 * @see OxpsPaymorrowOrder
 */
class Unit_Module_Controllers_OxpsPaymorrowOrderTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowOrder
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
            'OxpsPaymorrowOrder',
            array(
                'init', 'confirmOrder', 'getPayment', '_checkForErrorsToRedirect',
                '_OxpsPaymorrowOrder_getNextStep_parent', '_OxpsPaymorrowOrder_render_parent'
            )
        );

    }

    /**
     * Mocking Proxy Class to test Protected methods
     *
     * IDE - might underline methods with RED color, PLEASE IGNORE
     *
     * @param array $aParams
     *
     * @return OxpsPaymorrowOrder
     */
    protected function _getProxySUT( array $aParams = array() )
    {
        return $this->getProxyClass( 'OxpsPaymorrowOrder', $aParams );
    }


    public function testRender_callPaymorrowMethodSetCheckErrorsAndReturnParent()
    {
        $SUT = $this->getMock(
            'OxpsPaymorrowOrder',
            array('_setPaymorrowTypeOfPayment', '_checkForErrorsToRedirect', '_OxpsPaymorrowOrder_render_parent')
        );
        $SUT->expects( $this->once() )->method( '_setPaymorrowTypeOfPayment' );
        $SUT->expects( $this->once() )->method( '_checkForErrorsToRedirect' );
        $SUT->expects( $this->once() )->method( '_OxpsPaymorrowOrder_render_parent' )->will(
            $this->returnValue( 'order' )
        );

        $this->assertSame( 'order', $SUT->render() );
    }


    public function testGetNextStep_shouldReturnOrderStringWhenPaymorrowOrderErrorsArePresentInSession()
    {
        $SUT = $this->_getProxySUT();

        $this->assertEquals('payment?payerror=454', $SUT->_getNextStep(454));
    }


    public function testGetNextStep_shouldCallParentWhenPaymorrowOrderErrorFromSessionIsEmpty()
    {
        $SUT = $this->_getProxySUT();

        $this->assertEquals('payment?payerror=2', $SUT->_getNextStep(oxOrder::ORDER_STATE_PAYMENTERROR));
    }

    public function testGetNextStep_errorCodeIsFour_changeItToFiveOnParentCall()
    {
        $SUT = $this->_getProxySUT();

        $this->assertEquals('payment?payerror=5', $SUT->_getNextStep(4));
    }

    public function testGetNextStep_noResponseError_shouldDeleteOrderDataFromSession()
    {
        $SUT = $this->_getProxySUT();

        // Response handler mock
        $oResponseHandlerMock = $this->getMock(
            'OxpsPaymorrowResponseHandler',
            array('__construct', '__call', 'hasErrors', 'getErrorCode')
        );
        $oResponseHandlerMock->expects($this->once())->method('hasErrors')->will($this->returnValue(false));
        $oResponseHandlerMock->expects($this->never())->method('getErrorCode');

        oxTestModules::addModuleObject('OxpsPaymorrowResponseHandler', $oResponseHandlerMock);

        // Error handler mock
        $oErrorHandlerMock = $this->getMock(
            'OxpsPaymorrowErrorHandler',
            array('__construct', 'redirectWithError')
        );
        $oErrorHandlerMock->expects($this->never())->method('redirectWithError');

        oxTestModules::addModuleObject('OxpsPaymorrowErrorHandler', $oErrorHandlerMock);

        // Session mock
        $oSessionMock = $this->getMock('oxSession', array('deleteVariable'));
        $oSessionMock->expects($this->at(0))->method('deleteVariable')->with('pm_verify');
        $oSessionMock->expects($this->at(1))->method('deleteVariable')->with('pm_response');
        $oSessionMock->expects($this->at(2))->method('deleteVariable')->with('pm_responseINVOICE');
        $oSessionMock->expects($this->at(3))->method('deleteVariable')->with('pm_responseSDD');
        $oSessionMock->expects($this->at(4))->method('deleteVariable')->with('pm_order_transaction_id');
        $oSessionMock->expects($this->at(5))->method('deleteVariable')->with('pm_order_transaction_idINVOICE');
        $oSessionMock->expects($this->at(6))->method('deleteVariable')->with('pm_order_transaction_idSDD');

        oxRegistry::set('oxSession', $oSessionMock);

        $SUT->_getNextStep(1);
    }


    public function test_setPaymorrowTypeOfPayment_shouldDoNothingWhenPaymorrowIsNotActiveOrMapped()
    {
        $oxPaymentMock = $this->getMock(
            'oxpayment',
            array('load', '__construct', 'init', 'isPaymorrowActiveAndMapped')
        );

        $oxPaymentMock->expects( $this->once() )
            ->method( 'isPaymorrowActiveAndMapped' )
            ->will( $this->returnValue( false ) );

        $this->SUT->expects( $this->any() )
            ->method( 'getPayment' )
            ->will( $this->returnValue( $oxPaymentMock ) );

        $this->SUT->_setPaymorrowTypeOfPayment();

        $this->assertTrue( $this->SUT->getPayment() instanceof oxPayment );
    }


    public function test_setPaymorrowTypeOfPayment_shouldSet_oxpayments__oxdesc_toPaymorrowInvoiceType()
    {
        $oxPaymentMock = $this->getMock(
            'oxpayment',
            array('load', '__construct', 'init', 'isPaymorrowActiveAndMapped', 'getPaymorrowPaymentType')
        );

        $oxPaymentMock->expects( $this->once() )
            ->method( 'isPaymorrowActiveAndMapped' )
            ->will( $this->returnValue( true ) );

        $oxPaymentMock->expects( $this->once() )
            ->method( 'getPaymorrowPaymentType' )
            ->will( $this->returnValue( 'pm_invoice' ) );

        $this->SUT->expects( $this->any() )
            ->method( 'getPayment' )
            ->will( $this->returnValue( $oxPaymentMock ) );

        $this->SUT->_setPaymorrowTypeOfPayment();

        $this->assertEquals(
            oxRegistry::getLang()->translateString( 'PAYMORROW_PAYMENT_METHOD_NAME_INVOICE' ),
            $this->SUT->getPayment()->oxpayments__oxdesc->value
        );
    }


    public function test_setPaymorrowTypeOfPayment_shouldSet_oxpayments__oxdesc_toPaymorrowDirectDebitType()
    {
        $oxPaymentMock = $this->getMock(
            'oxpayment',
            array('load', '__construct', 'init', 'isPaymorrowActiveAndMapped', 'getPaymorrowPaymentType')
        );

        $oxPaymentMock->expects( $this->once() )
            ->method( 'isPaymorrowActiveAndMapped' )
            ->will( $this->returnValue( true ) );

        $oxPaymentMock->expects( $this->exactly( 2 ) )
            ->method( 'getPaymorrowPaymentType' )
            ->will( $this->returnValue( 'pm_sdd' ) );

        $this->SUT->expects( $this->any() )
            ->method( 'getPayment' )
            ->will( $this->returnValue( $oxPaymentMock ) );

        $this->SUT->_setPaymorrowTypeOfPayment();

        $this->assertEquals(
            oxRegistry::getLang()->translateString( 'PAYMORROW_PAYMENT_METHOD_NAME_DIRECT_DEBIT' ),
            $this->SUT->getPayment()->oxpayments__oxdesc->value
        );
    }


    public function test_getNextStep_shouldParentWhenPaymorrowResponseHandlerDoesNotHaveErrors()
    {
        $oPmResponseHandler = $this->getMock(
            'OxpsPaymorrowResponseHandler',
            array('init', '__construct', 'load', 'hasErrors', 'getErrorCode')
        );

        $oPmResponseHandler->expects( $this->once() )
            ->method( 'hasErrors' )
            ->will( $this->returnValue( true ) );

        $oPmResponseHandler->expects( $this->once() )
            ->method( 'getErrorCode' )
            ->will( $this->returnValue( 33 ) );

        $oPmErrorHandler = $this->getMock(
            'OxpsPaymorrowErrorHandler',
            array('init', '__construct', 'load', 'redirectWithError')
        );

        $oPmErrorHandler->expects( $this->once() )
            ->method( 'redirectWithError' )
            ->will( $this->returnValue( null ) );

        oxTestModules::addModuleObject( 'OxpsPaymorrowResponseHandler', $oPmResponseHandler );
        oxTestModules::addModuleObject( 'OxpsPaymorrowErrorHandler', $oPmErrorHandler );

        $SUT = $this->_getProxySUT();

        $this->assertEquals( 'payment?payerror=6565', $SUT->_getNextStep( 6565 ) );
    }
}
