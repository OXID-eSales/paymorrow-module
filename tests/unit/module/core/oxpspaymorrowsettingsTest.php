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
 * Class Unit_Module_Controllers_OxpsPaymorrowSettingsTest
 *
 * @see OxpsPaymorrowSettings
 */
class Unit_Module_Controllers_OxpsPaymorrowSettingsTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowSettings
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
        $this->SUT = $this->getMock( 'OxpsPaymorrowSettings', array('__construct') );
    }


    /**
     * Mocking Proxy Class to test Protected methods
     *
     * IDE - might underline methods with RED color, PLEASE IGNORE
     *
     * @param array $aParams
     *
     * @return oxpspaymorrowsettings
     */
    protected function _getProxySUT( array $aParams = array() )
    {
        return $this->getProxyClass( 'OxpsPaymorrowSettings', $aParams );
    }


    public function testGetMerchantID_nothingSet_returnEmptyString()
    {
        $this->setConfigParam( 'paymorrowSandboxMode', '' );
        $this->setConfigParam( 'paymorrowMerchantId', '' );
        $this->setConfigParam( 'paymorrowMerchantIdTest', '' );

        $this->assertSame( '', $this->SUT->getMerchantId() );
    }

    public function testGetMerchantID_sandboxModeIsOff_returnLiveMerchantId()
    {
        $this->setConfigParam( 'paymorrowSandboxMode', 0 );
        $this->setConfigParam( 'paymorrowMerchantId', 'Merch1-Live' );
        $this->setConfigParam( 'paymorrowMerchantIdTest', 'Merch1-Test' );

        $this->assertSame( 'Merch1-Live', $this->SUT->getMerchantId() );
    }

    public function testGetMerchantID_sandboxModeIsOn_returnLiveMerchantId()
    {
        $this->setConfigParam( 'paymorrowSandboxMode', 1 );
        $this->setConfigParam( 'paymorrowMerchantId', 'Merch1-Live' );
        $this->setConfigParam( 'paymorrowMerchantIdTest', 'Merch1-Test' );

        $this->assertSame( 'Merch1-Test', $this->SUT->getMerchantId() );
    }


    public function testGetPrivateKey_nothingSet_returnEmptyString()
    {
        $this->setConfigParam( 'paymorrowSandboxMode', '' );
        $this->setConfigParam( 'paymorrowPrivateKey', '' );
        $this->setConfigParam( 'paymorrowPrivateKeyTest', '' );

        $this->assertSame( '', $this->SUT->getPrivateKey() );
    }

    public function testGetPrivateKey_sandboxModeIsOff_returnPrivateKeyForLiveMode()
    {
        $this->setConfigParam( 'paymorrowSandboxMode', 0 );
        $this->setConfigParam( 'paymorrowPrivateKey', 'TElWRS1LRVk=' );
        $this->setConfigParam( 'paymorrowPrivateKeyTest', 'VEVTVC1LRVk=' );

        $this->assertSame( 'LIVE-KEY', $this->SUT->getPrivateKey() );
    }

    public function testGetPrivateKey_sandboxModeIsOn_returnPrivateKeyForTestMode()
    {
        $this->setConfigParam( 'paymorrowSandboxMode', 1 );
        $this->setConfigParam( 'paymorrowPrivateKey', 'TElWRS1LRVk=' );
        $this->setConfigParam( 'paymorrowPrivateKeyTest', 'VEVTVC1LRVk=' );

        $this->assertSame( 'TEST-KEY', $this->SUT->getPrivateKey() );
    }


    public function testGetPublicKey_nothingSet_returnEmptyString()
    {
        $this->setConfigParam( 'paymorrowSandboxMode', '' );
        $this->setConfigParam( 'paymorrowPublicKey', '' );
        $this->setConfigParam( 'paymorrowPublicKeyTest', '' );

        $this->assertSame( '', $this->SUT->getPublicKey() );
    }

    public function testGetPublicKey_sandboxModeIsOff_returnPublicKeyForLiveMode()
    {
        $this->setConfigParam( 'paymorrowSandboxMode', 0 );
        $this->setConfigParam( 'paymorrowPublicKey', 'TElWRS1DRVJU' );
        $this->setConfigParam( 'paymorrowPublicKeyTest', 'VEVTVC1DRVJU' );

        $this->assertSame( 'LIVE-CERT', $this->SUT->getPublicKey() );
    }

    public function testGetPublicKey_sandboxModeIsOn_returnPublicKeyForTestMode()
    {
        $this->setConfigParam( 'paymorrowSandboxMode', 1 );
        $this->setConfigParam( 'paymorrowPublicKey', 'TElWRS1DRVJU' );
        $this->setConfigParam( 'paymorrowPublicKeyTest', 'VEVTVC1DRVJU' );

        $this->assertSame( 'TEST-CERT', $this->SUT->getPublicKey() );
    }


    public function testGetPaymorrowKey_nothingSet_returnEmptyString()
    {
        $this->setConfigParam( 'paymorrowSandboxMode', '' );
        $this->setConfigParam( 'paymorrowPaymorrowKey', '' );
        $this->setConfigParam( 'paymorrowPaymorrowKeyTest', '' );

        $this->assertSame( '', $this->SUT->getPaymorrowKey() );
    }

    public function testGetPaymorrowKey_sandboxModeIsOff_returnPaymorrowPublicKeyForLiveMode()
    {
        $this->setConfigParam( 'paymorrowSandboxMode', 0 );
        $this->setConfigParam( 'paymorrowPaymorrowKey', 'TElWRS1QTQ==' );
        $this->setConfigParam( 'paymorrowPaymorrowKeyTest', 'VEVTVC1QTQ==' );

        $this->assertSame( 'LIVE-PM', $this->SUT->getPaymorrowKey() );
    }

    public function testGetPaymorrowKey_sandboxModeIsOn_returnPaymorrowPublicKeyForTestMode()
    {
        $this->setConfigParam( 'paymorrowSandboxMode', 1 );
        $this->setConfigParam( 'paymorrowPaymorrowKey', 'TElWRS1QTQ==' );
        $this->setConfigParam( 'paymorrowPaymorrowKeyTest', 'VEVTVC1QTQ==' );

        $this->assertSame( 'TEST-PM', $this->SUT->getPaymorrowKey() );
    }


    public function test_getSetEndpointUrl_shouldReturnSetEndPointProductionUrl()
    {
        $sEndPointUrl = 'end_point_test_url3';

        $this->setConfigParam( 'paymorrowEndpointUrlProd', $sEndPointUrl );

        $this->assertEquals( $sEndPointUrl, $this->SUT->getProductionEndPointURL() );
    }


    public function testGetTestEndPointURL_returnSetTestEndpointUrlSettingValue()
    {
        $this->setConfigParam( 'paymorrowEndpointUrlTest', 'http://example.com/resource.asp?v=22' );

        $this->assertEquals( 'http://example.com/resource.asp?v=22', $this->SUT->getTestEndPointURL() );
    }


    public function testIsLoggingEnabled_loggingSettingDisabled_returnFalse()
    {
        $this->setConfigParam( 'paymorrowLoggingEnabled', 0 );

        $this->assertFalse( $this->SUT->isLoggingEnabled() );
    }

    public function testIsLoggingEnabled_loggingSettingEnabled_returnTrue()
    {
        $this->setConfigParam( 'paymorrowLoggingEnabled', true );

        $this->assertTrue( $this->SUT->isLoggingEnabled() );
    }


    public function test_ifIsSandboxMode_shouldReturnFalse()
    {
        $this->setConfigParam( 'paymorrowSandboxMode', '0' );

        $this->assertFalse( $this->SUT->isSandboxMode() );
    }


    public function test_ifIsSanboxMode_shouldReturnTrue()
    {
        $this->setConfigParam( 'paymorrowSandboxMode', '1' );

        $this->assertTrue( $this->SUT->isSandboxMode() );
    }


    public function test_getValidSettings_shouldReturnSameArray()
    {
        $aSettings = array(
            'SandboxMode',
            'MerchantId',
            'MerchantIdTest',
            'PrivateKey',
            'PrivateKeyTest',
            'PublicKey',
            'PublicKeyTest',
            'PaymorrowKey',
            'PaymorrowKeyTest',
            'EndpointUrlTest',
            'EndpointUrlProd',
            'LoggingEnabled',
            'ResourcePath',
            'ResourcePathTest',
            'OperationMode',
            'OperationModeTest',
            'UpdateAddresses',
            'UpdatePhones',
        );

        $this->assertSame( $aSettings, $this->SUT->getValidSettings() );
    }


    public function test_getEndPointURL_shouldReturnProductionUrlWhenSandboxModeIsOff()
    {
        $sSetting = 'this_is_production_url_the_end';

        $this->setConfigParam( 'paymorrowSandboxMode', '0' );
        $this->setConfigParam( 'paymorrowEndpointUrlProd', $sSetting );

        $this->assertEquals( $sSetting, $this->SUT->getEndPointURL() );
    }


    public function testGetPaymorrowResourcePath_liveModeIsOn_returnLiveResourcePath()
    {
        $sPath = 'test-path_kafjefaseahrd33';
        $this->setConfigParam( 'paymorrowResourcePath', $sPath );
        $this->setConfigParam( 'paymorrowResourcePathTest', $sPath . 'Test' );
        $this->setConfigParam( 'paymorrowSandboxMode', '0' );

        $this->assertSame( $sPath, $this->SUT->getPaymorrowResourcePath() );
    }

    public function testGetPaymorrowResourcePath_testModeIsOn_returnTestResourcePath()
    {
        $sPath = 'test-path_kafjefaseahrd33';
        $this->setConfigParam( 'paymorrowResourcePath', $sPath );
        $this->setConfigParam( 'paymorrowResourcePathTest', $sPath . 'Test' );
        $this->setConfigParam( 'paymorrowSandboxMode', '1' );

        $this->assertSame( $sPath . 'Test', $this->SUT->getPaymorrowResourcePath() );
    }

    public function testGetPaymorrowOperationMode_liveModeIsOn_returnLiveOperationMode()
    {
        $this->setConfigParam( 'paymorrowOperationMode', 'VALIDATE' );
        $this->setConfigParam( 'paymorrowOperationModeTest', 'RISK_CHECK' );
        $this->setConfigParam( 'paymorrowSandboxMode', '0' );

        $this->assertSame( 'VALIDATE', $this->SUT->getPaymorrowOperationMode() );
    }

    public function testGetPaymorrowOperationMode_testModeIsOn_returnTestOperationMode()
    {
        $this->setConfigParam( 'paymorrowOperationMode', 'VALIDATE' );
        $this->setConfigParam( 'paymorrowOperationModeTest', 'RISK_CHECK' );
        $this->setConfigParam( 'paymorrowSandboxMode', '1' );

        $this->assertSame( 'RISK_CHECK', $this->SUT->getPaymorrowOperationMode() );
    }


    public function test_getEndPointURL_shouldReturnTestUrlWhenSandboxModeIsOn()
    {
        $sSetting = 'this_is_test_environment_url_the_end';

        $this->setConfigParam( 'paymorrowSandboxMode', '1' );
        $this->setConfigParam( 'paymorrowEndpointUrlTest', $sSetting );

        $this->assertEquals( $sSetting, $this->SUT->getEndPointURL() );
    }


    public function testIsAddressesUpdateEnabled_falseLikeValueSet_returnFalse()
    {
        $this->setConfigParam( 'paymorrowUpdateAddresses', '' );

        $this->assertFalse( $this->SUT->isAddressesUpdateEnabled() );
    }

    public function testIsAddressesUpdateEnabled_trueLikeValueSet_returnTrue()
    {
        $this->setConfigParam( 'paymorrowUpdateAddresses', '1' );

        $this->assertTrue( $this->SUT->isAddressesUpdateEnabled() );
    }


    public function testIsPhonesUpdateEnabled_falseLikeValueSet_returnFalse()
    {
        $this->setConfigParam( 'paymorrowUpdatePhones', '0' );

        $this->assertFalse( $this->SUT->isPhonesUpdateEnabled() );
    }

    public function testIsPhonesUpdateEnabled_trueLikeValueSet_returnTrue()
    {
        $this->setConfigParam( 'paymorrowUpdatePhones', 'true' );

        $this->assertTrue( $this->SUT->isPhonesUpdateEnabled() );
    }


    public function test_getSetting_shouldReturnNullBecauseRequestedSettingIsNotValid()
    {
        $this->assertNull( $this->SUT->getSetting( 'requesting_invalid_settings' ) );
    }


    public function test_getMpiSignature_shouldReturnConstructedMpiSignature()
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sStartsWith = 'Oxid-' . $config->getVersion();

        $this->assertStringStartsWith( $sStartsWith, $this->SUT->getMpiSignature() );
    }


    public function test_getSettings_shouldReturnSetSettingsAsArray()
    {
        $sMerchantID        = 'Mock_setting_MerchantID';
        $sMerchantIDTest    = 'Mock_setting_MerchantIDTest';
        $sPrivateKey        = 'RSA KEY';
        $sPrivateKeyTest    = 'RSA KEY test';
        $sPublicKey         = 'CERTIFICATE';
        $sPublicKeyTest     = 'CERTIFICATE test';
        $sPaymorrowKey      = 'PM CERTIFICATE test';
        $sPaymorrowKeyTest  = 'PM CERTIFICATE test';
        $sEndPointUrlTest   = 'Mock_setting_EndPointUrl - Test';
        $sEndPointUrlProd   = 'Mock_setting_EndPointUrl - Production';
        $sSandboxMode       = '1';
        $sLoggingEnabled    = '1';
        $sResourcePath      = 'awfiawf';
        $sResourcePathTest  = 'test.awfiawf';
        $sOperationMode     = 'RISK_CHECK';
        $sOperationModeTest = 'RISK_PRECHECK';
        $blUpdateAddresses  = true;
        $blUpdatePhones     = false;

        $this->setConfigParam( 'paymorrowMerchantId', $sMerchantID );
        $this->setConfigParam( 'paymorrowMerchantIdTest', $sMerchantIDTest );
        $this->setConfigParam( 'paymorrowPrivateKey', $sPrivateKey );
        $this->setConfigParam( 'paymorrowPrivateKeyTest', $sPrivateKeyTest );
        $this->setConfigParam( 'paymorrowPublicKey', $sPublicKey );
        $this->setConfigParam( 'paymorrowPublicKeyTest', $sPublicKeyTest );
        $this->setConfigParam( 'paymorrowPaymorrowKey', $sPaymorrowKey );
        $this->setConfigParam( 'paymorrowPaymorrowKeyTest', $sPaymorrowKeyTest );
        $this->setConfigParam( 'paymorrowEndpointUrlTest', $sEndPointUrlTest );
        $this->setConfigParam( 'paymorrowEndpointUrlProd', $sEndPointUrlProd );
        $this->setConfigParam( 'paymorrowSandboxMode', $sSandboxMode );
        $this->setConfigParam( 'paymorrowLoggingEnabled', $sLoggingEnabled );
        $this->setConfigParam( 'paymorrowResourcePath', $sResourcePath );
        $this->setConfigParam( 'paymorrowResourcePathTest', $sResourcePathTest );
        $this->setConfigParam( 'paymorrowOperationMode', $sOperationMode );
        $this->setConfigParam( 'paymorrowOperationModeTest', $sOperationModeTest );
        $this->setConfigParam( 'paymorrowUpdateAddresses', $blUpdateAddresses );
        $this->setConfigParam( 'paymorrowUpdatePhones', $blUpdatePhones );

        $aArrayToCompare = array(
            'SandboxMode'       => $sSandboxMode,
            'MerchantId'        => $sMerchantID,
            'MerchantIdTest'    => $sMerchantIDTest,
            'PrivateKey'        => $sPrivateKey,
            'PrivateKeyTest'    => $sPrivateKeyTest,
            'PublicKey'         => $sPublicKey,
            'PublicKeyTest'     => $sPublicKeyTest,
            'PaymorrowKey'      => $sPaymorrowKey,
            'PaymorrowKeyTest'  => $sPaymorrowKeyTest,
            'EndpointUrlTest'   => $sEndPointUrlTest,
            'EndpointUrlProd'   => $sEndPointUrlProd,
            'LoggingEnabled'    => $sLoggingEnabled,
            'ResourcePath'      => $sResourcePath,
            'ResourcePathTest'  => $sResourcePathTest,
            'OperationMode'     => $sOperationMode,
            'OperationModeTest' => $sOperationModeTest,
            'UpdateAddresses'   => $blUpdateAddresses,
            'UpdatePhones'      => $blUpdatePhones,
        );

        $this->assertSame( $aArrayToCompare, $this->SUT->getSettings() );
    }
}
