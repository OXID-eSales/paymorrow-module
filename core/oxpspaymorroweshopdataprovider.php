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
 * Class OxpsPaymorrowEshopDataProvider.
 * This is a copy of sample implementation class vendor/paymorrow_gateway/EshopDataProviderImpl.php
 */
class OxpsPaymorrowEshopDataProvider extends oxSuperCfg //implements EshopDataProvider
{

    /** A static key used for order hash generation */
    const OXPS_PAYMORROW_ORDER_HASH_SALT = 'lf5-SGk5.gGj3nNJJbp';


    /**
     * Compiles an array with mandatory authentication data.
     *
     * @return array
     */
    public function collectCommonData()
    {
        /** @var OxpsPaymorrowSettings $oPmSettings */
        $oPmSettings = oxNew( 'OxpsPaymorrowSettings' );

        /** @var oxUser|OxpsPaymorrowOxUser $oUser */
        $oUser = $this->getUser();

        return array(
            'merchantId'           => $oPmSettings->getMerchantId(),
            'mpiSignature'         => $oPmSettings->getMpiSignature(),
            'request_languageCode' => $oUser->getUserPaymorrowRequestLanguage(),
            'request_id'           => $this->getUniqueString(),
        );
    }

    /**
     * Compiles an array with all user and order data to pass to Paymorrow.
     *
     * @param bool $blLogData
     *
     * @return array
     */
    public function collectEshopData( $blLogData = true )
    {
        /** @var OxpsPaymorrowOxUser|oxUser $oUser */
        $oUser = $this->getUser();

        // If user does not exist return null
        if ( !$oUser->exists() ) {
            return null;
        }

        /** @var OxpsPaymorrowSettings $oPmSettings */
        $oPmSettings = oxNew( 'OxpsPaymorrowSettings' );

        // Mandatory authentication data
        $aData = $this->collectCommonData();

        $aData['addressEditing_disabled'] = 'N';
        $aData['order_id']                = $this->_getBasketAndSessionHash();
        $aData['source']                  = 'PAYMORROW_GATEWAY_JS';
        $aData['operationMode']           = $oPmSettings->getPaymorrowOperationMode();

        // Order data
        $aData = $this->_addOrderData( $aData, $oUser );

        // User profile data
        $aData = $this->_addUserProfileData( $aData, $oUser );

        // User shipping address data
        $aData = $this->_addUserShippingAddressData( $aData, $oUser );

        // User order history data
        $aData = $this->_addOrderHistoryData( $aData, $oUser );

        // Device and client data
        $aData = $this->_addDeviceAndClientData( $aData, $oUser );

        if ( !empty( $blLogData ) ) {

            /** @var OxpsPaymorrowLogger $oLogger */
            $oLogger = oxRegistry::get( 'OxpsPaymorrowLogger' );
            $oLogger->logWithType( $aData, 'collectEshopData' );
        }

        return $aData;
    }

    /**
     * Compiles an array with order confirmation data to pass to Paymorrow.
     *
     * @return array
     */
    public function collectConfirmData()
    {
        $aData = $this->collectEshopData(false);

        // Add transaction data from session
        $aData['pm_order_transaction_id'] = oxRegistry::get('OxpsPaymorrowModule')->getPaymentTransactionId();
        $aData['order_id'] = oxRegistry::getSession()->getVariable('pm_order_id');

        // Add verification data
        /** @var OxpsPaymorrowGateway $oGateway */
        $oGateway = oxRegistry::get('OxpsOxid2Paymorrow')->getPaymorrowGateway();

        $aData['verification_customer_email'] = $this->_findInArray($aData, 'customer_email');
        $aData['verification_shippingHash'] = $oGateway->getAddressHash(
            array(
                'lastName' => $this->_findInArray($aData, 'customer_lastName'),
                'street'   => $this->_findInArray($aData, 'customer_shippingAddress_street'),
                'houseNo'  => $this->_findInArray($aData, 'customer_shippingAddress_houseNo'),
                'zip'      => $this->_findInArray($aData, 'customer_shippingAddress_postalCode'),
            )
        );
        $aData['verification_billingHash'] = $oGateway->getAddressHash(
            array(
                'lastName' => $this->_findInArray($aData, 'customer_lastName'),
                'street'   => $this->_findInArray($aData, 'customer_billingAddress_street'),
                'houseNo'  => $this->_findInArray($aData, 'customer_billingAddress_houseNo'),
                'zip'      => $this->_findInArray($aData, 'customer_billingAddress_postalCode'),
            )
        );
        $aData['verification_order_grossAmount'] = $this->_findInArray($aData, 'order_grossAmount');

        /** @var OxpsPaymorrowLogger $oLogger */
        $oLogger = oxRegistry::get('OxpsPaymorrowLogger');
        $oLogger->logWithType($aData, 'collectConfirmData');

        return $aData;
    }


    /**
     * Get user data for the payment form initialization in JSON format.
     *
     * @return string
     */
    public function printPmData()
    {
        $aData      = $this->collectEshopData();
        $aPrintData = array(
            'phone'               => $this->_findInArray( $aData, 'customer_phoneNumber' ),
            'mobile'              => $this->_findInArray( $aData, 'customer_mobileNumber' ),
            'session_id'          => $this->_findInArray( $aData, 'client_browser_session_id' ),
            'cookie_id'           => $this->_findInArray( $aData, 'client_cookies_id' ),
            'firstName'           => $this->_findInArray( $aData, 'customer_firstName' ),
            'lastName'            => $this->_findInArray( $aData, 'customer_lastName' ),
            'dob'                 => $this->_findInArray( $aData, 'customer_dateOfBirth' ),
            'gender'              => $this->_findInArray( $aData, 'customer_gender' ),
            'email'               => $this->_findInArray( $aData, 'customer_email' ),
            'street'              => $this->_findInArray( $aData, 'customer_billingAddress_street' ),
            'houseNumber'         => $this->_findInArray( $aData, 'customer_billingAddress_houseNo' ),
            'locality'            => $this->_findInArray( $aData, 'customer_billingAddress_city' ),
            'postalCode'          => $this->_findInArray( $aData, 'customer_billingAddress_postalCode' ),
            'country'             => $this->_findInArray( $aData, 'customer_billingAddress_country' ),
            'shippingStreet'      => $this->_findInArray( $aData, 'customer_shippingAddress_street' ),
            'shippingHouseNumber' => $this->_findInArray( $aData, 'customer_shippingAddress_houseNo' ),
            'shippingLocality'    => $this->_findInArray( $aData, 'customer_shippingAddress_city' ),
            'shippingPostalCode'  => $this->_findInArray( $aData, 'customer_shippingAddress_postalCode' ),
            'shippingCountry'     => $this->_findInArray( $aData, 'customer_shippingAddress_country' ),
            'shippingFirstName'   => $this->_findInArray( $aData, 'customer_shippingAddress_firstName' ),
            'shippingLastName'    => $this->_findInArray( $aData, 'customer_shippingAddress_lastName' ),
            'orderAmount'         => $this->_findInArray( $aData, 'order_grossAmount' ),
            'langcode'            => $this->_findInArray( $aData, 'request_languageCode' ),
            'client_ip'           => $this->_findInArray( $aData, 'client_ipAddress' ),
            'currencyCode'        => $this->_findInArray( $aData, 'order_currency' ),
        );

        // Check if there are session fields set and add to print data if available
        $aSessionData = (array) oxRegistry::getSession()->getVariable( 'pm_init_data' );

        if ( !empty( $aSessionData ) ) {
            $aPrintData = array_merge( $aPrintData, $aSessionData );
        }

        return json_encode( $aPrintData );
    }

    /**
     * Create and return unique string.
     *
     * @param string $sPrefix
     * @param bool   $bSuperUnique
     *
     * @return string
     */
    public function getUniqueString( $sPrefix = '', $bSuperUnique = false )
    {
        return uniqid( $sPrefix, $bSuperUnique );
    }

    /**
     * Convert default OXID encoding ISO-8859-15 to UTF-8.
     * Additionally it checks if shop it not on UTF-8 encoding already.
     * Optionally crops string to fit provided length.
     *
     * @codeCoverageIgnore
     *
     * @param string   $sSting
     * @param null|int $mLimitLength
     *
     * @return string
     */
    public static function toUtf( $sSting, $mLimitLength = null )
    {
        if ( !oxRegistry::getConfig()->getConfigParam( 'iUtfMode' ) ) {
            $sSting = iconv( 'ISO-8859-15', 'UTF-8', $sSting );
        }

        if ( is_integer( $mLimitLength ) and $mLimitLength > 0 ) {
            $sSting = mb_substr( $sSting, 0, $mLimitLength, 'UTF-8' );
        }

        return $sSting;
    }


    /**
     * Get array value by key.
     *
     * @param array $aData
     * @param mixed $mKey
     * @param mixed $sDefault
     *
     * @return string
     */
    protected function _findInArray( array $aData, $mKey, $sDefault = '' )
    {
        $sVal = $sDefault;

        if ( array_key_exists( $mKey, $aData ) and is_scalar( $aData[$mKey] ) ) {
            $sVal = trim( (string) $aData[$mKey] );
        }

        return $sVal;
    }

    /**
     * Get request headers string.
     *
     * @codeCoverageIgnore
     *
     * @return string
     */
    protected function _getBrowserHeaders()
    {
        $sHeaders = '';
        $aHeaders = apache_request_headers();

        foreach ( $aHeaders as $sHeader => $sValue ) {
            $sHeaders .= " $sHeader: $sValue\n";
        }

        return base64_encode( $sHeaders );
    }

    /**
     * Compiles a hash string to identify a basket in current session.
     * Used as temporary order ID.
     *
     * @return string
     */
    protected function _getBasketAndSessionHash()
    {
        $oSession = oxRegistry::getSession();
        $oBasket  = $oSession->getBasket();

        return md5(
            $oSession->getId() . serialize( $oBasket ) .
            $this->getUniqueString( self::OXPS_PAYMORROW_ORDER_HASH_SALT )
        );
    }


    /**
     * Add order related data to array.
     *
     * @param array                      $aData
     * @param OxpsPaymorrowOxUser|oxUser $oUser
     *
     * @return array
     */
    protected function _addOrderData( array $aData, oxUser $oUser )
    {
        /** @var OxpsPaymorrowOxBasket|oxBasket $oBasket */
        $oBasket = $oUser->getUserPaymorrowUserBasketFromSession();

        // Common order fields
        $aData['order_grossAmount'] = (double) $oBasket->getPaymorrowTotalAmount();
        $aData['order_vatAmount']   = (double) $oBasket->getPaymorrowTotalVatAmount();
        $aData['order_currency']    = self::toUtf( $oBasket->getPaymorrowBasketCurrency() );

        // Summarized line items array: products, costs, vouchers
        $aData = array_merge( $aData, $oBasket->getPaymorrowBasketLineItems() );

        return $aData;
    }

    /**
     * Add user profile data: common user data and billing address data.
     *
     * @param array                      $aData
     * @param OxpsPaymorrowOxUser|oxUser $oUser
     *
     * @return array
     */
    protected function _addUserProfileData( array $aData, oxUser $oUser )
    {
        // Common user profile fields
        $aData['customer_id']           = self::toUtf( $oUser->getCustomerPaymorrowCustomerNumber() );
        $aData['customer_group']        = self::toUtf( $oUser->getUserGroupNames(), 255 );
        $aData['customer_title']        = self::toUtf( $oUser->getUserPaymorrowSalutation() );
        $aData['customer_gender']       = self::toUtf( $oUser->getUserPaymorrowGender() );
        $aData['customer_firstName']    = self::toUtf( $oUser->getUserPaymorrowFirstName() );
        $aData['customer_lastName']     = self::toUtf( $oUser->getUserPaymorrowLastName() );
        $aData['customer_phoneNumber']  = self::toUtf( $oUser->getUserPaymorrowPhone() );
        $aData['customer_mobileNumber'] = self::toUtf( $oUser->getUserPaymorrowMobilePhone() );
        $aData['customer_email']        = self::toUtf( $oUser->getUserPaymorrowEmail() );
        $aData['customer_dateOfBirth']  = self::toUtf( $oUser->getUserPaymorrowDateOfBirth() );

        // Billing address fields
        $aData['customer_billingAddress_street']     = self::toUtf( $oUser->getUserPaymorrowStreet() );
        $aData['customer_billingAddress_houseNo']    = self::toUtf( $oUser->getUserPaymorrowStreetNumber() );
        $aData['customer_billingAddress_postalCode'] = self::toUtf( $oUser->getUserPaymorrowZipCode() );
        $aData['customer_billingAddress_city']       = self::toUtf( $oUser->getUserPaymorrowCity() );
        $aData['customer_billingAddress_country']    = self::toUtf( $oUser->getUserPaymorrowCountry() );

        return $aData;
    }

    /**
     * Add user shipping address data: can be same as billing or a separate shipping address.
     *
     * @param array                      $aData
     * @param OxpsPaymorrowOxUser|oxUser $oUser
     *
     * @return array
     */
    protected function _addUserShippingAddressData( array $aData, oxUser $oUser )
    {
        $blShowShippingAddress = (bool) oxRegistry::getSession()->getVariable( 'blshowshipaddress' );

        if ( !$blShowShippingAddress ) {

            // Use billing address as shipping
            $aData['customer_shippingAddress_street']     = $aData['customer_billingAddress_street'];
            $aData['customer_shippingAddress_houseNo']    = $aData['customer_billingAddress_houseNo'];
            $aData['customer_shippingAddress_city']       = $aData['customer_billingAddress_city'];
            $aData['customer_shippingAddress_postalCode'] = $aData['customer_billingAddress_postalCode'];
            $aData['customer_shippingAddress_country']    = $aData['customer_billingAddress_country'];

            // Additional, optional fields
            $aData['customer_shippingAddress_company'] = self::toUtf( $oUser->oxuser__oxcompany->value );
        } else {

            // Load active shipping address
            /** @var oxAddress $oAddress */
            $oAddress = $oUser->getSelectedAddress();

            $aData['customer_shippingAddress_street']     = self::toUtf( $oAddress->oxaddress__oxstreet->value );
            $aData['customer_shippingAddress_houseNo']    = self::toUtf( $oAddress->oxaddress__oxstreetnr->value );
            $aData['customer_shippingAddress_city']       = self::toUtf( $oAddress->oxaddress__oxcity->value );
            $aData['customer_shippingAddress_postalCode'] = self::toUtf( $oAddress->oxaddress__oxzip->value );
            $aData['customer_shippingAddress_country']    = self::toUtf(
                $oUser->getUserPaymorrowCountry( $oAddress->oxaddress__oxcountryid->value )
            );

            // Additional, optional fields
            $aData['customer_shippingAddress_additionalInfo'] = self::toUtf( $oAddress->oxaddress__oxaddinfo->value );
            $aData['customer_shippingAddress_firstName']      = self::toUtf( $oAddress->oxaddress__oxfname->value );
            $aData['customer_shippingAddress_lastName']       = self::toUtf( $oAddress->oxaddress__oxlname->value );
            $aData['customer_shippingAddress_company']        = self::toUtf( $oAddress->oxaddress__oxcompany->value );
        }

        return $aData;
    }

    /**
     * Add user order history data.
     * Maximum 10 last orders fields are added.
     *
     * @param array  $aData
     * @param oxUser $oUser
     *
     * @return array
     */
    protected function _addOrderHistoryData( array $aData, oxUser $oUser )
    {
        /** @var oxList $oOrdersList */
        $oOrdersList = $oUser->getOrders( 10 );

        if ( empty( $oOrdersList ) or !$oOrdersList->count() ) {
            return $aData;
        }

        $sFieldPrefix = 'customer_history_%d_%s';
        $iItemNumber  = 1;

        foreach ( $oOrdersList as $oOrder ) {
            /** @var OxpsPaymorrowOxOrder|oxOrder $oOrder */

            $aData[sprintf( $sFieldPrefix, $iItemNumber, 'orderDate' )]     = $oOrder->getOrderDate();
            $aData[sprintf( $sFieldPrefix, $iItemNumber, 'totalAmount' )]   = $oOrder->getTotalOrderSum();
            $aData[sprintf( $sFieldPrefix, $iItemNumber, 'paymentMethod' )] = $oOrder->getPaymentMethodName();
            $aData[sprintf( $sFieldPrefix, $iItemNumber, 'orderStatus' )]   = $oOrder->getOrderStatus();

            $iItemNumber++;
        }

        return $aData;
    }

    /**
     * Add device and user client data.
     *
     * @param array                      $aData
     * @param OxpsPaymorrowOxUser|oxUser $oUser
     *
     * @return array
     */
    protected function _addDeviceAndClientData( array $aData, oxUser $oUser )
    {
        $oSession   = oxRegistry::getSession();
        $sSessionId = $oSession->getId();

        $aData['device_checkId']            = $sSessionId;
        $aData['client_browser_session_id'] = $sSessionId;
        $aData['client_cookies_id']         = $sSessionId;
        $aData['client_ipAddress']          = $oUser->getUserPaymorrowIpAddress();
        $aData['client_browser_header']     = $this->_getBrowserHeaders();

        return $aData;
    }
}

if ( !function_exists( 'apache_request_headers' ) ) {

    /**
     * Alternative for PHP method apache_request_headers()
     * Originates from http://www.php.net/manual/en/function.apache-request-headers.php#70810
     *
     * @codeCoverageIgnore
     *
     * @return array
     */
    function apache_request_headers()
    {
        $aHeaders = array();
        $sHttpRx  = '/\AHTTP_/';

        foreach ( $_SERVER as $sKey => $sVal ) {
            if ( preg_match( $sHttpRx, $sKey ) ) {
                $sHeader  = preg_replace( $sHttpRx, '', $sKey );
                $aMatches = explode( '_', $sHeader );

                if ( count( $aMatches ) > 0 and strlen( $sHeader ) > 2 ) {
                    foreach ( $aMatches as $sMatchKey => $sMatchVal ) {
                        $aMatches[$sMatchKey] = ucfirst( $sMatchVal );
                    }

                    $sHeader = implode( '-', $aMatches );
                }

                $aHeaders[$sHeader] = $sVal;
            }
        }

        return $aHeaders;
    }
}
