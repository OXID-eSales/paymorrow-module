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
 * Class Unit_Module_Models_OxpsPaymorrowOxPaymentTest
 *
 * @see OxpsPaymorrowOxPayment
 */
class Unit_Module_Models_OxpsPaymorrowOxPaymentTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowOxPayment
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
            'OxpsPaymorrowOxPayment',
            array('__construct', 'load', '_OxpsPaymorrowOxPayment_isValidPayment_parent')
        );
    }


    /**
     * Mocking Proxy Class to test Protected methods
     *
     * IDE - might underline methods with RED color, PLEASE IGNORE
     *
     * @param array $aParams
     *
     * @return oxpspaymorrowoxpayment|oxPayment
     */
    protected function _getProxySUT( array $aParams = array() )
    {
        return $this->getProxyClass( 'oxpspaymorrowoxpayment', $aParams );
    }


    public function test_setPaymorrowPaymentMapShouldReturnSetPaymentMap2()
    {
        $this->SUT->setPaymorrowPaymentMap( 2 );

        $this->assertEquals( 2, $this->SUT->getPaymorrowPaymentMap() );
    }

    public function test_setPaymorrowPaymentMapShouldReturnSetPaymentMap1()
    {
        $this->SUT->setPaymorrowPaymentMap( 1 );

        $this->assertEquals( 1, $this->SUT->getPaymorrowPaymentMap() );
    }


    public function test_setPaymorrowPaymentMapShouldReturnSetPaymentMap0()
    {
        $this->SUT->setPaymorrowPaymentMap( 0 );

        $this->assertEquals( 0, $this->SUT->getPaymorrowPaymentMap() );
    }


    public function test_setPaymorrowPaymentMap_shouldReturn()
    {
        $this->SUT->setPaymorrowPaymentMap( 29393 ); // Not Existing Paymorrow Payment type

        $this->assertNull( $this->SUT->getPaymorrowPaymentMap() );
    }


    public function testGetTitle_nothingSet_returnEmptyString()
    {
        $this->assertSame( '', $this->SUT->getTitle() );
    }

    public function testGetTitle_descriptionFieldSet_returnTheSetValue()
    {
        $this->SUT->oxpayments__oxdesc = new oxField( 'COD' );

        $this->assertSame( 'COD', $this->SUT->getTitle() );
    }


    public function test_getPaymorrowPaymentType_shouldReturnPaymentType_pm_off()
    {
        $this->SUT->setPaymorrowPaymentMap( 0 );

        $this->assertEquals( 'pm_off', $this->SUT->getPaymorrowPaymentType() );
    }


    public function test_getPaymorrowPaymentType_shouldReturnPaymentType_pm_invoice()
    {
        $this->SUT->setPaymorrowPaymentMap( 1 );

        $this->assertEquals( 'pm_invoice', $this->SUT->getPaymorrowPaymentType() );
    }


    public function test_getPaymorrowPaymentType_shouldReturnPaymentType_pm_sdd()
    {
        $this->SUT->setPaymorrowPaymentMap( 2 );

        $this->assertEquals( 'pm_sdd', $this->SUT->getPaymorrowPaymentType() );
    }


    public function testIsPaymorrowActiveAndMapped_methodIsNotActive_returnFalse()
    {
        $this->SUT->setPaymorrowActive( 0 );

        $this->assertFalse( $this->SUT->isPaymorrowActiveAndMapped() );
    }


    public function testIsPaymorrowActiveAndMapped_methodIsNotMapped_returnFalse()
    {
        $this->SUT->setPaymorrowActive( 1 );
        $this->SUT->setPaymorrowPaymentMap( 0 );

        $this->assertFalse( $this->SUT->isPaymorrowActiveAndMapped() );
    }

    public function testIsPaymorrowActiveAndMapped_merchantIdNotSet_returnFalse()
    {
        $this->SUT->setPaymorrowActive( 1 );
        $this->SUT->setPaymorrowPaymentMap( 1 );

        $oSettingsMock = $this->getMock( 'OxpsPaymorrowSettings', array('getMerchantId') );
        $oSettingsMock->expects( $this->once() )->method( 'getMerchantId' )->will( $this->returnValue( '' ) );
        oxRegistry::set( 'OxpsPaymorrowSettings', $oSettingsMock );

        $this->assertFalse( $this->SUT->isPaymorrowActiveAndMapped() );
    }

    public function testIsPaymorrowActiveAndMapped_methodIsActiveMappedAndMerchantIdSet_returnTrue()
    {
        $this->SUT->setPaymorrowActive( 1 );
        $this->SUT->setPaymorrowPaymentMap( 2 );

        $oSettingsMock = $this->getMock( 'OxpsPaymorrowSettings', array('getMerchantId') );
        $oSettingsMock->expects( $this->once() )->method( 'getMerchantId' )->will( $this->returnValue( 'MyId' ) );
        oxRegistry::set( 'OxpsPaymorrowSettings', $oSettingsMock );

        $this->assertTrue( $this->SUT->isPaymorrowActiveAndMapped() );
    }


    public function test_setPaymorrowActive_shouldReturnSetValue()
    {
        $this->SUT->setPaymorrowActive( 1 );

        $this->assertEquals( 1, $this->SUT->getPaymorrowActive() );
    }


    public function test_isPaymorrowActive_shouldReturnFalse()
    {
        $this->assertFalse( $this->SUT->isPaymorrowActive() );
    }


    public function test_isPaymorrowActive_shouldReturnTrueWhenActive()
    {
        $this->SUT->setPaymorrowActive( 1 );

        $this->assertTrue( $this->SUT->isPaymorrowActive() );
    }


    public function test_getPaymorrowPaymentType_shouldReturnPaymentType_Null()
    {
        $this->SUT->setPaymorrowPaymentMap( 2932293 ); // Not Existing Paymorrow Payment type

        $this->assertNull( $this->SUT->getPaymorrowPaymentType() );
    }


    public function test_isPaymentMappedToPaymorrowMethod_shouldReturnTrueWhenPaymentIsMapped_pm_ssd()
    {
        $this->SUT->setPaymorrowPaymentMap( 2 );

        $this->assertTrue( $this->SUT->isPaymentMappedToPaymorrowMethod() );

    }


    public function test_setPaymorrowPaymentMap_shouldReturnFalseWhenMappingIsInvalid()
    {
        $this->assertFalse( $this->SUT->setPaymorrowPaymentMap( 3435 ) );
    }

    public function test_setPaymorrowPaymentMap_shouldReturnTrueWhenIsValidMapping()
    {
        $this->assertTrue( $this->SUT->setPaymorrowPaymentMap( 2 ) );
    }


    public function test_isPaymentMappedToPaymorrowMethod_shouldReturnTrueWhenPaymentIsMapped_pm_invoice()
    {
        $this->SUT->setPaymorrowPaymentMap( 1 );

        $this->assertTrue( $this->SUT->isPaymentMappedToPaymorrowMethod() );

    }


    public function test_isPaymentMappedToPaymorrowMethod_shouldReturnTrueWhenPaymentIsNotMapped()
    {
        $this->assertFalse( $this->SUT->isPaymentMappedToPaymorrowMethod() );
    }


    public function testIsValidPayment_paymentIsNotPaymorrow_returnParentResultAndPreserveErrorCode()
    {
        $this->_setPaymentValidationMock( false, 0, 1 );

        $this->assertFalse( $this->SUT->isValidPayment( array(), '', new stdClass(), 100.0, '' ) );
        $this->assertSame( 1, $this->SUT->getPaymentErrorNumber() );
    }

    public function testIsValidPayment_paymentIsValid_returnTrue()
    {
        $this->_setPaymentValidationMock( true, 1, 0 );

        $this->assertTrue( $this->SUT->isValidPayment( array(), '', new stdClass(), 100.0, '' ) );
        $this->assertSame( 0, $this->SUT->getPaymentErrorNumber() );
    }

    public function testIsValidPayment_paymentIsInvalidButErrorCodeIsNotOne_returnFalseAndPreserveErrorCode()
    {
        $this->_setPaymentValidationMock( false, 1, 2 );

        $this->assertFalse( $this->SUT->isValidPayment( array(), '', new stdClass(), 100.0, '' ) );
        $this->assertSame( 2, $this->SUT->getPaymentErrorNumber() );
    }

    public function testIsValidPayment_paymentIsInvalidAndErrorCodeIsOne_returnTrueAndResetErrorCode()
    {
        $this->_setPaymentValidationMock( false, 1, 1 );

        $this->assertTrue( $this->SUT->isValidPayment( array(), '', new stdClass(), 100.0, '' ) );
        $this->assertNull( $this->SUT->getPaymentErrorNumber() );;
    }


    /**
     * Set SUT mock for method `isValidPayment` testing.
     *
     * @param bool $blIsParentValid
     * @param int  $iPaymorrowActive 0 or 1
     * @param int  $iPaymentError
     */
    protected function _setPaymentValidationMock( $blIsParentValid, $iPaymorrowActive, $iPaymentError )
    {
        $this->SUT->expects( $this->once() )->method( '_OxpsPaymorrowOxPayment_isValidPayment_parent' )
            ->with(
                $this->equalTo( array() ), $this->equalTo( '' ), $this->equalTo( new stdClass() ),
                $this->equalTo( 100.0 ), $this->equalTo( '' )
            )
            ->will( $this->returnValue( (bool) $blIsParentValid ) );

        $this->SUT->oxpayments__oxpspaymorrowactive = new oxField( (int) (bool) $iPaymorrowActive );

        $this->SUT->_iPaymentError = (int) $iPaymentError;
    }
}
