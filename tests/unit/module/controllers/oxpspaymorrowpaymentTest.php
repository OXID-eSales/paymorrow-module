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
 * Class Unit_Module_Controllers_OxpsPaymorrowPaymentTest
 *
 * @see OxpsPaymorrowPayment
 */
class Unit_Module_Controllers_OxpsPaymorrowPaymentTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowPayment
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
            'OxpsPaymorrowPayment',
            array('init', 'render', '_unsetSessionInitData', '_OxpsPaymorrowPayment_validatePayment_parent')
        );
        $this->SUT->expects($this->once())->method('_unsetSessionInitData');
        $this->SUT->expects($this->once())->method('_OxpsPaymorrowPayment_validatePayment_parent')->will(
            $this->returnValue('order')
        );
    }


    public function testValidatePayment_paymentMethodNotLoaded_returnParentResult()
    {
        $this->setConfigParam('paymentid', '');

        // Payment mock
        $this->_setPaymentMock('', false, false);

        $this->assertSame('order', $this->SUT->validatePayment());
    }

    public function testValidatePayment_paymentMethodIsNotPaymorrow_returnParentResult()
    {
        $this->setRequestParameter('paymentid', 'COD');

        // Payment mock
        $this->_setPaymentMock('COD', true, false);

        $this->assertSame('order', $this->SUT->validatePayment());
    }

    public function testValidatePayment_paymentMethodIsOkAndPaymorrowResponseEmpty_returnNull()
    {
        $this->setRequestParameter('paymentid', 'invoice');
        $this->setSessionParam('pm_verify', array('pm_paymentMethod_name' => 'INVOICE'));
        $this->setSessionParam('pm_responseINVOICE', array());

        // Payment mock
        $this->_setPaymentMock();

        $this->assertNull($this->SUT->validatePayment());
    }

    public function testValidatePayment_paymentMethodIsOkAndPaymorrowResponseStatusEmpty_returnNull()
    {
        $this->setRequestParameter('paymentid', 'invoice');
        $this->setSessionParam('pm_verify', array('pm_paymentMethod_name' => 'INVOICE'));
        $this->setSessionParam('pm_responseINVOICE', array('order_status' => 'VALIDATED'));

        // Payment mock
        $this->_setPaymentMock();

        $this->assertNull($this->SUT->validatePayment());
    }

    public function testValidatePayment_paymentMethodIsOkAndPaymorrowResponseStatusIsError_returnNull()
    {
        $this->setRequestParameter('paymentid', 'invoice');
        $this->setSessionParam('pm_verify', array('pm_paymentMethod_name' => 'INVOICE'));
        $this->setSessionParam(
            'pm_responseINVOICE', array('order_status' => 'VALIDATED', 'response_status' => 'Error')
        );

        // Payment mock
        $this->_setPaymentMock();

        $this->assertNull($this->SUT->validatePayment());
    }

    public function testValidatePayment_paymentMethodIsOkAndPaymorrowResponseOrderStatusEmpty_returnNull()
    {
        $this->setRequestParameter('paymentid', 'invoice');
        $this->setSessionParam('pm_verify', array('pm_paymentMethod_name' => 'INVOICE'));
        $this->setSessionParam('pm_responseINVOICE', array('response_status' => 'OK'));

        // Payment mock
        $this->_setPaymentMock();

        $this->assertNull($this->SUT->validatePayment());
    }

    public function testValidatePayment_paymentMethodIsOkAndPaymorrowResponseOrderStatusInvalid_returnNull()
    {
        $this->setRequestParameter('paymentid', 'invoice');
        $this->setSessionParam('pm_verify', array('pm_paymentMethod_name' => 'INVOICE'));
        $this->setSessionParam(
            'pm_responseINVOICE', array('order_status' => 'INVALIDATED', 'response_status' => 'OK')
        );

        // Payment mock
        $this->_setPaymentMock();

        $this->assertNull($this->SUT->validatePayment());
    }

    public function testValidatePayment_responseDoesNotMatchSelectedPaymentMethod_returnNull()
    {
        $this->setRequestParameter('paymentid', 'invoice');
        $this->setSessionParam('pm_verify', array('pm_paymentMethod_name' => 'SDD'));
        $this->setSessionParam(
            'pm_responseINVOICE', array('order_status' => 'VALIDATED', 'response_status' => 'OK')
        );

        // Payment mock
        $this->_setPaymentMock();

        $this->assertNull($this->SUT->validatePayment());
    }

    public function testValidatePayment_paymentMethodIsOkAndPaymorrowResponseIsOk_returnParentResult()
    {
        $this->setRequestParameter('paymentid', 'invoice');
        $this->setSessionParam('pm_verify', array('pm_paymentMethod_name' => 'INVOICE'));
        $this->setSessionParam(
            'pm_responseINVOICE', array('order_status' => 'VALIDATED', 'response_status' => 'OK')
        );

        // Payment mock
        $this->_setPaymentMock();

        $this->assertSame('order', $this->SUT->validatePayment());
    }

    public function testValidatePayment_orderStatusIsAccepted_alsoReturnParentResult()
    {
        $this->setRequestParameter('paymentid', 'invoice');
        $this->setSessionParam('pm_verify', array('pm_paymentMethod_name' => 'INVOICE'));
        $this->setSessionParam(
            'pm_responseINVOICE', array('order_status' => 'ACCEPTED', 'response_status' => 'OK')
        );

        // Payment mock
        $this->_setPaymentMock();

        $this->assertSame('order', $this->SUT->validatePayment());
    }


    /**
     * Create and set payment method mock.
     *
     * @param string $sPaymentMethodId
     * @param bool   $blLoaded
     * @param bool   $blPaymorrow
     */
    protected function _setPaymentMock($sPaymentMethodId = 'invoice', $blLoaded = true, $blPaymorrow = true)
    {
        $oPaymentMock = $this->getMock('oxPayment', array('__construct', 'load', 'isPaymorrowActiveAndMapped'));

        $oPaymentMock->expects($this->once())->method('load')
            ->with($this->equalTo((string) $sPaymentMethodId))
            ->will($this->returnValue((bool) $blLoaded));
        $oPaymentMock->expects($this->any())->method('isPaymorrowActiveAndMapped')->will(
            $this->returnValue((bool) $blPaymorrow)
        );

        oxTestModules::addModuleObject('oxPayment', $oPaymentMock);
    }
}
