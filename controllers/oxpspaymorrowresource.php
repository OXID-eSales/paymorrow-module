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
 * Class OxpsPaymorrowResource
 */
class OxpsPaymorrowResource extends oxUBase
{

    /**
     * Get Paymorrow dynamic JavaScript.
     */
    public function getPaymorrowJavaScript()
    {
        $this->_getResource( '/pmfunc.js' );
    }

    /**
     * Get Paymorrow dynamic JavaScript.
     */
    public function getPaymorrowSessionMonitorJavaScript()
    {
        /**
         * From Paymorrow documentation:
         *
         * 6.5 Paymorrow session monitoring
         * Paymorrow needs to monitor customer’s behavior in the eshop during the full shopping session for qualified decision whether accept or decline customer’s order.
         * Paymorrow requires for this session monitoring to place on every single page session monitroring JS.
         * <script type="text/javascript" src="pmResource.php?path=/pmsession.js&amp;session_id=xxxxx"></script>
         * session_id is customer’s http sessionId which is sent to prepareOrder as attribute client_browser_session_id.
         * This pmsession.js must NOT be cached.
         */
        $this->_getResource( '/pmsession.js?session_id=' . oxRegistry::getSession()->getId(), true );
    }

    /**
     * Get Paymorrow dynamic css.
     */
    public function getPaymorrowCSS()
    {
        $this->_getResource( '/css/pmstyle.css' );
    }

    /**
     * Get Paymorrow dynamic JavaScript for module pages in admin back-end.
     */
    public function getPaymorrowAdminJavaScript()
    {
        $this->_getResource( '/pmadminfunc.js' );
    }

    /**
     * Get Paymorrow dynamic CSS for module pages in admin back-end.
     */
    public function getPaymorrowAdminCss()
    {
        $this->_getResource( '/css/pmadminstyle.css' );
    }


    /**
     * Get a resource by path and output the resource content.
     *
     * @param string $sResourcePath
     * @param bool   $blNoCache     forces not to cache resource
     */
    protected function _getResource( $sResourcePath, $blNoCache = false )
    {
        $aResponse = null;

        if ( !$blNoCache ) {
            /** @var OxpsPaymorrowResourceCache $oResourceCache */
            $oResourceCache = oxNew( 'OxpsPaymorrowResourceCache' );
            $aResponse = $oResourceCache->pop( $sResourcePath );
        }

        if ( empty( $aResponse ) ) {

            /** @var OxpsOxid2Paymorrow $oOxidToPm */
            $oOxidToPm = oxNew( 'OxpsOxid2Paymorrow' );
            $aResponse = $oOxidToPm->getBuiltPaymorrowResourceProxy()->getResource( $sResourcePath );

            if ( !$blNoCache ) {
                $oResourceCache->push( $sResourcePath, $aResponse );
            }
        }

        $this->_resourceResponse( $aResponse );
    }

    /**
     * Sent response headers, content and stop execution to prevent defaults.
     *
     * @codeCoverageIgnore
     *
     * @param array $aResponse
     */
    protected function _resourceResponse( array $aResponse )
    {
        if ( isset( $aResponse['contentType'] ) ) {
            oxRegistry::getUtils()->setHeader( 'Content-Type: ' . $aResponse['contentType'] );
        }

        if ( isset( $aResponse['body'] ) ) {
            print( $aResponse['body'] );
        }

        exit();
    }
}
