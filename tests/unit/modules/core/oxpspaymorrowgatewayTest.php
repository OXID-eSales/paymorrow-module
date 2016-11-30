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
 * Class Unit_Module_Models_OxpsPaymorrowGatewayTest
 *
 * @see OxpsPaymorrowGateway
 */
class Unit_Module_Core_OxpsPaymorrowGatewayTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowGateway
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
        $this->SUT = $this->getMock( 'OxpsPaymorrowGateway', array('prepareResponseData') );
    }


    public function testCall_responseIsError_callErrorAndReturnProcessedResponseData()
    {
        $oResponseHandlerMock = $this->getMock(
            'OxpsPaymorrowResponseHandler',
            array('handleUpdateOrderIdResponseOK', 'handleUpdateOrderIdResponseError')
        );
        $oResponseHandlerMock->expects( $this->never() )->method( 'handleUpdateOrderIdResponseOK' );
        $oResponseHandlerMock->expects( $this->once() )->method( 'handleUpdateOrderIdResponseError' )->with(
            $this->equalTo( array('response_status' => 'error') )
        );

        oxRegistry::set( 'OxpsPaymorrowResponseHandler', $oResponseHandlerMock );

        $this->_setMocks(
            array('merchantId' => 'User1'),
            array('merchantId' => 'User1', 'order_id' => '001'),
            array('response_status' => 'error')
        );

        $this->assertSame(
            array('response_status' => 'error'),
            $this->SUT->updateOrderId( array('order_id' => '001') )
        );
    }

    public function testCall_responseIsOk_callSuccessAndReturnProcessedResponseData()
    {
        $oResponseHandlerMock = $this->getMock(
            'OxpsPaymorrowResponseHandler',
            array('handleUpdateOrderIdResponseOK', 'handleUpdateOrderIdResponseError')
        );
        $oResponseHandlerMock->expects( $this->once() )->method( 'handleUpdateOrderIdResponseOK' )->with(
            $this->equalTo( array('response_status' => 'OK') )
        );
        $oResponseHandlerMock->expects( $this->never() )->method( 'handleUpdateOrderIdResponseError' );

        oxRegistry::set( 'OxpsPaymorrowResponseHandler', $oResponseHandlerMock );

        $this->_setMocks(
            array('merchantId' => 'User1'),
            array('merchantId' => 'User1', 'order_id' => '001'),
            array('response_status' => 'OK')
        );

        $this->assertSame(
            array('response_status' => 'OK'),
            $this->SUT->updateOrderId( array('order_id' => '001') )
        );
    }


    /**
     * Create and set mocks for Paymorrow client, data provider, response handler and SUT.
     *
     * @param array $aAuthData
     * @param array $aRequestData
     * @param array $aResponseData
     */
    protected function _setMocks( $aAuthData, $aRequestData, $aResponseData, $mResponseHandlerMock = null )
    {
        $oClientMock = $this->getMock( 'PaymorrowClient', array('setEndPoint', 'sendRequest') );
        $oClientMock->expects( $this->once() )->method( 'setEndPoint' )->with(
            $this->equalTo( 'www.example.com/updateOrderId' )
        );
        $oClientMock->expects( $this->once() )->method( 'sendRequest' )->with( $this->equalTo( $aRequestData ) )->will(
            $this->returnValue( $aResponseData )
        );

        $oDataProviderMock = $this->getMock( 'OxpsPaymorrowEshopDataProvider', array('collectCommonData') );
        $oDataProviderMock->expects( $this->once() )->method( 'collectCommonData' )->will(
            $this->returnValue( $aAuthData )
        );

        $oOxid2PaymorrowMock = $this->getMock( 'OxpsOxid2Paymorrow', array('getEshopDataProvider') );
        $oOxid2PaymorrowMock->expects( $this->once() )->method( 'getEshopDataProvider' )->will(
            $this->returnValue( $oDataProviderMock )
        );

        oxTestModules::addModuleObject( 'OxpsOxid2Paymorrow', $oOxid2PaymorrowMock );

        $this->SUT->setPmClient( $oClientMock );
        $this->SUT->setEndPointUrl( 'www.example.com/' );
        $this->SUT->expects( $this->once() )->method( 'prepareResponseData' )
            ->with( $this->equalTo( $aResponseData ) )
            ->will( $this->returnArgument( 0 ) );
    }
}
