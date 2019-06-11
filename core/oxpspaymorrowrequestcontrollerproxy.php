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
 * Class OxpsPaymorrowRequestControllerProxy.
 */
class OxpsPaymorrowRequestControllerProxy extends oxSuperCfg
{

    /**
     * Magic call method to redirect any call to Paymorrow gateway.
     *
     * @codeCoverageIgnore
     *
     * @param string $sMethodName
     * @param array  $aArguments
     *
     * @return string
     */
    public function __call( $sMethodName, $aArguments )
    {
        return $this->_getRequestController()->getGateway()->$sMethodName( (array) reset( $aArguments ) );
    }


    /**
     * Order preparation call.
     * Also perform configuration update on error code 900 received and calls the request again one time.
     *
     * @param array $aPostData
     * @param bool  $blSettingsUpdated If true, then settings update is not called again.
     *
     * @return string
     */
    public function prepareOrder( array $aPostData, $blSettingsUpdated = false )
    {
        // Update user profile with values user entered to Paymorrow form
        $this->_updateUserData( $aPostData );

        // Reset payment method in session and basket on its change
        $this->_resetPaymentMethod( $aPostData );

        // Send order preparation.verification request
        $aResponse = $this->_getRequestController()->pmVerify( $aPostData );
        oxRegistry::get( 'OxpsPaymorrowLogger' )->logWithType( $aPostData, 'Proxy-prepareOrderPOST' );

        /** @var OxpsPaymorrowResponseHandler $oResponseHandler */
        $oResponseHandler   = oxRegistry::get( 'OxpsPaymorrowResponseHandler' );
        $iResponseErrorCore = $oResponseHandler->getErrorCodeFromResponseData( $aResponse );

        // Check of response is an error with configuration update error code 900
        if ( ( $iResponseErrorCore === 900 ) and empty( $blSettingsUpdated ) ) {

            // Call module settings update in case error code 900 received
            oxRegistry::get( 'OxpsPaymorrowModule' )->updateSettings();

            // Call the request again
            return $this->prepareOrder( $aPostData, true );
        }

        return json_encode( $aResponse );
    }

    /**
     * Collect current basket data and make verification API call.
     * Method is independent from JS of front-end forms, so marks terms and conditions as accepted.
     * It should be used to re-validate already verified order, which user accepted on checkout payment step.
     *
     * @return bool
     */
    public function validatePendingOrder()
    {
        /** @var OxpsPaymorrowEshopDataProvider $dataProvider */
        $dataProvider = oxNew( 'OxpsPaymorrowEshopDataProvider' );
        $data = $dataProvider->collectEshopData();

        // Combine basket data and payment session data
        $session = \OxidEsales\Eshop\Core\Registry::getSession();
        $sessionData = (array) $session->getVariable( 'pm_verify' );
        $data = array_merge( $data, $sessionData );

        // Send order preparation/verification request
        $aResponse = $this->_getRequestController()->pmVerify( $data );
        oxRegistry::get( 'OxpsPaymorrowLogger' )->logWithType( $data, 'Proxy-prepareOrderPOST_reValidate' );

        /** @var OxpsPaymorrowResponseHandler $oResponseHandler */
        $oResponseHandler = oxRegistry::get( 'OxpsPaymorrowResponseHandler' );
        $oResponseHandler->setResponse( $aResponse );

        return (bool) $oResponseHandler->wasAccepted();
    }

    /**
     * Order confirmation call.
     *
     * @codeCoverageIgnore
     *
     * @param string      $sPaymentMethodName Paymorrow payment method name (code): 'INVOICE' or 'SDD'
     * @param null|string $sOrderId           Order identifier (OXID or order number)
     *
     * @return array Paymorrow response from curl validation
     */
    public function confirmOrder($sPaymentMethodName, $sOrderId = null)
    {
        return $this->_getRequestController()->pmConfirm($sPaymentMethodName, $sOrderId);
    }


    /**
     * Update user profile with data posted from Paymorrow form.
     *
     * @nice-to-have: Adjust unit tests to proxy class and make method protected.
     *
     * @param array $aPostData
     */
    public function _updateUserData( array $aPostData )
    {
        if ( $oUser = $this->_updateUserProfileData( $aPostData ) ) {
            $this->_updateUserActiveShippingAddress( $oUser, $aPostData );
        }
    }

    /**
     * Reset changed payment method selection in session for payment surcharge calculation to be valid.
     * If selected method is not the same as in session, it removes session data and adjusts basket calculation.
     *
     * @param array $aPostData
     */
    public function _resetPaymentMethod( array $aPostData )
    {
        $oSession = oxRegistry::getSession();

        if ( $oSession->getVariable( 'paymentid' ) and
             array_key_exists( 'paymentid', $aPostData ) and
             ( $oSession->getVariable( 'paymentid' ) != $aPostData['paymentid'] )
        ) {
            // Remove previous method from sessions
            $oSession->deleteVariable( 'paymentid' );

            // Adjust basket by removing payment surcharge and recalculating the basket
            $oBasket = $oSession->getBasket();
            $oBasket->setPayment();
            $oBasket->setCost( 'oxpayment' );
            $oBasket->calculateBasket( true );
        }
    }

    /**
     * Get built request controller.
     *
     * @return RequestController
     */
    protected function _getRequestController()
    {
        /** @var OxpsOxid2Paymorrow $oOxidToPm */
        $oOxidToPm = oxNew( 'OxpsOxid2Paymorrow' );

        return $oOxidToPm->getBuiltPaymorrowRequestController();
    }

    /**
     * Get valid, logged in user and update their profile data.
     *
     * @param array $aPostData
     *
     * @return bool|OxpsPaymorrowOxUser|oxUser User object if loaded, false otherwise.
     */
    protected function _updateUserProfileData( array $aPostData )
    {
        /** @var OxpsPaymorrowOxUser|oxUser $oUser */
        $oUser = $this->getUser();

        if ( empty( $oUser ) or !( $oUser instanceof oxUser ) or !$oUser->getId() ) {
            return false;
        }

        $oUser->mapToProfileDataAndUpdateUser( $aPostData );

        return $oUser;
    }

    /**
     * Get user active shipping address if it is used in the session and update it.
     *
     * @param OxpsPaymorrowOxUser|oxUser $oUser
     * @param array                      $aPostData
     *
     * @return bool
     */
    protected function _updateUserActiveShippingAddress( oxUser $oUser, array $aPostData )
    {
        /** @var oxAddress $oShippingAddress */
        $oShippingAddress      = $oUser->getSelectedAddress();
        $blShowShippingAddress = (bool) oxRegistry::getSession()->getVariable( 'blshowshipaddress' );

        if ( !$blShowShippingAddress or !( $oShippingAddress instanceof oxAddress ) or !$oShippingAddress->getId()
        ) {
            return false;
        }

        return $oUser->mapShippingDataAndUpdateAddress( $aPostData, $oShippingAddress );
    }
}
