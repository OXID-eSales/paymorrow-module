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
 * Class OxpsPaymorrowErrorHandler.
 */
class OxpsPaymorrowErrorHandler extends OxpsPaymorrowModule
{

    /**
     * List of public error codes.
     *
     * @var array
     */
    protected $_aPublicErrors = array(

        // Payment form submission/validation/other errors
        3000 => 'GENERAL_ERROR',
    );


    /**
     * Get human readable error message by error code.
     *
     * @param integer $iErrorCode
     *
     * @return string
     */
    public function getErrorByCode( $iErrorCode )
    {
        return array_key_exists( $iErrorCode, $this->_aPublicErrors )
            ? $this->translateError( $this->_aPublicErrors[$iErrorCode] )
            : $this->translateError( $this->_aPublicErrors[3000] ); // If exact error not exist throw general
    }

    /**
     * Redirect user to given controller and shows an error.
     * In case of 'RELOAD_CONFIGURATION_REQUIRED' error, update module settings and redirect.
     *
     * @codeCoverageIgnore
     *
     * @param        $iErrorCode
     * @param string $sController
     */
    public function redirectWithError( $iErrorCode, $sController = 'order' )
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sErrorMessage = $this->getErrorByCode( $iErrorCode );

        // Set error
        $oEx = oxNew( 'oxExceptionToDisplay' );
        $oEx->setMessage( $sErrorMessage );
        oxRegistry::get( "oxUtilsView" )->addErrorToDisplay( $oEx, false );

        // Redirect (refresh page)
        $sUrl = $config->getShopCurrentUrl() . "cl=" . $sController;
        $sUrl = oxRegistry::get( "oxUtilsUrl" )->processUrl( $sUrl );
        oxRegistry::getUtils()->redirect( $sUrl );

        return;
    }

    /**
     * Translate Paymorrow errors.
     * Alias for module `translate` method.
     *
     * @param string $sError
     *
     * @return string
     */
    public function translateError( $sError )
    {
        return $this->translate( $sError );
    }
}
