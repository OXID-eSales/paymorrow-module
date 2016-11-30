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
 * Class Unit_Module_Controllers_OxpsPaymorrowResourceTest.
 *
 * @see OxpsPaymorrowResource
 */
class Unit_Module_Controllers_OxpsPaymorrowResourceTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowResource
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
        $this->SUT = $this->getMock( 'OxpsPaymorrowResource', array('init', 'render', '_resourceResponse') );
    }


    public function testGetPaymorrowJavaScript_getPmfuncJsResourceAndCallOutputMethod()
    {
        $this->_setResourceMocks( '/pmfunc.js' );

        $this->SUT->getPaymorrowJavaScript();
    }

    public function testGetPaymorrowSessionMonitorJavaScript_getPmfuncJsResourceAndCallOutputMethod()
    {
        $this->_setResourceMocks( '/pmsession.js?session_id=' . oxRegistry::getSession()->getId() );

        $this->SUT->getPaymorrowSessionMonitorJavaScript();
    }

    public function testGetPaymorrowCSS_getCssPmstyleCssResourceAndCallOutputMethod()
    {
        $this->_setResourceMocks( '/css/pmstyle.css' );

        $this->SUT->getPaymorrowCSS();
    }

    public function testGetPaymorrowAdminJavaScript_getPmAdminFuncJsResourceAndCallOutputMethod()
    {
        $this->_setResourceMocks( '/pmadminfunc.js' );

        $this->SUT->getPaymorrowAdminJavaScript();
    }

    public function testGetPaymorrowAdminCss_getPmAdminStyleCssResourceAndCallOutputMethod()
    {
        $this->_setResourceMocks( '/css/pmadminstyle.css' );

        $this->SUT->getPaymorrowAdminCss();
    }


    public function testGetResource_cacheHasNoData_callDataProxySaveDataToCacheAndCallResponseRender()
    {
        $SUT = $this->getMock( $this->getProxyClassName( 'OxpsPaymorrowResource' ), array('_resourceResponse') );
        $SUT->expects( $this->once() )->method( '_resourceResponse' )->with(
            $this->equalTo( array('contentType' => 'js', 'body' => '/* JS */') )
        );

        // Resource cache mock
        $oResourceCacheMock = $this->getMock( 'OxpsPaymorrowResourceCache', array('__construct', 'pop', 'push') );
        $oResourceCacheMock->expects( $this->once() )->method( 'pop' )
            ->with( $this->equalTo( 'path/to/file.js' ) )
            ->will( $this->returnValue( false ) );
        $oResourceCacheMock->expects( $this->once() )->method( 'push' )->with(
            $this->equalTo( 'path/to/file.js' ),
            $this->equalTo( array('contentType' => 'js', 'body' => '/* JS */') )
        );

        oxTestModules::addModuleObject( 'OxpsPaymorrowResourceCache', $oResourceCacheMock );

        // Resource proxy mock
        $oResourceProxyMock = $this->getMock( 'PaymorrowResourceProxy', array('getResource') );
        $oResourceProxyMock->expects( $this->once() )->method( 'getResource' )
            ->with( $this->equalTo( 'path/to/file.js' ) )
            ->will( $this->returnValue( array('contentType' => 'js', 'body' => '/* JS */') ) );

        // Oxid to Paymorrow mock
        $oOxidToPmMock = $this->getMock( 'OxpsOxid2Paymorrow', array('getBuiltPaymorrowResourceProxy') );
        $oOxidToPmMock->expects( $this->once() )->method( 'getBuiltPaymorrowResourceProxy' )->will(
            $this->returnValue( $oResourceProxyMock )
        );

        oxTestModules::addModuleObject( 'OxpsOxid2Paymorrow', $oOxidToPmMock );

        $SUT->_getResource( 'path/to/file.js' );
    }

    public function testGetResource_cacheHasValidData_DoNotCallResourceProxyUseCacheDataToCallResponseRender()
    {
        $SUT = $this->getMock( $this->getProxyClassName( 'OxpsPaymorrowResource' ), array('_resourceResponse') );
        $SUT->expects( $this->once() )->method( '_resourceResponse' )->with(
            $this->equalTo( array('contentType' => 'css', 'body' => '* {color: red;}') )
        );

        // Resource cache mock
        $oResourceCacheMock = $this->getMock( 'OxpsPaymorrowResourceCache', array('__construct', 'pop', 'push') );
        $oResourceCacheMock->expects( $this->once() )->method( 'pop' )
            ->with( $this->equalTo( 'path/to/style.css' ) )
            ->will( $this->returnValue( array('contentType' => 'css', 'body' => '* {color: red;}') ) );
        $oResourceCacheMock->expects( $this->never() )->method( 'push' );

        oxTestModules::addModuleObject( 'OxpsPaymorrowResourceCache', $oResourceCacheMock );

        // Resource proxy mock
        $oResourceProxyMock = $this->getMock( 'PaymorrowResourceProxy', array('getResource') );
        $oResourceProxyMock->expects( $this->never() )->method( 'getResource' );

        // Oxid to Paymorrow mock
        $oOxidToPmMock = $this->getMock( 'OxpsOxid2Paymorrow', array('getBuiltPaymorrowResourceProxy') );
        $oOxidToPmMock->expects( $this->any() )->method( 'getBuiltPaymorrowResourceProxy' )->will(
            $this->returnValue( $oResourceProxyMock )
        );

        oxTestModules::addModuleObject( 'OxpsOxid2Paymorrow', $oOxidToPmMock );

        $SUT->_getResource( 'path/to/style.css' );
    }


    /**
     * Set mocks for resource getters tests.
     *
     * @param string $sResourcePath
     */
    protected function _setResourceMocks( $sResourcePath )
    {
        // Resource cache mock
        $oResourceCacheMock = $this->getMock( 'OxpsPaymorrowResourceCache', array('__construct', 'pop', 'push') );

        oxTestModules::addModuleObject( 'OxpsPaymorrowResourceCache', $oResourceCacheMock );

        // Resource proxy mock
        $oResourceProxyMock = $this->getMock( 'PaymorrowResourceProxy', array('getResource') );
        $oResourceProxyMock->expects( $this->once() )->method( 'getResource' )
            ->with( $this->equalTo( (string) $sResourcePath ) )
            ->will( $this->returnValue( array('contentType' => 'foo', 'body' => 'bar') ) );

        // Oxid to Paymorrow mock
        $oOxidToPmMock = $this->getMock( 'OxpsOxid2Paymorrow', array('getBuiltPaymorrowResourceProxy') );
        $oOxidToPmMock->expects( $this->once() )->method( 'getBuiltPaymorrowResourceProxy' )->will(
            $this->returnValue( $oResourceProxyMock )
        );

        oxTestModules::addModuleObject( 'OxpsOxid2Paymorrow', $oOxidToPmMock );

        $this->SUT->expects( $this->once() )->method( '_resourceResponse' )->with(
            $this->equalTo( array('contentType' => 'foo', 'body' => 'bar') )
        );
    }
}
