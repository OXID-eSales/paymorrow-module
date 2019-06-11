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
 * Class OxpsPaymorrowModule
 * Extends oxModule class handles module setup, provides additional tools.
 */
class OxpsPaymorrowModule extends oxModule
{

    /**
     * @var OxpsPaymorrowModule The module instance.
     */
    private static $_instance = null;


    /**
     * Class constructor.
     * Sets main module data and load additional data.
     *
     * @param string $sModuleTitle
     * @param string $sModuleDescription
     */
    function __construct( $sModuleTitle = 'Paymorrow Payments',
                          $sModuleDescription = 'Paymorrow Payments Module' )
    {
        $sModuleId = 'oxpspaymorrow';

        $this->setModuleData(
            array(
                'id'          => $sModuleId,
                'title'       => $sModuleTitle,
                'description' => $sModuleDescription,
            )
        );

        $this->load( $sModuleId );

        // Add the module class to oxRegistry
        oxRegistry::set( 'OxpsPaymorrowModule', $this );
    }


    /**
     * Returns the module instance
     *
     * @return OxpsPaymorrowModule
     */
    public static function getInstance()
    {
        return oxRegistry::get( 'OxpsPaymorrowModule' );
    }

    /**
     * Check if module was ever installed on eShop.
     * Verifies if a custom field exists.
     *
     * @return bool
     */
    public static function isModuleInstalled()
    {
        $result = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getOne("SHOW COLUMNS FROM `oxpayments` LIKE 'OXPSPAYMORROWACTIVE'");

        return ('OXPSPAYMORROWACTIVE' === $result);
    }

    /**
     * Module activation script: executes docs/install.sql, rebuilds views and clears cache.
     */
    public static function onActivate()
    {
        if (!self::isModuleInstalled()) {
            self::_dbEvent('install.sql', 'Error activating module: ');
        }
    }

    /**
     * Module deactivation script: clears cache.
     */
    public static function onDeactivate()
    {
        self::cleanTmp();
    }

    /**
     * Clean temp folder content.
     *
     * @param string $sClearFolderPath Sub-folder path to delete from. Should be a full, valid path inside temp folder.
     *
     * @return boolean
     */
    public static function cleanTmp( $sClearFolderPath = '' )
    {
        $sTempFolderPath = realpath(oxRegistry::getConfig()->getConfigParam( 'sCompileDir' ));

        if ( !empty( $sClearFolderPath ) and
             ( strpos( $sClearFolderPath, $sTempFolderPath ) !== false ) and
             is_dir( $sClearFolderPath )
        ) {

            // User argument folder path to delete from
            $sFolderPath = $sClearFolderPath;
        } elseif ( empty( $sClearFolderPath ) ) {

            // Use temp folder path from settings
            $sFolderPath = $sTempFolderPath;
        } else {
            return false;
        }

        $hDir = opendir( $sFolderPath );

        if ( !empty( $hDir ) ) {
            while ( false !== ( $sFileName = readdir( $hDir ) ) ) {
                $sFilePath = $sFolderPath . '/' . $sFileName;

                if ( !in_array( $sFileName, array('.', '..', '.htaccess') ) and is_file( $sFilePath ) ) {

                    // Delete a file if it is allowed to delete
                    @unlink( $sFilePath );
                } elseif ( $sFileName == 'smarty' and is_dir( $sFilePath ) ) {

                    // Recursive call to clean Smarty temp
                    self::cleanTmp( $sFilePath );
                }
            }
        }

        return true;
    }


    /**
     * Returns Paymorrow module version
     *
     * @return string
     */
    public function getPaymorrowModuleVersion()
    {
        $aModuleVersion = $this->getModuleVersions();

        return $aModuleVersion[$this->getId()];
    }

    /**
     * Get translated string bt the translation code.
     *
     * @param string  $sCode
     * @param boolean $blUseModulePrefix User module translations prefix or not.
     *
     * @return string
     */
    public function translate( $sCode, $blUseModulePrefix = true )
    {
        if ( $blUseModulePrefix ) {
            $sCode = 'PAYMORROW_' . $sCode;
        }

        return oxRegistry::getLang()->translateString( $sCode, oxRegistry::getLang()->getBaseLanguage(), false );
    }

    /**
     * Get CMS snippet content by identified ID.
     *
     * @param string $sIdentifier
     * @param bool   $blNoHtml
     *
     * @return string
     */
    public function getCmsContent( $sIdentifier, $blNoHtml = true )
    {
        /** @var oxContent|oxI18n $oContent */
        $oContent = oxNew( 'oxContent' );
        $oContent->loadByIdent( trim( (string) $sIdentifier ) );

        $sValue = (string) $oContent->oxcontents__oxcontent->getRawValue();

        return ( empty( $blNoHtml ) ? $sValue : nl2br( strip_tags( $sValue ) ) );
    }

    /**
     * Get module setting value.
     *
     * @param string  $sModuleSettingName Module setting parameter name without module prefix.
     * @param boolean $blUseModulePrefix  User module settings prefix or not.
     *
     * @return mixed
     */
    public function getSetting( $sModuleSettingName, $blUseModulePrefix = true )
    {
        if ( $blUseModulePrefix ) {
            $sModuleSettingName = 'paymorrow' . (string) $sModuleSettingName;
        }

        return oxRegistry::getConfig()->getConfigParam( (string) $sModuleSettingName );
    }

    /**
     * Get module path.
     *
     * @return string Full path to module dir.
     */
    public function getPath()
    {
        return oxRegistry::getConfig()->getModulesDir() . 'oxps/paymorrow';
    }

    /**
     * Settings update event.
     * Fetches latest configuration data from Paymorrow and update relevant module settings with it.
     */
    public function updateSettings()
    {
        // Load the configuration from Paymorrow

        /** @var OxpsPaymorrowRequestControllerProxy $oRequestControllerProxy */
        $oRequestControllerProxy = oxNew( 'OxpsPaymorrowRequestControllerProxy' );
        $aConfigurationResponse  = $oRequestControllerProxy->getConfiguration();

        /** @var OxpsPaymorrowResponseHandler $oResponseHandler */
        $oResponseHandler   = oxNew( 'OxpsPaymorrowResponseHandler' );
        $aConfigurationData = $oResponseHandler->parseGetConfigurationResponse( $aConfigurationResponse );

        $oConfig = oxRegistry::getConfig();

        if ( oxRegistry::get( 'OxpsPaymorrowSettings' )->isSandboxMode() ) {
            $aSettingsMap = array(
                'api_endpoint'         => 'paymorrowEndpointUrlTest',
                'api_resource_handler' => 'paymorrowResourcePathTest',
                'api_operation_mode'   => 'paymorrowOperationModeTest',
            );
        } else {
            $aSettingsMap = array(
                'api_endpoint'         => 'paymorrowEndpointUrlProd',
                'api_resource_handler' => 'paymorrowResourcePath',
                'api_operation_mode'   => 'paymorrowOperationMode',
            );
        }

        // Apply the configuration for module settings
        foreach ( $aSettingsMap as $sPaymorrowKey => $sSettingsKey ) {
            if ( !empty( $aConfigurationData[$sPaymorrowKey] ) ) {
                $sValue = trim( (string) $aConfigurationData[$sPaymorrowKey] );

                $oConfig->setConfigParam( $sSettingsKey, $sValue );
                $oConfig->saveShopConfVar(
                    'str', $sSettingsKey, $sValue, null, sprintf( 'module:%s', $this->getId() )
                );
            }
        }

        // Reset resource cache
        /** @var OxpsPaymorrowResourceCache $oResourceCache */
        $oResourceCache = oxNew( 'OxpsPaymorrowResourceCache' );
        $oResourceCache->cleanCache();
    }

    /**
     * Check if user address and/or phone update is enabled and user normalized values to update profile data.
     *
     * @param array $aNormalizedData
     */
    public function updateProfile( array $aNormalizedData )
    {
        /** @var OxpsPaymorrowSettings $oSettings */
        $oSettings = oxRegistry::get( 'OxpsPaymorrowSettings' );

        /** @var OxpsPaymorrowOxUser|oxUser $oUser */
        $oUser = oxNew( 'OxpsPaymorrowOxUser' );
        $oUser->loadActiveUser();

        // Updating addresses if it is enabled in the settings
        if ( $oSettings->isAddressesUpdateEnabled() ) {

            // Updating billing address (not post code and not country)
            $oUser->mapDataAndSaveObject(
                $aNormalizedData,
                array(
                    'customer_billingAddress_street'  => 'oxuser__oxstreet',
                    'customer_billingAddress_houseNo' => 'oxuser__oxstreetnr',
                    'customer_billingAddress_city'    => 'oxuser__oxcity',
                )
            );

            // Loading and updating shipping address if it is selected
            /** @var oxAddress $oShippingAddress */
            $oShippingAddress      = $oUser->getSelectedAddress();
            $blShowShippingAddress = (bool) oxRegistry::getSession()->getVariable( 'blshowshipaddress' );

            if ( $blShowShippingAddress and ( $oShippingAddress instanceof oxAddress ) and $oShippingAddress->getId()
            ) {
                $oUser->mapDataAndSaveObject(
                    $aNormalizedData,
                    array(
                        'customer_shippingAddress_street'  => 'oxaddress__oxstreet',
                        'customer_shippingAddress_houseNo' => 'oxaddress__oxstreetnr',
                        'customer_shippingAddress_city'    => 'oxaddress__oxcity',
                    ),
                    $oShippingAddress
                );
            }
        }

        // Updating phone numbers if it is enabled in the settings
        if ( $oSettings->isPhonesUpdateEnabled() ) {
            $oUser->mapDataAndSaveObject(
                $aNormalizedData,
                array('customer_phoneNumber' => 'oxuser__oxfon', 'customer_mobileNumber' => 'oxuser__oxmobfon')
            );
        }
    }

    /**
     * Get module id's with versions
     * Method transferred from to make Paymorrow module compatible with eShop v5.2+
     *
     * @return array
     */
    public function getModuleVersions()
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        return $config->getConfigParam( 'aModuleVersions' );
    }

    /**
     * Get selected Paymorrow payment method name: "INVOICE" or "SDD".
     *
     * @return string
     */
    public function getPaymentMethodName()
    {
        $aVerifyData = (array) oxRegistry::getSession()->getVariable('pm_verify');
        $sMethodName = array_key_exists('pm_paymentMethod_name', $aVerifyData)
            ? (string) $aVerifyData['pm_paymentMethod_name']
            : '';

        return $sMethodName;
    }

    /**
     * Check session for verified Paymorrow payment method ID and get order transaction ID for the method.
     *
     * @param bool $blOnlySessionKey If true, only return a session ket of the transaction ID, otherwise - the ID
     *
     * @return string
     */
    public function getPaymentTransactionId($blOnlySessionKey = false)
    {
        $sTransactionIdKey = sprintf('pm_order_transaction_id%s', $this->getPaymentMethodName());
        $sTransactionId = (string) oxRegistry::getSession()->getVariable($sTransactionIdKey);

        return empty($blOnlySessionKey) ? $sTransactionId : $sTransactionIdKey;
    }

    /**
     * Get Paymorrow response data from session for selected payment method.
     *
     * @return array
     */
    public function getPaymorrowResponseData()
    {
        $sResponseKey = sprintf('pm_response%s', $this->getPaymentMethodName());

        return (array) oxRegistry::getSession()->getVariable($sResponseKey);
    }


    /**
     * Install/uninstall event.
     * Executes SQL queries form a file.
     *
     * @param string $sSqlFile      SQL file located in module docs folder (usually install.sql or uninstall.sql).
     * @param string $sFailureError An error message to show on failure.
     */
    protected static function _dbEvent( $sSqlFile, $sFailureError = "Operation failed: " )
    {
        try {
            $oDb  = oxDb::getDb();
            $sSql = file_get_contents( dirname( __FILE__ ) . '/../docs/' . (string) $sSqlFile );
            $aSql = explode( ';', trim($sSql));

            if ( !empty( $aSql ) ) {
                foreach ( $aSql as $sQuery ) {
                    if ( !empty( $sQuery ) ) {
                        $oDb->execute(trim($sQuery));
                    }
                }

            }
        } catch ( Exception $ex ) {
            error_log( $sFailureError . $ex->getMessage() );
        }

        self::cleanTmp();

        /** @var oxDbMetaDataHandler $oDbHandler */
        $oDbHandler = oxNew( 'oxDbMetaDataHandler' );
        $oDbHandler->updateViews();
    }
}
