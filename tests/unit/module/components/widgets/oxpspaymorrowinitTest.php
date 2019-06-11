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
 * Class Unit_Module_Components_OxpsPaymorrowInitTest
 *
 * @see OxpsPaymorrowInit
 */
class Unit_Module_Components_OxpsPaymorrowInitTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowInit
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
            'OxpsPaymorrowInit',
            array(
                'init', '_getPaymorrowPrintData', 'getSelectedPaymorrowMethod',
                'getPaymorrowControllerPrepareOrderProcessPaymentURL'
            )
        );

    }


    public function testNothing()
    {
        $this->assertTrue(true);
    }


    /**
     * Mocking Proxy Class to test Protected methods
     *
     * IDE - might underline methods with RED color, PLEASE IGNORE
     *
     * @param array $aParams
     *
     * @return oxpspaymorrowinit
     */
    protected function _getProxySUT( array $aParams = array() )
    {
        return $this->getProxyClass(
            'oxpspaymorrowinit', array_merge( array('__construct', 'load', 'init'), $aParams )
        );
    }


    public function testGetPaymorrowControllerPrepareOrderProcessPaymentURL_returnExpectedURL()
    {
        $oConfigMock = $this->getMock( 'oxConfig', array('getShopSecureHomeURL') );
        $oConfigMock->expects( $this->once() )->method( 'getShopSecureHomeURL' )->will(
            $this->returnValue( 'http://www.example.com/index.php?' )
        );

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfigMock);

        $SUT = oxNew( 'OxpsPaymorrowInit' );

        $this->assertSame(
            'http://www.example.com/index.php?cl=oxpspaymorrowprepareorder&fnc=prepareOrder',
            $SUT->getPaymorrowControllerPrepareOrderProcessPaymentURL()
        );
    }


    public function test_getPaymorrowResourceControllerJavaScript_shouldReturnExpectedLink()
    {
        $this->assertStringEndsWith(
            'cl=oxpspaymorrowresource&fnc=getPaymorrowJavaScript',
            $this->SUT->getPaymorrowResourceControllerJavaScript()
        );

    }


    public function test_getPaymorrowResourceControllerCSS_shouldReturnExpectedLink()
    {
        $this->assertStringEndsWith(
            'cl=oxpspaymorrowresource&fnc=getPaymorrowCSS', $this->SUT->getPaymorrowResourceControllerCSS()
        );
    }


    public function test_getPaymorrowJavaScriptPmInitFull_shouldReturnWrappedJsFunction()
    {
        $this->SUT->expects($this->once())
            ->method('_getPaymorrowPrintData')
            ->will($this->returnValue('printData'));

        $this->SUT->expects($this->once())
            ->method('getPaymorrowControllerPrepareOrderProcessPaymentURL')
            ->will($this->returnValue('URL'));

        $this->SUT->expects($this->once())
            ->method('getSelectedPaymorrowMethod')
            ->will($this->returnValue('pm_sdd'));

        $this->assertEquals(
            '$(document).ready(function(){' .
            'pmInitFull("SDD", "pmsdd", "rb_payment_sdd", "dl_payment_sdd", "payment", printData, "URL", true, "paymentNextStepBottom");' .
            'pmInitFull("INVOICE", "pminvoice", "rb_payment_invoice", "dl_payment_invoice", "payment", printData, "URL", false, "paymentNextStepBottom");' .
            '});',
            $this->SUT->getPaymorrowJavaScriptPmInitFull()
        );
    }


    public function testGetSelectedPaymorrowMethod_validPaymorrowPaymentIdIsInSession_loadTheMethodReturnItCode()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|OxpsPaymorrowInit $SUT */
        $SUT = $this->getMock( 'OxpsPaymorrowInit', array('init') );

        $this->setSessionParam('paymentid', 'invoice');

        // Payment model mock
        $oPaymentMock = $this->getMock(
            'OxpsPaymorrowOxPayment',
            array(
                '__construct', 'load', 'isPaymorrowActiveAndMapped', 'loadPaymorrowDefault', 'getPaymorrowPaymentType'
            )
        );
        $oPaymentMock->expects( $this->once() )->method( 'load' )->with( $this->equalTo( 'invoice' ) )->will(
            $this->returnValue( true )
        );
        $oPaymentMock->expects( $this->once() )->method( 'isPaymorrowActiveAndMapped' )->will(
            $this->returnValue( true )
        );
        $oPaymentMock->expects( $this->never() )->method( 'loadPaymorrowDefault' );
        $oPaymentMock->expects( $this->once() )->method( 'getPaymorrowPaymentType' )->will(
            $this->returnValue( 'pm_invoice' )
        );

        oxTestModules::addModuleObject( 'OxpsPaymorrowOxPayment', $oPaymentMock );

        $this->assertSame( 'pm_invoice', $SUT->getSelectedPaymorrowMethod() );
    }

    public function testGetSelectedPaymorrowMethod_noValidPaymentIdInSession_loadDefaultSelectedPaymorrowMethodAndReturnItsCode()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|OxpsPaymorrowInit $SUT */
        $SUT = $this->getMock( 'OxpsPaymorrowInit', array('init') );

        $this->setSessionParam('paymentid', '');

        // Payment model mock
        $oPaymentMock = $this->getMock(
            'OxpsPaymorrowOxPayment',
            array(
                '__construct', 'load', 'isPaymorrowActiveAndMapped', 'loadPaymorrowDefault', 'getPaymorrowPaymentType'
            )
        );
        $oPaymentMock->expects( $this->never() )->method( 'load' );
        $oPaymentMock->expects( $this->never() )->method( 'isPaymorrowActiveAndMapped' );
        $oPaymentMock->expects( $this->once() )->method( 'loadPaymorrowDefault' )->will( $this->returnValue( true ) );
        $oPaymentMock->expects( $this->once() )->method( 'getPaymorrowPaymentType' )->will(
            $this->returnValue( 'pm_sdd' )
        );

        oxTestModules::addModuleObject( 'OxpsPaymorrowOxPayment', $oPaymentMock );

        $this->assertSame( 'pm_sdd', $SUT->getSelectedPaymorrowMethod() );
    }

    public function testGetSelectedPaymorrowMethod_noValidMethodLoaded_returnEmptyString()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|OxpsPaymorrowInit $SUT */
        $SUT = $this->getMock( 'OxpsPaymorrowInit', array('init') );

        $this->setSessionParam('paymentid', 'some_method');

        // Payment model mock
        $oPaymentMock = $this->getMock(
            'OxpsPaymorrowOxPayment',
            array(
                '__construct', 'load', 'isPaymorrowActiveAndMapped', 'loadPaymorrowDefault', 'getPaymorrowPaymentType'
            )
        );
        $oPaymentMock->expects( $this->once() )->method( 'load' )->with( $this->equalTo( 'some_method' ) )->will(
            $this->returnValue( false )
        );
        $oPaymentMock->expects( $this->never() )->method( 'isPaymorrowActiveAndMapped' );
        $oPaymentMock->expects( $this->once() )->method( 'loadPaymorrowDefault' )->will( $this->returnValue( false ) );
        $oPaymentMock->expects( $this->once() )->method( 'getPaymorrowPaymentType' )->will( $this->returnValue( '' ) );

        oxTestModules::addModuleObject( 'OxpsPaymorrowOxPayment', $oPaymentMock );

        $this->assertSame( '', $SUT->getSelectedPaymorrowMethod() );
    }


    public function testGetPaymorrowPrintData_callForPrintDataFromOxidToPaymorrowClassAndReturnIt()
    {
        // Oxid2Paymorrow mock
        $oOxid2PaymorrowMock = $this->getMock( 'OxpsOxid2Paymorrow', array('__construct', 'getPaymorrowPrintData') );
        $oOxid2PaymorrowMock->expects( $this->once() )->method( 'getPaymorrowPrintData' )->will(
            $this->returnValue( 'jsPrint-Data' )
        );

        oxTestModules::addModuleObject( 'OxpsOxid2Paymorrow', $oOxid2PaymorrowMock );

        $SUT = $this->getProxyClass( 'OxpsPaymorrowInit' );

        $this->assertSame( 'jsPrint-Data', $SUT->_getPaymorrowPrintData() );
    }
}
