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
 * Class Unit_Module_Controllers_OxpsOxid2PaymorrowTest
 *
 * @see OxpsOxid2Paymorrow
 */
class Unit_Module_Controllers_OxpsOxid2PaymorrowTest extends OxidTestCase
{

    /**
     * @var OxpsOxid2Paymorrow
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
            'OxpsOxid2Paymorrow',
            array(
                '__construct', 'load', 'init', '_paymorrowTemporaryFinalizeOrder',
                '_collectOrderData', '_sendPaymorrowRequest', 'printPmData',
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
     * @return OxpsOxid2Paymorrow
     */
    protected function _getProxySUT( array $aParams = array() )
    {
        return $this->getProxyClass( 'OxpsOxid2Paymorrow', $aParams );
    }


    public function test_getPaymorrowSettings_shouldReturnPaymorrowSettingsObject()
    {
        $this->assertTrue( $this->_getProxySUT()->getPaymorrowSettings() instanceof OxpsPaymorrowSettings );
    }


    public function test_getEshopDataProvider_shouldReturnEshopDataProviderInstance()
    {
        $this->assertTrue( $this->_getProxySUT()->getEshopDataProvider() instanceof OxpsPaymorrowEshopDataProvider );
    }

    public function testBuildPaymorrowGateway_returnInitializedPaymorrowGatewayInstance()
    {
        // Paymorrow client mock
        $oClientMock = $this->getMock( 'OxpsPaymorrowClient', array('setPrivateKey', 'setPaymorrowPublicKey') );
        oxTestModules::addModuleObject( 'OxpsPaymorrowClient', $oClientMock );

        // Data provider mock
        $oDataProviderMock = $this->getMock( 'OxpsPaymorrowEshopDataProvider' );
        oxTestModules::addModuleObject( 'OxpsPaymorrowEshopDataProvider', $oDataProviderMock );

        // Paymorrow settings mock
        $oSettingsMock = $this->getMock( 'OxpsPaymorrowSettings', array('getEndPointURL') );
        $oSettingsMock->expects( $this->once() )->method( 'getEndPointURL' )->will(
            $this->returnValue( 'example.com/services/' )
        );
        oxTestModules::addModuleObject( 'OxpsPaymorrowSettings', $oSettingsMock );

        // Response handler mock
        $oResponseHandlerMock = $this->getMock( 'OxpsPaymorrowResponseHandler' );
        oxRegistry::set( 'OxpsPaymorrowResponseHandler', $oResponseHandlerMock );

        // Paymorrow gateway mock
        $oGatewayMock = $this->getMock(
            'OxpsPaymorrowGateway',
            array('setPmClient', 'setEshopDataProvider', 'setEndPointUrl', 'setResponseHandler')
        );
        $oGatewayMock->expects( $this->once() )->method( 'setPmClient' )->with( $this->equalTo( $oClientMock ) );
        $oGatewayMock->expects( $this->once() )->method( 'setEshopDataProvider' )->with(
            $this->equalTo( $oDataProviderMock )
        );
        $oGatewayMock->expects( $this->once() )->method( 'setEndPointUrl' )->with(
            $this->equalTo( 'example.com/services/' )
        );
        $oGatewayMock->expects( $this->once() )->method( 'setResponseHandler' )->with(
            $this->equalTo( $oResponseHandlerMock )
        );
        oxTestModules::addModuleObject( 'OxpsPaymorrowGateway', $oGatewayMock );

        $this->assertSame( $oGatewayMock, $this->_getProxySUT()->buildPaymorrowGateway() );
    }


    public function test_getPaymorrowGateway_shouldReturnPaymorrowGatewayObject()
    {
        $this->assertTrue( $this->_getProxySUT()->getPaymorrowGateway() instanceof OxpsPaymorrowGateway );
    }


    public function test_getPaymorrowClient_shouldReturnPaymorrowClientObject()
    {
        $this->assertTrue( $this->_getProxySUT()->getPaymorrowClient() instanceof OxpsPaymorrowClient );
    }


    public function test_getBuiltPaymorrowResourceProxy_shouldReturnPaymorrowResourceProxyInstance()
    {
        $this->assertTrue( $this->SUT->getBuiltPaymorrowResourceProxy() instanceof PaymorrowResourceProxy );
    }


    /**
     * @param array $aMethods
     *
     * @return oxUser
     */
    protected function _getUserMock( array $aMethods = array() )
    {
        return $this->getMock( 'oxuser', array_merge( array('load', '__construct', 'init'), $aMethods ) );
    }

    /**
     * @param array $aMethods
     *
     * @return oxBasket
     */
    protected function _getBasketMock( array $aMethods = array() )
    {
        return $this->getMock( 'oxbasket', array_merge( array('load', '__construct', 'init'), $aMethods ) );
    }


    public function test_getPaymorrowErrorHandler_shouldReturnErrorHandlerInstance()
    {
        $this->assertTrue( $this->SUT->getPaymorrowErrorHandler() instanceof OxpsPaymorrowErrorHandler );
    }


    public function test_getPaymorrowRequestController_shouldReturnRequestControllerInstance()
    {
        $this->assertTrue( $this->SUT->getPaymorrowRequestController() instanceof requestcontroller );
    }


    public function testGetBuiltPaymorrowRequestController_returnRequestControllerInstanceWithGatewayBuiltAndSet()
    {
        // Gateway mock
        $oGatewayMock = $this->getMock( 'OxpsPaymorrowGateway' );

        // Paymorrow request controller mock
        $oRequestControllerMock = $this->getMock( 'RequestController', array('setGateway') );
        $oRequestControllerMock->expects( $this->once() )->method( 'setGateway' )->with(
            $this->equalTo( $oGatewayMock )
        );
        oxTestModules::addModuleObject( 'RequestController', $oRequestControllerMock );

        // SUT mock
        $SUT = $this->getMock( 'OxpsOxid2Paymorrow', array('buildPaymorrowGateway') );
        $SUT->expects( $this->once() )->method( 'buildPaymorrowGateway' )->will(
            $this->returnValue( $oGatewayMock )
        );

        $this->assertSame( $oRequestControllerMock, $SUT->getBuiltPaymorrowRequestController() );
    }
}
