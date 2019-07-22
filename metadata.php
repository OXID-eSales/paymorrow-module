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
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'oxpspaymorrow',
    'title'       => array(
        'de' => 'Paymorrow Zahlungen',
        'en' => 'Paymorrow Payments',
    ),
    'description' => array(
        'de' => '<p><a style="color: blue; text-decoration: underline;" href="https://paymorrow.de/" title="Paymorrow-Homepage" target="_blank">>> Paymorrow-Homepage</a></p>
        <p><a style="color: blue; text-decoration: underline;" href="https://paymorrow.net/perthPortal/" title="Paymorrow-H&auml;ndlerportal" target="_blank">>> Paymorrow-H&auml;ndlerportal</a></p>
        <h2>Paymorrow-Plugin (OxpsOxid2Paymorrow)</h2>',
        'en' => '<p><a style="color: blue; text-decoration: underline;" href="https://paymorrow.de/" title="Paymorrow-Homepage" target="_blank">>> Paymorrow-Homepage</a></p>
        <p><a style="color: blue; text-decoration: underline;" href="https://paymorrow.net/perthPortal/" title="Paymorrow-H&auml;ndlerportal" target="_blank">>> Paymorrow-H&auml;ndlerportal</a></p>
        <h2>Paymorrow-Plugin (OxpsOxid2Paymorrow)</h2>',
    ),
    'thumbnail'   => 'out/pictures/pmlogo.png',
    'version'     => '2.0.2',
    'author'      => 'OXID Professional Services',
    'url'         => 'https://paymorrow.de/',
    'email'       => 'info@oxid-esales.com',
    'extend'      => array(

        // Standard Controllers
        'order'            => 'oxps/paymorrow/controllers/oxpspaymorroworder',
        'payment'          => 'oxps/paymorrow/controllers/oxpspaymorrowpayment',

        // Core
        'oxviewconfig'     => 'oxps/paymorrow/core/oxpspaymorrowoxviewconfig',

        // Models
        'oxpayment'        => 'oxps/paymorrow/models/oxpspaymorrowoxpayment',
        'oxuser'           => 'oxps/paymorrow/models/oxpspaymorrowoxuser',
        'oxorder'          => 'oxps/paymorrow/models/oxpspaymorrowoxorder',
        'oxbasketitem'     => 'oxps/paymorrow/models/oxpspaymorrowoxbasketitem',
        'oxpaymentgateway' => 'oxps/paymorrow/models/oxpspaymorrowoxpaymentgateway',
        'oxuserpayment'    => 'oxps/paymorrow/models/oxpspaymorrowoxuserpayment',
        'oxbasket'         => 'oxps/paymorrow/models/oxpspaymorrowoxbasket',
    ),
    'files'       => array(

        // Admin Controllers
        'oxpspaymorrowadminerrorlog'          => 'oxps/paymorrow/controllers/admin/oxpspaymorrowadminerrorlog.php',
        'oxpspaymorrowpaymentmap'             => 'oxps/paymorrow/controllers/admin/oxpspaymorrowpaymentmap.php',

        // Custom Controllers
        'oxpspaymorrowprepareorder'           => 'oxps/paymorrow/controllers/oxpspaymorrowprepareorder.php',
        'oxpspaymorrowresource'               => 'oxps/paymorrow/controllers/oxpspaymorrowresource.php',

        // Core
        'oxpspaymorrowmodule'                 => 'oxps/paymorrow/core/oxpspaymorrowmodule.php',
        'oxpspaymorrowclient'                 => 'oxps/paymorrow/core/oxpspaymorrowclient.php',
        'oxpspaymorroweshopdataprovider'      => 'oxps/paymorrow/core/oxpspaymorroweshopdataprovider.php',
        'oxpspaymorrowerrorhandler'           => 'oxps/paymorrow/core/oxpspaymorrowerrorhandler.php',
        'oxpspaymorrowgateway'                => 'oxps/paymorrow/core/oxpspaymorrowgateway.php',
        'oxpspaymorrowlogger'                 => 'oxps/paymorrow/core/oxpspaymorrowlogger.php',
        'oxpspaymorrowsettings'               => 'oxps/paymorrow/core/oxpspaymorrowsettings.php',
        'oxpspaymorrowrequestcontrollerproxy' => 'oxps/paymorrow/core/oxpspaymorrowrequestcontrollerproxy.php',
        'oxpspaymorrowresourcecache'          => 'oxps/paymorrow/core/oxpspaymorrowresourcecache.php',

        // Widgets
        'oxpspaymorrowinit'                   => 'oxps/paymorrow/components/widgets/oxpspaymorrowinit.php',
        'oxpspaymorrowsessionmonitor'         => 'oxps/paymorrow/components/widgets/oxpspaymorrowsessionmonitor.php',

        // Interface for: PaymorrowGateway
        'oxpsoxid2paymorrow'                  => 'oxps/paymorrow/core/oxpsoxid2paymorrow.php',
        'oxpspaymorrowresponsehandler'        => 'oxps/paymorrow/core/oxpspaymorrowresponsehandler.php',

        // Vendors
        'paymorrowgateway'                    => 'oxps/paymorrow/vendor/paymorrow_gateway/PaymorrowGateway.php',
        'eshopdataprovider'                   => 'oxps/paymorrow/vendor/paymorrow_gateway/EshopDataProvider.php',
        'requestcontroller'                   => 'oxps/paymorrow/vendor/paymorrow_gateway/RequestController.php',
        'abstractpaymorrowclient'             => 'oxps/paymorrow/vendor/paymorrow_gateway/paymorrow_client/AbstractPaymorrowClient.php',
        'paymorrowresourceproxy'              => 'oxps/paymorrow/vendor/paymorrow_gateway/PaymorrowResourceProxy.php',
    ),
    'templates'   => array(

        // Admin Template
        'paymorrow_paymnet_validation.tpl'   => 'oxps/paymorrow/views/admin/inc/paymorrow_paymnet_validation.tpl',
        'paymorrow_errorlog.tpl'             => 'oxps/paymorrow/views/admin/paymorrow_errorlog.tpl',
        'paymorrow_paymentmap.tpl'           => 'oxps/paymorrow/views/admin/paymorrow_paymentmap.tpl',

        // JavaScript hook/ Widget Templates
        'oxpspaymorrowinit.tpl'              => 'oxps/paymorrow/views/widget/oxpspaymorrowinit.tpl',
        'oxpspaymorrowsessionmonitor.tpl'    => 'oxps/paymorrow/views/widget/oxpspaymorrowsessionmonitor.tpl',

        // Payment templates
        'paymorrow_payment_direct_debit.tpl' => 'oxps/paymorrow/views/page/checkout/inc/paymorrow_payment_direct_debit.tpl',
        'paymorrow_payment_invoice.tpl'      => 'oxps/paymorrow/views/page/checkout/inc/paymorrow_payment_invoice.tpl',
    ),
    'blocks'      => array(

        // Admin back-end
        array(
            'template' => 'module_config.tpl',
            'block'    => 'admin_module_config_form',
            'file'     => 'views/admin/blocks/paymorrow_module_config.tpl'
        ),
        array(
            'template' => 'payment_main.tpl',
            'block'    => 'admin_payment_main_form',
            'file'     => 'views/admin/blocks/paymorrow_payment_main.tpl'
        ),

        // Pages
        array(
            'template' => 'page/checkout/payment.tpl',
            'block'    => 'select_payment',
            'file'     => 'views/blocks/paymorrow_select_payment.tpl'
        ),

        // Emails
        array(
            'template' => 'email/html/order_cust.tpl',
            'block'    => 'email_html_order_cust_orderemailend',
            'file'     => 'views/email/html/order_cust_pm_html_bank_details.tpl'
        ),
        array(
            'template' => 'email/plain/order_cust.tpl',
            'block'    => 'email_plain_order_cust_orderemailend',
            'file'     => 'views/email/plain/order_cust_pm_plain_bank_details.tpl'
        ),
        array(
            'template' => 'email/html/order_cust.tpl',
            'block'    => 'email_html_order_cust_paymentinfo',
            'file'     => 'views/email/html/order_cust_pm_html_paymentinfo.tpl'
        ),
        array(
            'template' => 'email/plain/order_cust.tpl',
            'block'    => 'email_plain_order_cust_paymentinfo',
            'file'     => 'views/email/plain/order_cust_pm_plain_paymentinfo.tpl'
        ),
        array(
            'template' => 'layout/footer.tpl',
            'block'    => 'footer_main',
            'file'     => 'views/blocks/paymorrow_footer.tpl'
        ),
    ),
    'settings'    => array(
        array(
            'group' => 'oxpsPaymorrowConfiguration',
            'name'  => 'paymorrowSandboxMode',
            'type'  => 'bool',
            'value' => true
        ),
        array(
            'group' => 'oxpsPaymorrowConfiguration',
            'name'  => 'paymorrowMerchantId',
            'type'  => 'str',
            'value' => ''
        ),
        array(
            'group' => 'oxpsPaymorrowConfiguration',
            'name'  => 'paymorrowMerchantIdTest',
            'type'  => 'str',
            'value' => ''
        ),
        array(
            'group' => 'oxpsPaymorrowConfiguration',
            'name'  => 'paymorrowEndpointUrlProd',
            'type'  => 'str',
            'value' => 'https://paymorrow.net/service/v2_1/'
        ),
        array(
            'group' => 'oxpsPaymorrowConfiguration',
            'name'  => 'paymorrowEndpointUrlTest',
            'type'  => 'str',
            'value' => 'https://test.paymorrow.net/service/v2_1/'
        ),
        array(
            'group' => 'oxpsPaymorrowConfiguration',
            'name'  => 'paymorrowResourcePath',
            'type'  => 'str',
            'value' => 'https://paymorrow.net/perthResource/jsmodule'
        ),
        array(
            'group' => 'oxpsPaymorrowConfiguration',
            'name'  => 'paymorrowResourcePathTest',
            'type'  => 'str',
            'value' => 'https://test.paymorrow.net/perthResource/jsmodule'
        ),
        array(
            'group' => 'oxpsPaymorrowConfiguration',
            'name'  => 'paymorrowOperationMode',
            'type'  => 'str',
            'value' => 'RISK_CHECK'
        ),
        array(
            'group' => 'oxpsPaymorrowConfiguration',
            'name'  => 'paymorrowOperationModeTest',
            'type'  => 'str',
            'value' => 'RISK_CHECK'
        ),
        array(
            'group' => 'oxpsPaymorrowConfiguration',
            'name'  => 'paymorrowLoggingEnabled',
            'type'  => 'bool',
            'value' => false
        ),
        array(
            'group' => 'oxpsPaymorrowConfiguration',
            'name'  => 'paymorrowKeysJson',
            'type'  => 'str',
            'value' => ''
        ),
        array(
            'group' => 'oxpsPaymorrowConfiguration',
            'name'  => 'paymorrowPrivateKey',
            'type'  => 'str',
            'value' => ''
        ),
        array(
            'group' => 'oxpsPaymorrowConfiguration',
            'name'  => 'paymorrowPrivateKeyTest',
            'type'  => 'str',
            'value' => ''
        ),
        array(
            'group' => 'oxpsPaymorrowConfiguration',
            'name'  => 'paymorrowPublicKey',
            'type'  => 'str',
            'value' => ''
        ),
        array(
            'group' => 'oxpsPaymorrowConfiguration',
            'name'  => 'paymorrowPublicKeyTest',
            'type'  => 'str',
            'value' => ''
        ),
        array(
            'group' => 'oxpsPaymorrowConfiguration',
            'name'  => 'paymorrowPaymorrowKey',
            'type'  => 'str',
            'value' => ''
        ),
        array(
            'group' => 'oxpsPaymorrowConfiguration',
            'name'  => 'paymorrowPaymorrowKeyTest',
            'type'  => 'str',
            'value' => ''
        ),
        array(
            'group' => 'oxpsPaymorrowProfileUpdate',
            'name'  => 'paymorrowUpdateAddresses',
            'type'  => 'bool',
            'value' => true
        ),
        array(
            'group' => 'oxpsPaymorrowProfileUpdate',
            'name'  => 'paymorrowUpdatePhones',
            'type'  => 'bool',
            'value' => true
        ),
    ),
    'events'      => array(
        'onActivate'   => 'OxpsPaymorrowModule::onActivate',
        'onDeactivate' => 'OxpsPaymorrowModule::onDeactivate',
    ),
);
