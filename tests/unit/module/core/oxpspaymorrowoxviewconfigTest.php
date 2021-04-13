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
 * Class Unit_Module_Core_OxpsPaymorrowOxViewConfigTest
 *
 * @see OxpsPaymorrowOxViewConfig
 */
class Unit_Module_Core_OxpsPaymorrowOxViewConfigTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowOxViewConfig
     */
    protected $SUT;


    /**
     * Set initial objects state.
     *
     * @return null|void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // SUT mock
        $this->SUT = new OxpsPaymorrowOxViewConfig();
    }


    public function testGetPaymorrowMerchantId_callModuleSettingsClassGetMerchantIdMethod()
    {
        $oSettingsMock = $this->getMock( 'OxpsPaymorrowSettings', array('getMerchantId') );
        $oSettingsMock->expects( $this->once() )->method( 'getMerchantId' )->will( $this->returnValue( 'my_ID' ) );
        oxRegistry::set( 'OxpsPaymorrowSettings', $oSettingsMock );

        $this->assertSame( 'my_ID', $this->SUT->getPaymorrowMerchantId() );
    }


    public function testGetActiveInterfaceLanguageAbbr_callServerUtilsForOxidAdminLanguageCookieValueAndReturnIt()
    {
        $oServerUtilsMock = $this->getMock( 'oxUtilsServer', array('__construct', '__call', 'getOxCookie') );
        $oServerUtilsMock->expects( $this->once() )->method( 'getOxCookie' )
            ->with( $this->equalTo( 'oxidadminlanguage' ) )
            ->will( $this->returnValue( 'en' ) );
        oxRegistry::set( 'oxUtilsServer', $oServerUtilsMock );

        $this->assertSame( 'en', $this->SUT->getActiveInterfaceLanguageAbbr() );
    }
}
