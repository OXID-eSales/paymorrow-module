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

include_once 'PaymorrowAcceptanceTestCase.php';

/**
 * Class Acceptance_02administrationOfPaymorrowTest.
 * Test Paymorrow module administration and back end behaviour.
 */
class Acceptance_02administrationOfPaymorrowTest extends PaymorrowAcceptanceTestCase
{
    public function testPaymorrowModuleIsInModulesList()
    {
        $this->loginAdmin('Extensions', 'Modules', true, PAYMORROW_USER_ADMIN, PAYMORROW_USER_ADMIN);

        $this->assertElementPresent(
            '//div[@id="liste"]//a[text()="Paymorrow Payments"]',
            'Paymorrow module should be in the list.'
        );
    }

    public function testPaymorrowModuleActivation()
    {
        $this->loginAdmin('Extensions', 'Modules', true, PAYMORROW_USER_ADMIN, PAYMORROW_USER_ADMIN);

        // Select Paymorrow module and open overview tab
        $this->openListItem('Paymorrow Payments');
        $this->openTab('Overview');

        // First deactivate module for the activation test if it is active already
        if ($this->isElementPresent('//input[@id="module_deactivate"]')) {
            $this->clickAndWaitFrame('//input[@id="module_deactivate"]', 'list');
            $this->waitForFrameToLoad('edit');
        }

        // Check and click "Activate" button
        $this->clickAndWait('//input[@id="module_activate"]');
        $this->waitForFrameToLoad('edit');
        // Check if the module was activated properly
        $this->assertElementPresent('//input[@id="module_deactivate"]', 'Now button "Deactivate" should appear.');

        $this->loginAdmin('Extensions', 'Modules', true, PAYMORROW_USER_ADMIN, PAYMORROW_USER_ADMIN);
        $this->openListItem('Paymorrow Payments');
        $this->frame('list');
        $this->assertElementPresent('//div[@class="tabs"]//a[text()="Log"]', 'Paymorrow "Log" tab should appear.');

        $this->openTab('Settings');
        $this->assertElementPresent('//b[text()="API Configuration"]', 'Setting should contain API Configuration');
        $this->assertElementPresent('//b[text()="Order Data Update"]', 'Setting should contain Order Data Update');
    }

    public function testPaymorrowModuleDeactivation()
    {
        $this->loginAdmin('Extensions', 'Modules', true, PAYMORROW_USER_ADMIN, PAYMORROW_USER_ADMIN);

        // Select Paymorrow module and open overview tab
        $this->assertElementPresent(
            '//div[@id="liste"]//a[text()="Paymorrow Payments"]',
            'Paymorrow module should be in the list.'
        );
        $this->clickAndWait('//div[@id="liste"]//a[text()="Paymorrow Payments"]');
        $this->openTab('Overview');

        // Check and click "Deactivate" button
        $this->assertElementPresent(
            '//input[@id="module_deactivate"]',
            'Paymorrow module "Deactivate" button should be available.'
        );
        $this->clickAndWaitFrame('//input[@id="module_deactivate"]', 'list');

        // Check if the module was deactivated properly
        $this->waitForElement('//input[@id="module_activate"]', 10, true);
        $this->assertElementPresent('//input[@id="module_activate"]', 'Now button "Activate" should appear.');

        $this->frame('list');
        $this->assertElementNotPresent(
            '//div[@class="tabs"]//a[text()="Log"]',
            'Paymorrow "Log" tab should be gone.'
        );
    }

    public function testPaymorrowModuleSettingsContainAllFields()
    {
        $this->_openPaymorrowSettings();

        // Open API settings
        $this->click('//b[text()="API Configuration"]');

        $this->assertElementPresent(
            '//input[@name="confbools[paymorrowSandboxMode]"]',
            'Settings field "paymorrowSandboxMode" should exist.'
        );
        $this->assertElementPresent(
            '//input[@name="confstrs[paymorrowMerchantId]"]',
            'Settings field "paymorrowMerchantId" should exist.'
        );
        $this->assertElementPresent(
            '//input[@name="confstrs[paymorrowMerchantIdTest]"]',
            'Settings field "paymorrowMerchantIdTest" should exist.'
        );
        $this->assertElementPresent(
            '//input[@name="confstrs[paymorrowEndpointUrlProd]"]',
            'Settings field "paymorrowEndpointUrlProd" should exist.'
        );
        $this->assertElementPresent(
            '//input[@name="confstrs[paymorrowEndpointUrlTest]"]',
            'Settings field "paymorrowEndpointUrlTest" should exist.'
        );
        $this->assertElementPresent(
            '//input[@name="confstrs[paymorrowResourcePath]"]',
            'Settings field "paymorrowResourcePath" should exist.'
        );
        $this->assertElementPresent(
            '//input[@name="confstrs[paymorrowResourcePathTest]"]',
            'Settings field "paymorrowResourcePathTest" should exist.'
        );
        $this->assertElementPresent(
            '//input[@name="confstrs[paymorrowOperationMode]"]',
            'Settings field "paymorrowOperationMode" should exist.'
        );
        $this->assertElementPresent(
            '//input[@name="confstrs[paymorrowOperationModeTest]"]',
            'Settings field "paymorrowOperationModeTest" should exist.'
        );
        $this->assertElementPresent(
            '//input[@name="confbools[paymorrowLoggingEnabled]"]',
            'Settings field "paymorrowLoggingEnabled" should exist.'
        );
        $this->assertElementPresent(
            '//input[@name="confstrs[paymorrowKeysJson]"]',
            'Settings field "paymorrowKeysJson" should exist.'
        );
        $this->assertElementPresent(
            '//input[@name="confstrs[paymorrowPrivateKey]"]',
            'Settings field "paymorrowPrivateKey" should exist.'
        );
        $this->assertElementPresent(
            '//input[@name="confstrs[paymorrowPrivateKeyTest]"]',
            'Settings field "paymorrowPrivateKeyTest" should exist.'
        );
        $this->assertElementPresent(
            '//input[@name="confstrs[paymorrowPublicKey]"]',
            'Settings field "paymorrowPublicKey" should exist.'
        );
        $this->assertElementPresent(
            '//input[@name="confstrs[paymorrowPublicKeyTest]"]',
            'Settings field "paymorrowPublicKeyTest" should exist.'
        );
        $this->assertElementPresent(
            '//input[@name="confstrs[paymorrowPaymorrowKey]"]',
            'Settings field "paymorrowPaymorrowKey" should exist.'
        );
        $this->assertElementPresent(
            '//input[@name="confstrs[paymorrowPaymorrowKeyTest]"]',
            'Settings field "paymorrowPaymorrowKeyTest" should exist.'
        );

        // Open Order Data Update settings
        $this->click('//b[text()="Order Data Update"]');

        $this->assertElementPresent(
            '//input[@name="confbools[paymorrowUpdateAddresses]"]',
            'Settings field "paymorrowUpdateAddresses" should exist.'
        );
        $this->assertElementPresent(
            '//input[@name="confbools[paymorrowUpdatePhones]"]',
            'Settings field "paymorrowUpdatePhones" should exist.'
        );
    }

    public function testPaymorrowModuleSettingsShowPlainFormOnNoMerchantId()
    {
        $this->_openPaymorrowSettings();

        // Open API settings
        $this->click('//b[text()="API Configuration"]');

        // Make sure no merchant ID is configured
        $this->type('//input[@name="confstrs[paymorrowMerchantIdTest]"]', '');
        $this->clickAndWait('//input[@name="save"]');

        // Open API settings again
        $this->click('//b[text()="API Configuration"]');

        $this->assertElementVisible(
            '//input[@name="confstrs[paymorrowKeysJson]"]',
            'Settings field "paymorrowKeysJson" should be visible.'
        );
        $this->assertElementVisible(
            '//input[@name="confstrs[paymorrowPrivateKey]"]',
            'Settings field "paymorrowPrivateKey" should be visible.'
        );
        $this->assertElementVisible(
            '//input[@name="confstrs[paymorrowPrivateKeyTest]"]',
            'Settings field "paymorrowPrivateKeyTest" should be visible.'
        );
        $this->assertElementVisible(
            '//input[@name="confstrs[paymorrowPublicKey]"]',
            'Settings field "paymorrowPublicKey" should be visible.'
        );
        $this->assertElementVisible(
            '//input[@name="confstrs[paymorrowPublicKeyTest]"]',
            'Settings field "paymorrowPublicKeyTest" should be visible.'
        );
        $this->assertElementVisible(
            '//input[@name="confstrs[paymorrowPaymorrowKey]"]',
            'Settings field "paymorrowPaymorrowKey" should be visible.'
        );
        $this->assertElementVisible(
            '//input[@name="confstrs[paymorrowPaymorrowKeyTest]"]',
            'Settings field "paymorrowPaymorrowKeyTest" should be visible.'
        );

        $this->assertElementNotPresent(
            '//button[@id="register_certificate_btn_live"]',
            'There should be no Paymorrow LIVE certificated generation button.'
        );
        $this->assertElementNotPresent(
            '//button[@id="register_certificate_btn_test"]',
            'There should be no Paymorrow TEST certificated generation button.'
        );
    }

    public function testPaymorrowModuleSettingsShowKeyGenerationToolOnceMerchantIdConfigured()
    {
        $this->_openPaymorrowSettings();

        // Open API settings
        $this->click('//b[text()="API Configuration"]');

        // Make sure merchant ID is configured
        $this->type('//input[@name="confstrs[paymorrowMerchantIdTest]"]', PAYMORROW_SETTING_MERCHANT_ID);
        $this->clickAndWait('//input[@name="save"]');

        // Open API settings again
        $this->click('//b[text()="API Configuration"]');

        $this->assertElementNotVisible(
            '//input[@name="confstrs[paymorrowKeysJson]"]',
            'Settings field "paymorrowKeysJson" should not be visible.'
        );
        $this->assertElementNotVisible(
            '//input[@name="confstrs[paymorrowPrivateKey]"]',
            'Settings field "paymorrowPrivateKey" should not be visible.'
        );
        $this->assertElementNotVisible(
            '//input[@name="confstrs[paymorrowPrivateKeyTest]"]',
            'Settings field "paymorrowPrivateKeyTest" should not be visible.'
        );
        $this->assertElementNotVisible(
            '//input[@name="confstrs[paymorrowPublicKey]"]',
            'Settings field "paymorrowPublicKey" should not be visible.'
        );
        $this->assertElementNotVisible(
            '//input[@name="confstrs[paymorrowPublicKeyTest]"]',
            'Settings field "paymorrowPublicKeyTest" should not be visible.'
        );
        $this->assertElementNotVisible(
            '//input[@name="confstrs[paymorrowPaymorrowKey]"]',
            'Settings field "paymorrowPaymorrowKey" should not be visible.'
        );
        $this->assertElementNotVisible(
            '//input[@name="confstrs[paymorrowPaymorrowKeyTest]"]',
            'Settings field "paymorrowPaymorrowKeyTest" should not be visible.'
        );

        $this->assertElementVisible(
            '//button[@id="register_certificate_btn_live"]',
            'There should be no Paymorrow LIVE certificated generation button.'
        );
        $this->assertElementVisible(
            '//button[@id="register_certificate_btn_test"]',
            'There should be no Paymorrow TEST certificated generation button.'
        );
    }

    public function testPaymorrowModuleSettingsCertificatesToolPopUpIsGeneratingKeys()
    {
        $this->_openPaymorrowSettings();

        // Open API settings
        $this->click('//b[text()="API Configuration"]');

        // Enter a merchant ID
        $this->type('//input[@name="confstrs[paymorrowMerchantIdTest]"]', PAYMORROW_SETTING_MERCHANT_ID);
        $this->clickAndWait('//input[@name="save"]');

        // Open API settings again
        $this->click('//b[text()="API Configuration"]');

        $this->assertElementVisible(
            '//button[@id="register_certificate_btn_live"]',
            'There should be no Paymorrow LIVE certificated generation button.'
        );
        $this->assertElementVisible(
            '//button[@id="register_certificate_btn_test"]',
            'There should be no Paymorrow TEST certificated generation button.'
        );

        // Press certificate generation button
        $this->click('//button[@id="register_certificate_btn_test"]');

        // Make sure pop-up is now visible and certificates are being generated
        $this->assertElementVisible(
            '//div[@id="pm_certificate_form_test"]',
            'Certificate generation pup-up should be opened.'
        );
        $this->assertElementPresent(
            '//td[@class="pm-header" and text()="Initialization Code:"]',
            'Initialization code field should be visible.'
        );
        $this->assertElementVisible(
            '//div[@class="progress"]',
            'On pop-up just opened snipping wheel should indicate certificates are being generated. '
        );
        $this->assertElementPresent(
            '//button[@id="buttonRegister" and @disabled="disabled"]',
            'Certificate registration button remains disabled while certificates are being generated.'
        );

        // Wait several minutes and check if form is ready to confirm generated certificates
        sleep(PAYMORROW_PARAM_DELAY * 8);

        $this->assertElementNotPresent(
            'div[@class="progress-desc"]',
            'Snipping wheel should be gone once certificates are generated.'
        );
        $this->assertElementNotPresent(
            '//button[@id="buttonRegister" and @disabled="disabled"]',
            'Initialization code submit button should not be disabled.'
        );
        $this->assertElementPresent(
            '//button[@id="buttonRegister"]',
            'Initialization code submit button should not be enabled.'
        );
    }

    public function testPaymentMethodsSetupContainPaymorrowTabAndRelevantFields()
    {
        $this->_activateAndSetupPaymorrowModuleAndOpenPaymentForm();
    }

    public function testPaymentMethodsInvoiceMappedAndConfigured()
    {
        $this->_activateAndSetupPaymorrowModuleAndOpenPaymentForm(true);

        // First unset all values to default if any were configured
        if ($this->_oPage->find('xpath', '//input[@name="editval[oxpayments__oxpspaymorrowactive]" and @checked]')
        ) {
            $this->type('//input[@name="editval[oxpayments__oxsort]"]', '0');
            $this->type('//input[@name="editval[oxpayments__oxtoamount]"]', '1000000');
            $this->type('//input[@name="editval[oxpayments__oxfromamount]"]', '0');
            $this->type('//input[@name="editval[oxpayments__oxaddsum]"]', '0');
            $this->click('//input[@name="editval[oxpayments__oxpspaymorrowactive]"]');
            $this->clickAndWait('//input[@name="save"]');
        }

        // Make sure payment method is not activated and form is disabled
        $this->assertElementNotPresent(
            '//input[@name="editval[oxpayments__oxpspaymorrowactive]" and @checked]',
            'Activation checkbox should not be checked.'
        );
        $this->assertElementPresent(
            '//input[@name="editval[oxpayments__oxpspaymorrowactive]"]',
            'Activation checkbox should not be still available.'
        );
        $this->assertElementPresent(
            '//input[@name="editval[oxpayments__oxpspaymorrowmap]" and @value="1" and @disabled]',
            'Payment method type radio should be disabled.'
        );

        // Now activate the method
        $this->click('//input[@name="editval[oxpayments__oxpspaymorrowactive]"]');
        $this->clickAndWait('//input[@name="save"]');

        // Map it as Invoice payment method, fill some form values and save the form
        $this->click('//input[@name="editval[oxpayments__oxpspaymorrowmap]" and @value="1"]');
        $this->type('//input[@name="editval[oxpayments__oxsort]"]', '11');
        $this->clickAndWait('//input[@name="save"]');

        // Finally go to Main tab and check the form
        $this->openTab('Main');
        sleep(PAYMORROW_PARAM_DELAY);
        $this->assertElementPresent(
            '//input[@name="editval[oxpayments__oxsort]" and @value="11"]',
            'Sort form should have same value as was set in Paymorrow tab.'
        );
    }

    public function testPaymentMethodsDirectDebitMappedAndConfigured()
    {
        $this->_activateAndSetupPaymorrowModuleAndOpenPaymentForm(false);

        // First unset all values to default if any were configured
        if ($this->_oPage->find('xpath', '//input[@name="editval[oxpayments__oxpspaymorrowactive]" and @checked]')
        ) {
            $this->type('//input[@name="editval[oxpayments__oxsort]"]', '0');
            $this->type('//input[@name="editval[oxpayments__oxtoamount]"]', '1000000');
            $this->type('//input[@name="editval[oxpayments__oxfromamount]"]', '0');
            $this->type('//input[@name="editval[oxpayments__oxaddsum]"]', '0');
            $this->click('//input[@name="editval[oxpayments__oxpspaymorrowactive]"]');
            $this->clickAndWait('//input[@name="save"]');
        }

        // Make sure payment method is not activated and form is disabled
        $this->assertElementNotPresent(
            '//input[@name="editval[oxpayments__oxpspaymorrowactive]" and @checked]',
            'Activation checkbox should not be checked.'
        );
        $this->assertElementPresent(
            '//input[@name="editval[oxpayments__oxpspaymorrowactive]"]',
            'Activation checkbox should not be still available.'
        );
        $this->assertElementPresent(
            '//input[@name="editval[oxpayments__oxpspaymorrowmap]" and @value="2" and @disabled]',
            'Payment method type radio should be disabled.'
        );

        // Now activate the method
        $this->click('//input[@name="editval[oxpayments__oxpspaymorrowactive]"]');
        $this->clickAndWait('//input[@name="save"]');

        // Map it as Direct Debit payment method, fill some form values and save the form
        $this->click('//input[@name="editval[oxpayments__oxpspaymorrowmap]" and @value="1"]');
        $this->type('//input[@name="editval[oxpayments__oxsort]"]', '22');
        $this->type('//input[@name="editval[oxpayments__oxaddsum]"]', '1.22');
        $this->clickAndWait('//input[@name="save"]');

        // Finally go to Main tab and check the form
        $this->openTab('Main');
        sleep(PAYMORROW_PARAM_DELAY);
        $this->assertElementPresent(
            '//input[@name="editval[oxpayments__oxsort]" and @value="22"]',
            'Sort form should have same sort value as was set in Paymorrow tab.'
        );
        $this->assertElementPresent(
            '//input[@name="editval[oxpayments__oxaddsum]" and @value="1.22"]',
            'Sort form should have same surcharge value as was set in Paymorrow tab.'
        );
    }


    /*----------------------------*
     *- Back end testing helpers -*
     *----------------------------*/

    /**
     * Log in to back end, make sure Paymorrow module is active and open its settings tab.
     */
    protected function _openPaymorrowSettings()
    {
        $this->loginAdmin('Extensions', 'Modules', true, PAYMORROW_USER_ADMIN, PAYMORROW_USER_ADMIN);

        // Select Paymorrow module and open overview tab
        $this->assertElementPresent(
            '//div[@id="liste"]//a[text()="Paymorrow Payments"]',
            'Paymorrow module should be in the list.'
        );
        $this->clickAndWait('//div[@id="liste"]//a[text()="Paymorrow Payments"]');
        $this->openTab('Overview');

        // Activate module if it is not yet active
        if ($this->_oPage->find('xpath', '//input[@id="module_activate"]')) {

            $this->clickAndWait('//input[@id="module_activate"]');

            // Check if the module was activated properly
            $this->assertElementPresent('//input[@id="module_deactivate"]', 'Now button "Deactivate" should appear.');

            $this->frame('list');

            $this->waitForElement('//div[@class="tabs"]//a[text()="Log"]', 10, true);
            $this->assertElementPresent(
                '//div[@class="tabs"]//a[text()="Log"]',
                'Paymorrow "Log" tab should appear.'
            );
        }

        // Open settings
        $this->openTab('Settings');
        sleep(PAYMORROW_PARAM_DELAY);
        $this->assertElementPresent('//b[text()="API Configuration"]', 'Setting should contain API Configuration');
        $this->assertElementPresent('//b[text()="Order Data Update"]', 'Setting should contain Order Data Update');
    }

    /**
     * Log in to back end, make sure Paymorrow module is active and enter all settings to make it work.
     */
    protected function _activateAndSetupPaymorrowModule()
    {
        $this->_openPaymorrowSettings();

        // Open API settings
        $this->click('//b[text()="API Configuration"]');

        // Remove merchant ID if its is set to make sure certificate fields are visible
        $this->type('//input[@name="confstrs[paymorrowMerchantIdTest]"]', '');
        $this->clickAndWait('//input[@name="save"]');

        // Open API settings again
        $this->click('//b[text()="API Configuration"]');

        // Enter a merchant ID and certificates
        $this->type('//input[@name="confstrs[paymorrowMerchantIdTest]"]', PAYMORROW_SETTING_MERCHANT_ID);

        // Values too long probably - cause "Unable to connect !" error...
        /*$this->type( '//input[@name="confstrs[paymorrowKeysJson]"]', PAYMORROW_SETTING_ALL_KEYS );
        $this->type( '//input[@name="confstrs[paymorrowPrivateKeyTest]"]', PAYMORROW_SETTING_PRIVATE_KEY );
        $this->type( '//input[@name="confstrs[paymorrowPublicKeyTest]"]', PAYMORROW_SETTING_PUBLIC_KEY );
        $this->type( '//input[@name="confstrs[paymorrowPaymorrowKeyTest]"]', PAYMORROW_SETTING_PM_PUBLIC_KEY );*/
        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam('paymorrowKeysJson', PAYMORROW_SETTING_ALL_KEYS);
        $oConfig->saveShopConfVar(
            'str', 'paymorrowKeysJson', PAYMORROW_SETTING_ALL_KEYS, null, 'module:oxpspaymorrow'
        );
        $oConfig->setConfigParam('paymorrowPrivateKeyTest', PAYMORROW_SETTING_PRIVATE_KEY);
        $oConfig->saveShopConfVar(
            'str', 'paymorrowPrivateKeyTest', PAYMORROW_SETTING_PRIVATE_KEY, null, 'module:oxpspaymorrow'
        );
        $oConfig->setConfigParam('paymorrowPublicKeyTest', PAYMORROW_SETTING_PUBLIC_KEY);
        $oConfig->saveShopConfVar(
            'str', 'paymorrowPublicKeyTest', PAYMORROW_SETTING_PUBLIC_KEY, null, 'module:oxpspaymorrow'
        );
        $oConfig->setConfigParam('paymorrowPaymorrowKeyTest', PAYMORROW_SETTING_PM_PUBLIC_KEY);
        $oConfig->saveShopConfVar(
            'str', 'paymorrowPaymorrowKeyTest', PAYMORROW_SETTING_PM_PUBLIC_KEY, null, 'module:oxpspaymorrow'
        );

        $this->clickAndWait('//input[@name="save"]');
    }

    /**
     * Log in to back end, make sure Paymorrow module is active and enter all settings to make it work and
     * open payment methods configuration with a selected method and Paymorrow tab open.
     *
     * @param bool $blUseInvoice Select Invoice method if true, Direct Debit otherwise
     */
    protected function _activateAndSetupPaymorrowModuleAndOpenPaymentForm($blUseInvoice = true)
    {
        if (!empty($blUseInvoice)) {
            $sMethodDeName = 'Rechnung';
            $sMethodEnName = 'Invoice';
        } else {
            $sMethodDeName = 'Bankeinzug/Lastschrift';
            $sMethodEnName = 'Direct Debit';
        }

        $this->_activateAndSetupPaymorrowModule();

        // Go to payment methods settings and check for Paymorrow tab and fields
        $this->selectMenu('Shop Settings', 'Payment Methods');

        $this->filterPaymentMethod($sMethodDeName);

        // Select a payment method
        $this->assertElementPresent(
            sprintf(
                '//div[@id="liste"]//a[text()="%s"] | //div[@id="liste"]//a[text()="%s"]',
                $sMethodDeName,
                $sMethodEnName
            ),
            'There should be a default Invoice payment method.'
        );
        $this->clickAndWait(
            sprintf(
                '//div[@id="liste"]//a[text()="%s"] | //div[@id="liste"]//a[text()="%s"]',
                $sMethodDeName,
                $sMethodEnName
            )
        );

        // Check if Paymorrow tab exists and open it
        $this->assertElementPresent(
            '//td[contains(@class, "tab")]//a[text()="Paymorrow"]',
            'There should be a Paymorrow tab.'
        );
        $this->clickAndWait('//td[contains(@class, "tab")]//a[text()="Paymorrow"]');
        $this->frame('edit');
        sleep(PAYMORROW_PARAM_DELAY);
        $this->openTab('Paymorrow');

        // Check if all relevant fields exist
        $this->assertElementPresent(
            '//input[@name="editval[oxpayments__oxpspaymorrowactive]"]',
            'Paymorrow payment activation checkbox should be in the form.'
        );
        $this->assertElementPresent(
            '//input[@name="editval[oxpayments__oxpspaymorrowmap]" and @value="1"]',
            'Paymorrow payment mapping radio button for Invoice should be in the form.'
        );
        $this->assertElementPresent(
            '//input[@name="editval[oxpayments__oxpspaymorrowmap]" and @value="2"]',
            'Paymorrow payment mapping radio button for Direct Debit should be in the form.'
        );
        $this->assertElementPresent(
            '//input[@name="editval[oxpayments__oxaddsum]"]',
            'Payment surcharge value field should be in the form.'
        );
        $this->assertElementPresent(
            '//select[@name="editval[oxpayments__oxaddsumtype]"]',
            'Payment surcharge type field should be in the form.'
        );
        $this->assertElementPresent(
            '//input[@name="editval[oxpayments__oxfromamount]"]',
            'Payment amount from field should be in the form.'
        );
        $this->assertElementPresent(
            '//input[@name="editval[oxpayments__oxtoamount]"]',
            'Payment amount to field should be in the form.'
        );
        $this->assertElementPresent(
            '//input[@name="editval[oxpayments__oxchecked]"]',
            'Payment selected checkbox should be in the form.'
        );
        $this->assertElementPresent(
            '//input[@name="editval[oxpayments__oxsort]"]',
            'Payment sort order field should be in the form.'
        );
    }


    /*-----------------*
     *- Debug helpers -*
     *-----------------*/

    /**
     * Find element and print its textual content.
     *
     * @param string $sLocator
     * @param string $sSelector (optional) Default is "xpath", also available "css"
     */
    protected function _elementDump($sLocator, $sSelector = 'xpath')
    {
        $oElement = $this->_oPage->find($sSelector, $sLocator);

        if (is_null($oElement)) {
            $sContent = '!_NOT_FOUND_!';
        } else {
            $sContent = $oElement->getText();
        }

        printf(PHP_EOL . 'LOCATOR "%s": "%s"' . PHP_EOL, $sLocator, $sContent);
    }

    /**
     * Filter payment by description.
     *
     * @param $paymentDescription
     */
    private function filterPaymentMethod($paymentDescription)
    {
        $this->type("where[oxpayments][oxdesc]", $paymentDescription);
        $this->clickAndWait("submitit");
    }
}
