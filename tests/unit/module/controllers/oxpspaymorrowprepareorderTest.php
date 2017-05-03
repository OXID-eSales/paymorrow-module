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
 * Class Unit_Module_Controllers_OxpsPaymorrowPrepareOrderTest
 *
 * @see OxpsPaymorrowPrepareOrder
 */
class Unit_Module_Controllers_OxpsPaymorrowPrepareOrderTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowPrepareOrder|PHPUnit_Framework_MockObject_MockObject
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
            'OxpsPaymorrowPrepareOrder',
            array('__construct', 'init', 'render', 'appendPostData')
        );
    }


    public function testPrepareOrder_makePrepareOrderCallAndExitWithJsonResponse()
    {
        $this->setRequestParameter('pm_paymentMethod_name', '');

        /** @var oxPayment|OxpsPaymorrowOxPayment|PHPUnit_Framework_MockObject_MockObject $payment */
        $payment = $this->getMock('oxPayment', array('__construct', '__call', 'loadByPaymorrowName', 'getId'));
        $payment->expects($this->once())->method('loadByPaymorrowName')->with('')->will($this->returnValue(false));
        $payment->expects($this->never())->method('getId');
        oxTestModules::addModuleObject('oxPayment', $payment);

        /** @var OxpsPaymorrowRequestControllerProxy|PHPUnit_Framework_MockObject_MockObject $oGatewayMock */
        $oGatewayMock = $this->getMock('OxpsPaymorrowRequestControllerProxy', array('__call', 'prepareOrder'));
        $oGatewayMock->expects($this->once())->method('prepareOrder')->with($this->isType('array'))->will(
            $this->returnValue('[JSON]')
        );
        oxTestModules::addModuleObject('OxpsPaymorrowRequestControllerProxy', $oGatewayMock);

        /** @var oxUtils|PHPUnit_Framework_MockObject_MockObject $oUtilsMock */
        $oUtilsMock = $this->getMock('oxUtils', array('setHeader', 'showMessageAndExit'));
        $oUtilsMock->expects($this->once())->method('setHeader')->with(
            $this->equalTo('Content-Type: application/json')
        );
        $oUtilsMock->expects($this->once())->method('showMessageAndExit')->with(
            $this->equalTo('[JSON]')
        );
        oxRegistry::set('oxUtils', $oUtilsMock);

        $this->SUT->expects($this->never())->method('appendPostData');
        $this->SUT->prepareOrder();
    }

    public function testPrepareOrder_paymentLoadedByName_setPaymentIdToPostData()
    {
        $this->setRequestParameter('pm_paymentMethod_name', 'INVOICE');
        $this->setRequestParameter('paymentid', '');

        /** @var oxPayment|OxpsPaymorrowOxPayment|PHPUnit_Framework_MockObject_MockObject $payment */
        $payment = $this->getMock('oxPayment', array('__construct', '__call', 'loadByPaymorrowName', 'getId'));
        $payment->expects($this->once())->method('loadByPaymorrowName')
            ->with('INVOICE')
            ->will($this->returnValue(true));
        $payment->expects($this->once())->method('getId')->will($this->returnValue('oxinvoiceid'));
        oxTestModules::addModuleObject('oxPayment', $payment);

        /** @var OxpsPaymorrowRequestControllerProxy|PHPUnit_Framework_MockObject_MockObject $oGatewayMock */
        $oGatewayMock = $this->getMock('OxpsPaymorrowRequestControllerProxy', array('__call', 'prepareOrder'));
        $oGatewayMock->expects($this->once())->method('prepareOrder')->with($this->isType('array'))->will(
            $this->returnValue('[JSON]')
        );
        oxTestModules::addModuleObject('OxpsPaymorrowRequestControllerProxy', $oGatewayMock);

        /** @var oxUtils|PHPUnit_Framework_MockObject_MockObject $oUtilsMock */
        $oUtilsMock = $this->getMock('oxUtils', array('setHeader', 'showMessageAndExit'));
        $oUtilsMock->expects($this->once())->method('setHeader')->with(
            $this->equalTo('Content-Type: application/json')
        );
        $oUtilsMock->expects($this->once())->method('showMessageAndExit')->with(
            $this->equalTo('[JSON]')
        );
        oxRegistry::set('oxUtils', $oUtilsMock);

        $this->SUT->expects($this->once())->method('appendPostData')->with(array('paymentid' => 'oxinvoiceid'));
        $this->SUT->prepareOrder();
    }
}
