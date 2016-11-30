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
 * NOTE: This is a copy of the module file views/admin/en/oxpspaymorrow_admin_en_lang.php
 * It is needed for compatibility with older OXID eShop version.
 */

$sLangName = "English";

$aLang = array(
    "charset"                                      => "UTF-8",
    "oxpspaymorrow"                                => "Paymorrow",

    'NAVIGATION_PAYMORROW'                         => '<a href="https://paymorrow.de/" target="_blank">Paymorrow Payment</a>',
    'SHOP_MODULE_GROUP_oxpsPaymorrowConfiguration' => 'API Configuration',
    'SHOP_MODULE_GROUP_oxpsPaymorrowProfileUpdate' => 'Order Data Update',
    'OXPSPAYMORROW_PAYMENT_TYPE_INVOICE'           => 'Invoice',
    'OXPSPAYMORROW_PAYMENT_TYPE_DIRECT_DEBIT'      => 'Direct Debit',
    'oxpspaymorrow_form_error_log'                 => 'Log',
    'oxpspaymorrow_paymorrow_info'                 => 'Paymorrow Info',
    'oxpspaymorrow_payment_map'                    => 'Paymorrow',

    // Main Menu Settings
    'OXPSPAYMORROW_MAIN_MENU_SETTINGS_TITLE'       => 'Error Log',
    'SHOP_MODULE_paymorrowSandboxMode'             => 'Sandbox Mode',
    'SHOP_MODULE_paymorrowMerchantId'              => 'Live Webservice User',
    'SHOP_MODULE_paymorrowMerchantIdTest'          => 'Test Webservice User',
    'SHOP_MODULE_paymorrowEndpointUrlTest'         => 'Test Endpoint URL',
    'SHOP_MODULE_paymorrowEndpointUrlProd'         => 'Live Endpoint URL',
    'SHOP_MODULE_paymorrowLoggingEnabled'          => 'Enable Logging',
    'SHOP_MODULE_paymorrowResourcePath'            => 'Live Resource Path (JavaScript/CSS)',
    'SHOP_MODULE_paymorrowResourcePathTest'        => 'Test Resource Path (JavaScript/CSS)',
    'SHOP_MODULE_paymorrowOperationMode'           => 'Live Operation Mode',
    'SHOP_MODULE_paymorrowOperationModeTest'       => 'Test Operation Mode',

    // RSA Keys fields
    'SHOP_MODULE_paymorrowKeysJson'                => 'All Fields Data',
    'SHOP_MODULE_paymorrowPrivateKey'              => 'Live Merchant Active Private Key',
    'SHOP_MODULE_paymorrowPrivateKeyTest'          => 'Test Merchant Active Private Key',
    'SHOP_MODULE_paymorrowPublicKey'               => 'Live Merchant Active Public Key',
    'SHOP_MODULE_paymorrowPublicKeyTest'           => 'Test Merchant Active Public Key',
    'SHOP_MODULE_paymorrowPaymorrowKey'            => 'Live Paymorrow Active Public Key',
    'SHOP_MODULE_paymorrowPaymorrowKeyTest'        => 'Test Paymorrow Active Public Key',

    // Profile data normalization settings
    'SHOP_MODULE_paymorrowUpdateAddresses'         => 'Update order address(es) if changed within checkout',
    'SHOP_MODULE_paymorrowUpdatePhones'            => 'Update user phone number(s) if changed within checkout',

    // Help Idents
    'PM_HELP_ADMIN_PAYMENT_METHODS_ACTIVATE'       => 'Activation causes the assignment of this selected payment method to paymorrow.',
    'PM_HELP_ADMIN_PAYMENT_METHODS_INVOICE'        => 'Activation enables the paymorrow invoice payment method and associates it to this payment method.',
    'PM_HELP_ADMIN_PAYMENT_METHODS_SDD'            => 'Activation enables the paymorrow direct debit payment method and associates it to this payment method.',
);
