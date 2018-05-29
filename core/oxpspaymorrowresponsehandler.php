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
 * Class OxpsPaymorrowResponseHandler.
 *
 * @see PaymorrowWsResponseHandler
 */
class OxpsPaymorrowResponseHandler extends oxSuperCfg //implements PaymorrowWsResponseHandler
{

    /**
     * OxpsPaymorrowResponseHandler instance used by oxRegistry
     *
     * @var OxpsPaymorrowResponseHandler
     */
    private static $_instance = null;

    /**
     * Error code after Paymorrow validation.
     *
     * @var integer
     */
    private $_iErrorCode = null;

    /**
     * Response data.
     *
     * @var array
     */
    private $aResponse = array();


    /**
     * Magic call method to handle default workflow.
     * Methods ending with "OK" are treated as successful response.
     * Message ending with "Error" are errors.
     *
     * @param string $sMethodName
     * @param array  $aArguments
     *
     * @return bool|null
     */
    public function __call( $sMethodName, $aArguments )
    {
        mb_internal_encoding( 'UTF-8' );

        $iMethodNameLength = mb_strlen( $sMethodName );

        if ( mb_substr( $sMethodName, ( $iMethodNameLength - 2 ), 2 ) === 'OK' ) {

            // Success response workflow
            $aResponse = (array) reset( $aArguments );
            oxRegistry::get( 'OxpsPaymorrowLogger' )->logWithType( $aResponse, 'PM_' . $sMethodName );
            $this->setResponse( $aResponse );

            return true;
        } elseif ( mb_substr( $sMethodName, ( $iMethodNameLength - 5 ), 5 ) === 'Error' ) {

            // Error response workflow
            oxRegistry::get( 'OxpsPaymorrowLogger' )->logWithType( (array) reset( $aArguments ), 'PM_' . $sMethodName );

            return false;
        }

        return null;
    }


    /**
     * Set response data.
     *
     * @param array $aResponse
     */
    public function setResponse( $aResponse )
    {
        $this->aResponse = $aResponse;
    }

    /**
     * Get response data.
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->aResponse;
    }

    /**
     * Set Paymorrow error code.
     *
     * @param integer $iErrorCode
     */
    public function setErrorCode( $iErrorCode )
    {
        $this->_iErrorCode = $iErrorCode;
    }

    /**
     * Get Paymorrow error code.
     *
     * @return integer
     */
    public function getErrorCode()
    {
        return $this->_iErrorCode;
    }

    /**
     * Check if order contains errors.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return (bool) $this->getErrorCode();
    }

    /**
     * Check is order was declined.
     *
     * @return bool
     */
    public function wasDeclined()
    {
        $aResponse = $this->getResponse();

        if ( !empty( $aResponse['order_status'] ) and $aResponse['order_status'] == 'DECLINED' ) {
            return true;
        }

        return false;
    }

    /**
     * Check of order was accepted with no error codes.
     *
     * @return bool
     */
    public function wasAccepted()
    {
        if ( $this->hasErrors() ) {
            return false;
        }

        $aResponse = $this->getResponse();

        if ( !empty( $aResponse['pm_order_status'] ) && $aResponse['pm_order_status'] === 'ACCEPTED' ) {
            return true;
        }

        return false;
    }

    /**
     * Returns first match of client error code.
     *
     * @param array $aResponseData
     *
     * @return integer
     */
    public function getErrorCodeFromResponseData( array $aResponseData )
    {
        foreach ( $aResponseData as $sKey => $iValue ) {
            if ( preg_match( '/error_(\d+)_code/', $sKey ) ) {
                return (int) $iValue;
            }
        }

        return 999; // UNEXPECTED_ERROR
    }

    /**
     * Collects payment method data from order declination response.
     *
     * @return array
     */
    public function getDeclinationDataFromResponse()
    {
        $aDeclinationData = array();
        $aResponse        = $this->getResponse();
        $aExpectedKeys    = array('paymentMethod_name', 'paymentMethod_status', 'paymentMethod_declineType');

        foreach ( $aExpectedKeys as $sKey ) {
            if ( array_key_exists( $sKey, $aResponse ) ) {
                $aDeclinationData[$sKey] = $aResponse[$sKey];
            }
        }

        return $aDeclinationData;
    }

    /**
     * Collects error(s) data from erroneous order response.
     *
     * @return array
     */
    public function getErrorDataFromResponse()
    {
        $aErrorData = array();
        $aResponse  = (array) $this->getResponse();

        foreach ( $aResponse as $sKey => $sValue ) {
            if ( mb_strpos( $sKey, 'error_', null, 'UTF-8' ) === 0 ) {
                $aErrorData[$sKey] = $sValue;
            }
        }

        return $aErrorData;
    }

    /**
     * Parse getConfiguration call response for Paymorrow settings.
     *
     * @param array $aResponseData
     *
     * @return array Assoc array with key as setting name for each corresponding value.
     */
    public function parseGetConfigurationResponse( array $aResponseData )
    {
        $aConfigurationData = array();
        $iKey               = 1;
        $iMaxIterations     = 10000;

        while ( !empty( $aResponseData["pm_configuration_${iKey}_key"] ) and ( $iKey <= $iMaxIterations ) ) {
            $aConfigurationData[$aResponseData["pm_configuration_${iKey}_key"]] =
                isset( $aResponseData["pm_configuration_${iKey}_value"] )
                    ? trim( (string) $aResponseData["pm_configuration_${iKey}_value"] )
                    : '';
            $iKey++;
        }

        return $aConfigurationData;
    }

    /**
     * Order preparation successful response handler.
     *
     * @param array $aResponseData
     *
     * @return bool
     */
    public function handlePrepareOrderResponseOK( $aResponseData )
    {
        oxRegistry::get( 'OxpsPaymorrowLogger' )->logWithType( $aResponseData, 'PM_handlePrepareOrderResponseOK' );
        $this->setResponse( $aResponseData );

        // Trigger user profile update using normalized response data
        oxRegistry::get( 'OxpsPaymorrowModule' )->updateProfile( $aResponseData );

        return true;
    }

    /**
     * Order preparation error response handler.
     *
     * @param array $responseData
     *
     * @return bool
     */
    public function handlePrepareOrderResponseError( $responseData )
    {
        oxRegistry::get( 'OxpsPaymorrowLogger' )->logWithType( $responseData, 'PM_handlePrepareOrderResponseError' );

        return false;
    }

    /**
     * Order confirmation successful response handler.
     *
     * @param array $responseData
     *
     * @return bool
     */
    public function handleConfirmOrderResponseOK( $responseData )
    {
        oxRegistry::get( 'OxpsPaymorrowLogger' )->logWithType( $responseData, 'PM_handleConfirmOrderResponseOK' );
        $this->setResponse( $responseData );

        return true;
    }

    /**
     * Order confirmation error response handler.
     *
     * @param array $responseData
     *
     * @return bool
     */
    public function handleConfirmOrderResponseError( $responseData )
    {
        oxRegistry::get( 'OxpsPaymorrowLogger' )->logWithType( $responseData, 'PM_handleConfirmOrderResponseError' );
        $this->setResponse( $responseData );
        $this->setErrorCode( $this->getErrorCodeFromResponseData( $responseData ) );

        return false;
    }
}
