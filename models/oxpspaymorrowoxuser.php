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
 * Class OxpsPaymorrowOxUser extends oxUser
 *
 * @see oxUser
 */
class OxpsPaymorrowOxUser extends OxpsPaymorrowOxUser_parent
{

    /**
     * Return User mobile phone number if exist, else return phone number.
     *
     * @return string
     */
    public function getUserTelephone()
    {
        return !empty( $this->oxuser__oxmobfon->value )
            ? $this->getUserPaymorrowMobilePhone()
            : $this->getUserPaymorrowPhone();
    }

    /**
     * Get user mobile phone number.
     *
     * @return string
     */
    public function getUserPaymorrowMobilePhone()
    {
        return $this->oxuser__oxmobfon->value;
    }

    /**
     * Get user phone number.
     *
     * @return string
     */
    public function getUserPaymorrowPhone()
    {
        return $this->oxuser__oxfon->value;
    }

    /**
     * Get user birth date.
     *
     * @param bool $blSplit If true return array with year, month, day, if false the date as string (default)
     *
     * @return string|array
     */
    public function getUserPaymorrowDateOfBirth( $blSplit = false )
    {
        $mDate = $this->oxuser__oxbirthdate->value;

        if ( !empty( $blSplit ) ) {
            $aDate = explode( '-', $mDate );
            $mDate = array(
                $this->_getDateComponentAsInteger( $aDate, 0, '0000' ),
                $this->_getDateComponentAsInteger( $aDate, 1 ),
                $this->_getDateComponentAsInteger( $aDate, 2 ),
            );
        }

        return $mDate;
    }

    /**
     * Get a customer number of user.
     *
     * @return string
     */
    public function getCustomerPaymorrowCustomerNumber()
    {
        return $this->oxuser__oxcustnr->value;
    }

    /**
     * Get user gender basing on salutation.
     * NOTE: IT will not determine a gender in non English/German languages and non default salutations.
     *
     * @param bool $blLowercase Will return lowercase value if true, and UPPERCASE on false (default)
     *
     * @return string
     */
    public function getUserPaymorrowGender( $blLowercase = false )
    {
        $sGender     = '';
        $sSalutation = mb_strtolower( $this->getUserPaymorrowSalutation(), 'UTF-8' );

        if ( in_array( $sSalutation, array('mr', 'herr') ) ) {
            $sGender = 'MALE';
        } elseif ( in_array( $sSalutation, array('mrs', 'frau') ) ) {
            $sGender = 'FEMALE';
        }

        return empty( $blLowercase ) ? $sGender : strtolower( $sGender );
    }

    /**
     * Get user salutation.
     *
     * @return string
     */
    public function getUserPaymorrowSalutation()
    {
        return $this->oxuser__oxsal->value;
    }

    /**
     * Get user language used when browsing shop.
     *
     * @return string
     */
    public function getUserPaymorrowRequestLanguage()
    {
        return oxRegistry::getLang()->getLanguageAbbr();
    }

    /**
     * Get client remote IP address.
     * For IPv6 got from utils, gets value from REMOTE_ADDR variable directly.
     *
     * @return string
     */
    public function getUserPaymorrowIpAddress()
    {
        /** @var  $oUtilsServer oxUtilsServer */
        $oUtilsServer = oxRegistry::get( "oxUtilsServer" );
        $sIpAddress   = $oUtilsServer->getRemoteAddress();

        return strpos( $sIpAddress, ':' ) ? $oUtilsServer->getServerVar( 'REMOTE_ADDR' ) : $sIpAddress;
    }

    /**
     * Get a cookie value.
     *
     * @codeCoverageIgnore
     *
     * @param $sCookie - cookie to retrieve
     *
     * @return mixed
     */
    public function getUserPaymorrowCookie( $sCookie )
    {
        /** @var  $oUtilsServer oxUtilsServer */
        $oUtilsServer = oxRegistry::get( "oxUtilsServer" );

        return $oUtilsServer->getOxCookie( $sCookie );
    }

    /**
     * Get first name.
     *
     * @return string
     */
    public function getUserPaymorrowFirstName()
    {
        return $this->oxuser__oxfname->value;
    }

    /**
     * Get last name.
     *
     * @return string
     */
    public function getUserPaymorrowLastName()
    {
        return $this->oxuser__oxlname->value;
    }

    /**
     * Get user email (login name).
     *
     * @return string
     */
    public function getUserPaymorrowEmail()
    {
        return $this->oxuser__oxusername->value;
    }


    /**
     * Get user billing address street name.
     *
     * @return string
     */
    public function getUserPaymorrowStreet()
    {
        return $this->oxuser__oxstreet->value;
    }

    /**
     * Get user billing address street number.
     *
     * @return string
     */
    public function getUserPaymorrowStreetNumber()
    {
        return $this->oxuser__oxstreetnr->value;
    }

    /**
     * Get user billing address postal code.
     *
     * @return string
     */
    public function getUserPaymorrowZipCode()
    {
        return $this->oxuser__oxzip->value;
    }

    /**
     * Get user session ID.
     *
     * @return string
     */
    public function getUserPaymorrowSessionId()
    {
        $session = \OxidEsales\Eshop\Core\Registry::getSession();
        return $session->getId();
    }

    /**
     * Get oxBasket object loaded from Session.
     *
     * @return oxBasket|OxpsPaymorrowOxBasket
     */
    public function getUserPaymorrowUserBasketFromSession()
    {
        $session = \OxidEsales\Eshop\Core\Registry::getSession();
        return $session->getBasket();
    }

    /**
     * Get order ID from current basket in session.
     *
     * @return string
     */
    public function getUserPaymorrowOrderIdFromBasket()
    {
        return $this->getUserPaymorrowUserBasketFromSession()->getOrderId();
    }

    /**
     * Get user billing address city.
     *
     * @return string
     */
    public function getUserPaymorrowCity()
    {
        return $this->oxuser__oxcity->value;
    }

    /**
     * Get user billing address country code.
     *
     * @param string $sCountryId Optional country ID to load. If empty, default user billing country is loaded.
     *
     * @return string
     */
    public function getUserPaymorrowCountry( $sCountryId = '' )
    {
        /** @var oxCountry $oCountry */
        $oCountry = oxNew( 'oxCountry' );

        $oCountry->load( empty( $sCountryId ) ? $this->oxuser__oxcountryid->value : (string) $sCountryId );

        return $oCountry->oxcountry__oxisoalpha2->value;
    }

    /**
     * Search data array for user profile fields and update the user with matching values if any.
     *
     * @param array $aData
     *
     * @return bool|null
     */
    public function mapToProfileDataAndUpdateUser( array $aData )
    {
        return $this->mapDataAndSaveObject(
            $aData,
            array(
                'pm_customer_firstName'                 => 'oxuser__oxfname',
                'pm_customer_lastName'                  => 'oxuser__oxlname',
                'pm_customer_phoneNumber'               => 'oxuser__oxfon',
                'pm_customer_dateOfBirth'               => 'oxuser__oxbirthdate',
                'pm_customer_billingAddress_street'     => 'oxuser__oxstreet',
                'pm_customer_billingAddress_houseNo'    => 'oxuser__oxstreetnr',
                'pm_customer_billingAddress_postalCode' => 'oxuser__oxzip',
                'pm_customer_billingAddress_city'       => 'oxuser__oxcity',
            )
        );
    }

    /**
     * Checks data address fields to update.
     *
     * @param array     $aData
     * @param oxAddress $oAddress User active shipping address
     *
     * @return bool|null|string
     */
    public function mapShippingDataAndUpdateAddress( array $aData, oxAddress $oAddress )
    {
        return $this->mapDataAndSaveObject(
            $aData,
            array(
                'pm_customer_shippingAddress_street'         => 'oxaddress__oxstreet',
                'pm_customer_shippingAddress_houseNo'        => 'oxaddress__oxstreetnr',
                'pm_customer_shippingAddress_postalCode'     => 'oxaddress__oxzip',
                'pm_customer_shippingAddress_city'           => 'oxaddress__oxcity',
                'pm_customer_shippingAddress_firstName'      => 'oxaddress__oxfname',
                'pm_customer_shippingAddress_lastName'       => 'oxaddress__oxlname',
                'pm_customer_shippingAddress_company'        => 'oxaddress__oxcompany',
                'pm_customer_shippingAddress_additionalInfo' => 'oxaddress__oxaddinfo',
            ),
            $oAddress
        );
    }

    /**
     * Get user group names as comma separated string.
     *
     * @return string
     */
    public function getUserGroupNames()
    {
        /** @var OxpsPaymorrowOxUser|oxUser $this */

        /** @var oxList $oGroups */
        if ( !( $oGroups = $this->getUserGroups() ) ) {
            return '';
        }

        $aGroups = array();

        foreach ( $oGroups as $oGroup ) {
            if ( $this->_isGroupValid( $oGroup ) ) {
                $aGroups[] = $oGroup->oxgroups__oxtitle->value;
            }
        }

        return implode( ', ', $aGroups );
    }

    /**
     * Find and map object fields in data array, assign the values and save object.
     * Updates only with not empty values and if values duffer from existing ones.
     *
     * @param array       $aData
     * @param array       $aMap
     * @param object|null $oObject
     *
     * @return bool|null
     */
    public function mapDataAndSaveObject( array $aData, array $aMap, $oObject = null )
    {
        if ( is_null( $oObject ) ) {
            $oObject = $this;
        }

        if ( $this->_mapDataFieldsToObject( $aData, $aMap, $oObject ) ) {
            return $oObject->save();
        }

        return null;
    }


    /**
     * Get date component from an array by key as an integer.
     *
     * @param array  $aDate       Date components as array.
     * @param int    $iKey        Date component index.
     * @param string $sZeroString A zero representing string value, default is "00".
     *
     * @return int
     */
    protected function _getDateComponentAsInteger( $aDate, $iKey, $sZeroString = '00' )
    {
        if ( empty( $aDate[$iKey] ) or ( $aDate[$iKey] == $sZeroString ) ) {
            return 0;
        }

        return (int) $aDate[$iKey];
    }

    /**
     * Check if group is active and has non-empty title.
     *
     * @param object $oGroup
     *
     * @return bool
     */
    protected function _isGroupValid( $oGroup )
    {
        return ( !empty( $oGroup->oxgroups__oxactive->value ) and !empty( $oGroup->oxgroups__oxtitle->value ) );
    }

    /**
     * Check each data array field if it could be mapped to object and set the value if it is not empty and differ.
     *
     * @param array  $aData
     * @param array  $aMap
     * @param object $oObject
     *
     * @return bool True if at least one field updated, false otherwise.
     */
    protected function _mapDataFieldsToObject( array $aData, array $aMap, $oObject )
    {
        $blValueSet = false;

        foreach ( $aData as $sKey => $mValue ) {
            $sValue = trim( (string) $mValue );

            if ( $this->_isObjectFieldWithValidValue( $sKey, $sValue, $aMap, $oObject ) ) {
                $sField           = $aMap[$sKey];
                $oObject->$sField = new oxField( $sValue );
                $blValueSet       = true;
            }
        }

        return $blValueSet;
    }

    /**
     * Check if field is in the map array and value is nor blank, neither same as already set.
     *
     * @param string $sKey
     * @param string $sValue
     * @param array  $aMap
     * @param object $oObject
     *
     * @return bool True if key is mapped with new non-blank value, false otherwise.
     */
    protected function _isObjectFieldWithValidValue( $sKey, $sValue, $aMap, $oObject )
    {
        if ( !array_key_exists( $sKey, $aMap ) ) {
            return false;
        }

        $sField = $aMap[$sKey];

        if ( empty( $sValue ) or ( $oObject->$sField->value == $sValue ) ) {
            return false;
        }

        return true;
    }
}
