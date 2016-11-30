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
 * Class Unit_Module_Models_OxpsPaymorrowOxUserPaymentTest
 *
 * @see OxpsPaymorrowOxUserPayment
 */
class Unit_Module_Models_OxpsPaymorrowOxUserPaymentTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowOxUserPayment
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
            'OxpsPaymorrowOxUserPayment',
            array('__construct', 'load', 'init',)
        );

    }


    /**
     * Mocking Proxy Class to test Protected methods
     *
     * IDE - might underline methods with RED color, PLEASE IGNORE
     *
     * @param array $aParams
     *
     * @return OxpsPaymorrowOxUserPayment|oxUserPayment
     */
    protected function _getProxySUT( array $aParams = array() )
    {
        return $this->getProxyClass( 'OxpsPaymorrowOxUserPayment', $aParams );
    }


    /**
     * @param bool $blActive
     *
     * @return mixed
     */
    protected function _getPaymentMock( $blActive = true )
    {
        $oPayment = $this->getMock(
            'oxpayment',
            array('init', 'load', '__construct', 'isPaymorrowActiveAndMapped')
        );

        return $oPayment;
    }


    public function test_isUserPaymentPaymorrowMethod_shouldReturnTrueWhenPaymentIsMappedToPaymorrow()
    {
        $oPaymentMock = $this->_getPaymentMock();

        $oPaymentMock->expects( $this->once() )
            ->method( 'isPaymorrowActiveAndMapped' )
            ->will( $this->returnValue( true ) );

        oxTestModules::addModuleObject( "oxpayment", $oPaymentMock );

        $this->assertTrue( $this->SUT->isUserPaymentPaymorrowMethod() );
    }


    public function test_isUserPaymentPaymorrowMethod_shouldReturnFalseWhenPaymentIsNotMapped()
    {
        $oPaymentMock = $this->_getPaymentMock( false );

        $oPaymentMock->expects( $this->once() )
            ->method( 'isPaymorrowActiveAndMapped' )
            ->will( $this->returnValue( false ) );

        oxTestModules::addModuleObject( "oxpayment", $oPaymentMock );

        $this->assertFalse( $this->SUT->isUserPaymentPaymorrowMethod() );
    }


    public function test_getPaymorrowBankName_shouldReturnSetBankName()
    {
        $this->SUT->setPaymorrowBankName( 'Morgan' );

        $this->assertEquals( 'Morgan', $this->SUT->getPaymorrowBankName() );
    }


    public function test_getPaymorrowOrderId_shouldReturnSetValue()
    {
        $this->SUT->setPaymorrowOrderId( 'ORDER_ID_PM' );

        $this->assertEquals( 'ORDER_ID_PM', $this->SUT->getPaymorrowOrderId() );
    }


    public function test_getPaymorrowIBAN_shouldReturnSetIBAN()
    {
        $this->SUT->setPaymorrowIBAN( 'IBAN_TEST' );

        $this->assertEquals( 'IBAN_TEST', $this->SUT->getPaymorrowIBAN() );
    }


    public function test_getPaymorrowBIC_shouldReturnSetBIC()
    {
        $this->SUT->setPaymorrowBIC( 'BIC_TEST' );

        $this->assertEquals( 'BIC_TEST', $this->SUT->getPaymorrowBIC() );
    }


    public function test_getPaymorrowOxPayment_shouldReturnOxPaymentInstance()
    {
        $sPaymentId = 'ajfawfhs3';

        $this->SUT->oxuserpayments__oxpaymentsid = new oxField( $sPaymentId );

        $this->assertTrue( $this->SUT->getPaymorrowOxPayment() instanceof oxPayment );
    }
}
