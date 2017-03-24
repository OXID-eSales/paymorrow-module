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
 * Loading class for CodeCoverage report
 */
if ( !class_exists( 'OxpsPaymorrowPaymentMap' ) ) {
    require_once( 'OxpsPaymorrowPaymentMap' );
}

/**
 * Class Unit_Module_Controllers_OxpsPaymorrowPaymentMapTest
 *
 * @see OxpsPaymorrowPaymentMap
 */
class Unit_Module_Controllers_OxpsPaymorrowPaymentMapTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowPaymentMap
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
        $this->SUT = $this->getMock( 'OxpsPaymorrowPaymentMap', array('init', 'load', '__construct') );
    }


    public function test_getPaymorrowEditValue_shouldReturnNullWhenPaymentIdIsNotPresent()
    {
        $this->assertNull( $this->SUT->getPaymorrowEditValue() );
    }


    public function test_getPaymorrowEditValue_shouldReturnOxPaymentInstance()
    {
        $this->setRequestParameter( 'oxid', 'PHP_UNIT_TEST' );

        $this->assertTrue( $this->SUT->getPaymorrowEditValue() instanceof oxPayment );
    }


    public function test_getPaymentObjectId_shouldReturnExpectedId()
    {
        $this->setRequestParameter( 'oxid', 'PHP_UNIT_TEST' );

        $this->assertEquals( 'PHP_UNIT_TEST', $this->SUT->getPaymentObjectId() );
    }


    public function test_save_shouldNotSavePaymentMapWhenMappingIsInvalid()
    {
        $aEditVal = array(
            'oxpayments__oxid'                => 'agjklwjg92jlawk',
            'oxpayments__oxpspaymorrowactive' => 1,
            'oxpayments__oxpspaymorrowmap'    => 2,
        );

        $this->setRequestParameter( 'editval', $aEditVal );

        $oxPaymentMock = $this->getMock(
            'oxpayment',
            array('__construct', 'load', 'init', 'save', 'setPaymorrowPaymentMap', 'setPaymorrowActive')
        );

        $oxPaymentMock->expects( $this->once() )
            ->method( 'setPaymorrowPaymentMap' )
            ->will( $this->returnValue( false ) );

        oxTestModules::addModuleObject( "oxpayment", $oxPaymentMock );


        $this->assertNull( $this->SUT->save() );

    }


    public function test_save_shouldSaveMapWhenMappingIsValid()
    {
        $aEditVal = array(
            'oxpayments__oxid'                => 'agjklwjg92jlawk',
            'oxpayments__oxpspaymorrowactive' => 1,
            'oxpayments__oxpspaymorrowmap'    => 2,
        );

        $this->setRequestParameter( 'editval', $aEditVal );

        $oxPaymentMock = $this->getMock(
            'oxpayment',
            array('__construct', 'load', 'init', 'save', 'setPaymorrowPaymentMap', 'setPaymorrowActive')
        );

        $oxPaymentMock->expects( $this->once() )
            ->method( 'setPaymorrowPaymentMap' )
            ->will( $this->returnValue( true ) );

        oxTestModules::addModuleObject( "oxpayment", $oxPaymentMock );


        $this->assertNull( $this->SUT->save() );

    }

    public function testSetDuplicatedFields_fieldNotSetInTheClass_theValueIsNotChanged()
    {
        $SUT = $this->_getSutProxy();

        $oPaymentMock = $this->getMock( 'oxPayment', array('__construct') );

        $oPaymentMock->oxpayments__oxdesc = new oxField( 'Good method.' );

        $SUT->_setDuplicatedFields( $oPaymentMock, array('oxpayments__oxdesc' => 'Super payment!') );
        $this->assertSame( 'Good method.', $oPaymentMock->oxpayments__oxdesc->value );
    }

    public function testSetDuplicatedFields_textFieldNotPresentInDataArray_theValueIsNotChanged()
    {
        $SUT = $this->_getSutProxy();

        $oPaymentMock = $this->getMock( 'oxPayment', array('__construct') );

        $oPaymentMock->oxpayments__oxaddsum = new oxField( 100.88 );

        $SUT->_setDuplicatedFields( $oPaymentMock, array('oxpayments__oxaddsumtype' => 'abs') );
        $this->assertSame( 100.88, $oPaymentMock->oxpayments__oxaddsum->value );
    }

    public function testSetDuplicatedFields_fieldCheckedNotSetInDataArray_theValueIsSetToZero()
    {
        $SUT = $this->_getSutProxy();

        $oPaymentMock = $this->getMock( 'oxPayment', array('__construct') );

        $oPaymentMock->oxpayments__oxchecked = new oxField( 1 );

        $SUT->_setDuplicatedFields( $oPaymentMock, array() );
        $this->assertSame( 0, $oPaymentMock->oxpayments__oxchecked->value );
    }

    public function testSetDuplicatedFields_fieldAmountTypeHasInvalidValue_theValueIsNotChanged()
    {
        $SUT = $this->_getSutProxy();

        $oPaymentMock = $this->getMock( 'oxPayment', array('__construct') );

        $oPaymentMock->oxpayments__oxaddsumtype = new oxField( 'abs' );

        $SUT->_setDuplicatedFields( $oPaymentMock, array('oxpayments__oxaddsumtype' => 'itm') );
        $this->assertSame( 'abs', $oPaymentMock->oxpayments__oxaddsumtype->value );
    }

    public function testSetDuplicatedFields_allFieldsSetAndValid_theValuesAreChangedToCorrespondingValuesAndTypes()
    {
        $SUT = $this->_getSutProxy();

        $oPaymentMock = $this->getMock( 'oxPayment', array('__construct') );

        $oPaymentMock->oxpayments__oxaddsum     = new oxField( 0.0 );
        $oPaymentMock->oxpayments__oxaddsumtype = new oxField( '%' );
        $oPaymentMock->oxpayments__oxfromamount = new oxField( 0.0 );
        $oPaymentMock->oxpayments__oxtoamount   = new oxField( 100000.0 );
        $oPaymentMock->oxpayments__oxchecked    = new oxField( 0 );
        $oPaymentMock->oxpayments__oxsort       = new oxField( 0 );

        $SUT->_setDuplicatedFields(
            $oPaymentMock,
            array(
                'oxpayments__oxaddsum'     => '20.50',
                'oxpayments__oxaddsumtype' => 'abs',
                'oxpayments__oxfromamount' => '20',
                'oxpayments__oxtoamount'   => '3000',
                'oxpayments__oxchecked'    => '1',
                'oxpayments__oxsort'       => '10.0',
            )
        );

        $this->assertSame( 20.5, $oPaymentMock->oxpayments__oxaddsum->value );
        $this->assertSame( 'abs', $oPaymentMock->oxpayments__oxaddsumtype->value );
        $this->assertSame( 20.0, $oPaymentMock->oxpayments__oxfromamount->value );
        $this->assertSame( 3000.0, $oPaymentMock->oxpayments__oxtoamount->value );
        $this->assertSame( 1, $oPaymentMock->oxpayments__oxchecked->value );
        $this->assertSame( 10, $oPaymentMock->oxpayments__oxsort->value );
    }


    /**
     * Create a proxy type mock to test protected methods directly.
     *
     * @return object
     */
    protected function _getSutProxy()
    {
        return $this->getProxyClass( 'OxpsPaymorrowPaymentMap', array('__construct', 'init') );
    }
}
