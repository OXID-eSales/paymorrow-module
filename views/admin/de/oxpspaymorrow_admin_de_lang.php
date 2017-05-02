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

$sLangName = "Deutsch";

$aLang = array(
    "charset"                                      => "UTF-8",
    "oxpspaymorrow"                                => "Paymorrow",

    'NAVIGATION_PAYMORROW'                         => '<a href="https://paymorrow.de/" target="_blank">Paymorrow Payment</a>',
    'SHOP_MODULE_GROUP_oxpsPaymorrowConfiguration' => 'API-Konfiguration',
    'SHOP_MODULE_GROUP_oxpsPaymorrowProfileUpdate' => 'Bestelldaten-Aktualisierung',
    'OXPSPAYMORROW_PAYMENT_TYPE_INVOICE'           => 'Rechnungskauf',
    'OXPSPAYMORROW_PAYMENT_TYPE_DIRECT_DEBIT'      => 'Lastschriftverfahren',
    'oxpspaymorrow_form_error_log'                 => 'Protokoll',
    'oxpspaymorrow_paymorrow_info'                 => 'Paymorrow-Info',
    'oxpspaymorrow_payment_map'                    => 'Paymorrow',

    // Main Menu Settings
    'OXPSPAYMORROW_MAIN_MENU_SETTINGS_TITLE'       => 'Fehlerprotokoll',
    'SHOP_MODULE_paymorrowSandboxMode'             => 'Sandbox-Modus',
    'SHOP_MODULE_paymorrowMerchantId'              => 'Live-Webservice-Benutzer',
    'SHOP_MODULE_paymorrowMerchantIdTest'          => 'Test-Webservice-Benutzer',
    'SHOP_MODULE_paymorrowEndpointUrlTest'         => 'Test-Endpoint-URL',
    'SHOP_MODULE_paymorrowEndpointUrlProd'         => 'Live-Endpoint-URL',
    'SHOP_MODULE_paymorrowLoggingEnabled'          => 'Protokollierung aktivieren',
    'SHOP_MODULE_paymorrowResourcePath'            => 'Live-Resource-Pfad (JavaScript/CSS)',
    'SHOP_MODULE_paymorrowResourcePathTest'        => 'Test-Resource-Pfad (JavaScript/CSS)',
    'SHOP_MODULE_paymorrowOperationMode'           => 'Live-Operation-Mode',
    'SHOP_MODULE_paymorrowOperationModeTest'       => 'Test-Operation-Mode',

    // RSA Keys fields
    'SHOP_MODULE_paymorrowKeysJson'                => 'Feld aller Daten',
    'SHOP_MODULE_paymorrowPrivateKey'              => 'Live - aktiver privater Schlüssel',
    'SHOP_MODULE_paymorrowPrivateKeyTest'          => 'Test - aktiver privater Schlüssel',
    'SHOP_MODULE_paymorrowPublicKey'               => 'Live - aktiver öffenticher Schlüssel',
    'SHOP_MODULE_paymorrowPublicKeyTest'           => 'Test - aktiver öffenticher Schlüssel',
    'SHOP_MODULE_paymorrowPaymorrowKey'            => 'Live - öffenticher Schlüssel Paymorrow',
    'SHOP_MODULE_paymorrowPaymorrowKeyTest'        => 'Test - öffenticher Schlüssel Paymorrow',

    // Profile data normalization settings
    'SHOP_MODULE_paymorrowUpdateAddresses'         => 'Zurückspielen der Anschriften bei Veränderung im Checkout',
    'SHOP_MODULE_paymorrowUpdatePhones'            => 'Zurückspielen der Telefonnummer bei Veränderung im Checkout',

    // Help Idents
    'PM_HELP_ADMIN_PAYMENT_METHODS_ACTIVATE'       => 'Aktivierung bewirkt die Zuordnung dieser Zahlungsart zu Paymorrow.',
    'PM_HELP_ADMIN_PAYMENT_METHODS_INVOICE'        => 'Aktivierung bewirkt, dass diese Paymorrow zugeordnete Zahlungsart für den Paymorrow-Rechnungskauf freigeschaltet wird.',
    'PM_HELP_ADMIN_PAYMENT_METHODS_SDD'            => 'Aktivierung bewirkt, dass diese Paymorrow zugeordnete Zahlungsart für das Paymorrow-Lastschriftverfahren freigeschaltet wird.',
);
