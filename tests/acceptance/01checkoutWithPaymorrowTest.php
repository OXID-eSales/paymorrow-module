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
 * Class Acceptance_01checkoutWithPaymorrowTest.
 * Test different checkout cases using Paymorrow payment methods.
 */
class Acceptance_01checkoutWithPaymorrowTest extends PaymorrowAcceptanceTestCase
{
    protected $_sShopIdParam = '';

    /** @var string Language id. */
    protected $translateLanguageId = '0';

    /**
     * Prepare shop for testing.
     */
    public function setUp()
    {
        parent::setUp();

        $this->getTranslator()->setLanguage(0);

        if (isSUBSHOP && oxSHOPID) {
            $this->getTranslator()->setLanguage(1);
            $this->_sShopIdParam = "shp=" . oxSHOPID;
        }

        // Open shop
        $this->openShop();
    }

    /*---------------------------------------*
     *- Test with different payment methods -*
     *---------------------------------------*/

    public function testPaymorrowInvoicePaymentWithLoggedInAndTotallyValidUser()
    {
        // Log in with user, who has all valid profile and address data
        $this->loginInFrontend( PAYMORROW_USER_VALID, PAYMORROW_USER_VALID );

        // Clear user basket
        $this->_clearBasket();

        // Add an article to basket
        $this->addToBasket( "dc5ffdf380e15674b56dd562a7cb6aec" ); //3503

        // Checkout and place order with Paymorrow Invoice method and validate it
        $this->_makePaymentAndOrderNow();
    }

    public function testPaymorrowDirectDebitPaymentWithLoggedInAndTotallyValidUser()
    {
        // Log in with user, who has all valid profile and address data
        $this->loginInFrontend( PAYMORROW_USER_VALID, PAYMORROW_USER_VALID );

        // Add an article to basket
        $this->addToBasket( "f4f73033cf5045525644042325355732" ); //3788

        // Checkout and place order with Paymorrow Direct Debit method and validate it
        $this->_makePaymentAndOrderNow( false );
    }


    /*--------------------------------------*
     *- Test with different basket content -*
     *--------------------------------------*/

    public function testPaymorrowInvoicePaymentWithVariantOfSameArticleInBasket()
    {
        // Log in with user, who has all valid profile and address data
        $this->loginInFrontend( PAYMORROW_USER_VALID, PAYMORROW_USER_VALID );

        // Add some variant of same article
        $this->addToBasket( "6b62dc6bf5b7723743d514eb1b7a7f87" ); //3583 - 1607-02-WHT-5-7 - XL | While
        $this->addToBasket( "6b68f8516ba4d81bace5295830b7f5d6" ); //3583 - 1607-02-WHT-2-7 -  S | White

        // Checkout and place order with Paymorrow Invoice method and validate it
        $this->_makePaymentAndOrderNow();
    }

    public function testPaymorrowInvoicePaymentWithManyDifferentArticles()
    {
        // Log in with user, who has all valid profile and address data
        $this->loginInFrontend( PAYMORROW_USER_VALID, PAYMORROW_USER_VALID );

        // Add different articles
        $this->addToBasket( "dc5ffdf380e15674b56dd562a7cb6aec" ); //3503

        $this->addToBasket( "6b62dc6bf5b7723743d514eb1b7a7f87" ); //3583 - 1607-02-WHT-5-7 - XL | While
        $this->addToBasket( "6b68f8516ba4d81bace5295830b7f5d6" ); //3583 - 1607-02-WHT-2-7 -  S | White

        $this->addToBasket( "f33d5bcc7135908fd36fc736c643aa1c" ); //1506

        $this->addToBasket( "oiaa81b5e002fc2f73b9398c361c0b97" ); //10101

        // Checkout and place order with Paymorrow Invoice method and validate it
        $this->_makePaymentAndOrderNow();
    }

    public function testPaymorrowInvoicePaymentWithPercentVoucherInBasket()
    {
        // Log in with user, who has all valid profile and address data
        $this->loginInFrontend( PAYMORROW_USER_VALID, PAYMORROW_USER_VALID );

        // Add an article to basket
        $this->addToBasket( "dc5ffdf380e15674b56dd562a7cb6aec" ); //3503

        // Add percent voucher to the basket
        $this->_addVoucher( PAYMORROW_VOUCHER_PERCENT );

        // Checkout and place order with Paymorrow Invoice method and validate it
        $this->_makePaymentAndOrderNow();
    }

    public function testPaymorrowInvoicePaymentWithAbsoluteVoucherInBasket()
    {
        // Log in with user, who has all valid profile and address data
        $this->loginInFrontend( PAYMORROW_USER_VALID, PAYMORROW_USER_VALID );

        // Add an article to basket
        $this->addToBasket( "f4f73033cf5045525644042325355732" ); //3788

        // Add absolute voucher to the basket
        $this->_addVoucher( PAYMORROW_VOUCHER_ABSOLUTE );

        // Checkout and place order with Paymorrow Invoice method and validate it
        $this->_makePaymentAndOrderNow();
    }

    public function testPaymorrowInvoicePaymentWithMultipleMixedVouchersInBasket()
    {
        // Log in with user, who has all valid profile and address data
        $this->loginInFrontend( PAYMORROW_USER_VALID, PAYMORROW_USER_VALID );

        // Add an article to basket
        $this->addToBasket( "dc5ffdf380e15674b56dd562a7cb6aec" ); //3503

        // Add different vouchers to the basket
        $this->_addVoucher( PAYMORROW_VOUCHER_ABSOLUTE );
        $this->_addVoucher( PAYMORROW_VOUCHER_PERCENT );
        $this->_addVoucher( PAYMORROW_VOUCHER_PERCENT );
        $this->_addVoucher( PAYMORROW_VOUCHER_ABSOLUTE );

        // Checkout and place order with Paymorrow Invoice method and validate it
        $this->_makePaymentAndOrderNow();
    }

    public function testPaymorrowDirectDebitPaymentWithMultipleMixedVouchersAndDifferentArticlesInBasket()
    {
        // Log in with user, who has all valid profile and address data
        $this->loginInFrontend( PAYMORROW_USER_VALID, PAYMORROW_USER_VALID );

        // Add different articles
        $this->addToBasket( "dc5ffdf380e15674b56dd562a7cb6aec" ); //3503

        $this->addToBasket( "6b62dc6bf5b7723743d514eb1b7a7f87" ); //3583 - 1607-02-WHT-5-7 - XL | While
        $this->addToBasket( "6b68f8516ba4d81bace5295830b7f5d6" ); //3583 - 1607-02-WHT-2-7 -  S | White

        $this->addToBasket( "f33d5bcc7135908fd36fc736c643aa1c" ); //1506

        $this->addToBasket( "oiaa81b5e002fc2f73b9398c361c0b97" ); //10101

        // Add different vouchers to the basket
        $this->_addVoucher( PAYMORROW_VOUCHER_ABSOLUTE );
        $this->_addVoucher( PAYMORROW_VOUCHER_PERCENT );
        $this->_addVoucher( PAYMORROW_VOUCHER_PERCENT );
        $this->_addVoucher( PAYMORROW_VOUCHER_ABSOLUTE );

        // Checkout and place order with Paymorrow Direct Debit method and validate it
        $this->_makePaymentAndOrderNow( false );
    }

    public function testPaymorrowInvoicePaymentWithZeroPriceItem()
    {
        // Log in with user, who has all valid profile and address data
        $this->loginInFrontend( PAYMORROW_USER_VALID, PAYMORROW_USER_VALID );

        // Add free downloadable article
        $this->addToBasket( "oiaa81b5e002fc2f73b9398c361c0b97" ); //10101

        // Go to checkout payment step
        $this->_oMinkSession->visit( shopURL . "?cl=payment&{$this->_sShopIdParam}" );

        // Make sure the page is loaded but Paymorrow payment methods not visible
        $this->assertElementPresent(
            '//section[@id="content"]//li[contains(@class, "step3 active")]',
            'Checkout payment page should be open.'
        );
        $this->assertElementNotVisible(
            sprintf( '//div[@id="%s"]', PAYMORROW_TAG_ID_DIV_INVOCE ),
            'Paymorrow Invoice payment method should not be in the list.'
        );
        $this->assertElementNotVisible(
            sprintf( '//div[@id="%s"]', PAYMORROW_TAG_ID_DIV_DIRECT_DEBIT ),
            'Paymorrow Direct Debit payment method should not be in the list.'
        );
    }

    public function testPaymorrowInvoicePaymentWithTooBitTotalAmount()
    {
        // Log in with user, who has all valid profile and address data
        $this->loginInFrontend( PAYMORROW_USER_VALID, PAYMORROW_USER_VALID );

        // Add an article to basket
        $this->addToBasket( "dc5ffdf380e15674b56dd562a7cb6aec", 10000 ); //3503

        // Go to checkout payment step
        $this->_oMinkSession->visit( shopURL . "?cl=payment&{$this->_sShopIdParam}" );

        // Make sure the page is loaded but Paymorrow payment methods not visible
        $this->assertElementPresent(
            '//section[@id="content"]//li[contains(@class, "step3 active")]',
            'Checkout payment page should be open.'
        );
        $this->assertElementNotVisible(
            sprintf( '//div[@id="%s"]', PAYMORROW_TAG_ID_DIV_INVOCE ),
            'Paymorrow Invoice payment method should not be in the list.'
        );
        $this->assertElementNotVisible(
            sprintf( '//div[@id="%s"]', PAYMORROW_TAG_ID_DIV_DIRECT_DEBIT ),
            'Paymorrow Direct Debit payment method should not be in the list.'
        );
    }


    /*------------------------------------*
     *- Test with different profile data -*
     *------------------------------------*/

    public function testPaymorrowInvoicePaymentWithIncompleteUserData()
    {
        // Log in with user, who has all valid profile and address data
        $this->loginInFrontend( PAYMORROW_USER_INCOMPLETE, PAYMORROW_USER_INCOMPLETE );

        // Clear user basket
        $this->_clearBasket();

        // Add an article to basket
        $this->addToBasket( "dc5ffdf380e15674b56dd562a7cb6aec" ); //3503

        // Go to payment page and check that Paymorrow payment method is available
        $this->_goAndCheckPaymentStep();

        // User DoB and phone fields should be visible
        $this->assertElementVisible(
            sprintf( '//input[@name="%s"]', PAYMORROW_TAG_NAME_DOB_DAY ),
            'DoB day should be visible.'
        );
        $this->assertElementVisible(
            sprintf( '//input[@name="%s"]', PAYMORROW_TAG_NAME_DOB_MONTH ),
            'DoB month should be visible.'
        );
        $this->assertElementVisible(
            sprintf( '//input[@name="%s"]', PAYMORROW_TAG_NAME_DOB_YEAR ),
            'DoB year should be visible.'
        );
        $this->assertElementVisible(
            sprintf( '//input[@name="%s"]', PAYMORROW_TAG_NAME_PHONE ),
            'Phone field should be visible.'
        );

        // Enter missing values
        $this->type( sprintf( '//input[@name="%s"]', PAYMORROW_TAG_NAME_DOB_DAY ), PAYMORROW_TEST_VAL_DOB_DAY );
        $this->type( sprintf( '//input[@name="%s"]', PAYMORROW_TAG_NAME_DOB_MONTH ), PAYMORROW_TEST_VAL_DOB_MONTH );
        $this->type( sprintf( '//input[@name="%s"]', PAYMORROW_TAG_NAME_DOB_YEAR ), PAYMORROW_TEST_VAL_DOB_YEAR);
        $this->type( sprintf( '//input[@name="%s"]', PAYMORROW_TAG_NAME_PHONE ), PAYMORROW_TEST_VAL_PHONE );

        // Continue to make an order and check it
        $this->_makePaymentAndOrderNow( true, true );
    }

    public function testPaymorrowInvoicePaymentWithUserHavingDifferentShippingAddress()
    {
        // Log in with user, who has all valid profile and address data
        $this->loginInFrontend( PAYMORROW_USER_MULTIADDRESS, PAYMORROW_USER_MULTIADDRESS );

        // Clear user basket
        $this->_clearBasket();

        // Add an article to basket
        $this->addToBasket( "dc5ffdf380e15674b56dd562a7cb6aec" ); //3503

        // Checkout and place order with Paymorrow Invoice method and validate it
        $this->_makePaymentAndOrderNow();
    }

    /*--------------------------------*
     *- Test different special cases -*
     *--------------------------------*/

    public function testPaymorrowInvoicePaymentForManyOrdersInARow()
    {
        // Log in with user, who has all valid profile and address data
        $this->loginInFrontend( PAYMORROW_USER_MULTIADDRESS, PAYMORROW_USER_MULTIADDRESS );

        // Clear user basket
        $this->_clearBasket();

        // Add an article to basket
        $this->addToBasket( "dc5ffdf380e15674b56dd562a7cb6aec" ); //3503

        // Checkout and place order with Paymorrow Invoice method and validate it
        $this->_makePaymentAndOrderNow();

        // Add an article to basket
        $this->addToBasket( "f33d5bcc7135908fd36fc736c643aa1c" ); //1506

        // Checkout and place order with Paymorrow Invoice method and validate it
        $this->_makePaymentAndOrderNow( false );

        // Add some articles to basket
        $this->addToBasket( "dc5ffdf380e15674b56dd562a7cb6aec" ); //3503
        $this->addToBasket( "f33d5bcc7135908fd36fc736c643aa1c", 2 ); //1506

        // Checkout and place order with Paymorrow Invoice method and validate it
        $this->_makePaymentAndOrderNow();
    }

    public function testPaymorrowDirectDebitPaymentWithBasketManipulationBeingDone()
    {
        // Log in with user, who has all valid profile and address data
        $this->loginInFrontend( PAYMORROW_USER_VALID, PAYMORROW_USER_VALID );

        // Clear user basket
        $this->_clearBasket();

        // Add an article to basket
        $this->addToBasket( "dc5ffdf380e15674b56dd562a7cb6aec" ); //3503

        // Go to payment page and check that Paymorrow payment method is available
        $this->_goAndCheckPaymentStep( false );

        // Choose Paymorrow method to pay and confirm the form
        $this->click( sprintf( '//input[@id="%s"]', PAYMORROW_TAG_ID_RADIO_DIRECT_DEBIT ) );
        $this->click( sprintf( '//input[@id="%s"]', PAYMORROW_TAG_ID_CHECKBOX_DIRECT_DEBIT ) );

        // Additional fields to fill for Direct Debit case
        $this->type( sprintf( '//input[@id="%s"]', PAYMORROW_TAG_ID_IBAN ), PAYMORROW_TEST_VAL_IBAN );
        $this->type( sprintf( '//input[@id="%s"]', PAYMORROW_TAG_ID_BIC ), PAYMORROW_TEST_VAL_BIC );

        $this->clickAndWait('//button[@id="paymentNextStepBottom"]');
        // Wait while JavaScript makes all calls and redirects user

        // Check if user is on order confirmation page
        $this->assertElementNotPresent(
            sprintf( '//input[@id="%s"]', PAYMORROW_TAG_ID_CHECKBOX_INVOCE ),
            'User should be on order confirmation page already.'
        );
        $this->assertElementPresent(
            '//form[@id="orderConfirmAgbBottom"]',
            'Order confirmation form should be present on the page.'
        );
        $this->assertElementPresent(
            '//form[@id="orderConfirmAgbBottom"]//button[@type="submit"]',
            '"Order now" button should be present on the order confirmation page.'
        );

        // Add more items to basket
        $this->addToBasket( "dc5ffdf380e15674b56dd562a7cb6aec", 2 ); //3503

        // Go to checkout order step again
        $this->_oMinkSession->visit( shopURL . "?cl=order&{$this->_sShopIdParam}" );
        $this->assertElementPresent(
            '//form[@id="orderConfirmAgbBottom"]//button[@type="submit"]',
            '"Order now" button should be present on the order confirmation page.'
        );

        // Try to confirm order and if should still pass
        $this->clickAndWait( '//form[@id="orderConfirmAgbBottom"]//button[@type="submit"]' );
        $this->assertElementPresent( '//div[@id="thankyouPage"]', 'User should be on "Thank You" page.' );

        // Visit order history and find 3 items of article "3503"
        $this->_oMinkSession->visit( shopURL . "en/order-history/?{$this->_sShopIdParam}" );
        $this->assertElementPresent(
            '//ul[@class="orderList"]/li/table[@class="orderitems"]',
            'Order history should be not empty.'
        );

        // Find and check latest order number placeholder
        $oOrderNumberHolder = $this->_oPage->find(
            'xpath',
            '//ul[@class="orderList"]/li/table[@class="orderitems"]//span[contains(@id, "accOrderNo_")]'
        );
        $this->assertNotNull( $oOrderNumberHolder );

        // Get and check order number exists and is a numeric value
        $sOrderNumber = trim( $oOrderNumberHolder->getText() );
        $this->assertFalse( empty( $sOrderNumber ), 'Order number should not be empty.' );
        $this->assertTrue( is_numeric( $sOrderNumber ), 'Order number should be a numeric value.' );

        // Find order item and check quantity
        $oOrderItemQty = $this->_oPage->find(
            'xpath',
            sprintf(
                '//ul[@class="orderList"]/li/table[@class="orderitems"]//a[@id="accOrderLink_%d_1"]',
                (int) $sOrderNumber
            )
        );
        $this->assertNotNull( $oOrderItemQty );
        $quantityText = $oOrderItemQty->getText();
        $this->assertContains('3', $quantityText);
    }


    /*--------------------------------------------*
     *- Helpers for common assertions and checks -*
     *--------------------------------------------*/

    /**
     * Make a payment, place order and check it.
     * Default workflow for valid user profile, address and basket.
     *
     * @param bool $blInvoice       Use Invoice if true, Direct Debit otherwise.
     * @param bool $blSkipFirstStep Skip the step of going to checkout page
     */
    protected function _makePaymentAndOrderNow( $blInvoice = true, $blSkipFirstStep = false )
    {
        if ( empty( $blSkipFirstStep ) ) {
            // Go to payment page and check that Paymorrow payment method is available
            $this->_goAndCheckPaymentStep( $blInvoice );
        }

        // Select Paymorrow payment method and place an order
        $this->_payWithPaymorrowMethodAndConfirmOrder( $blInvoice );

        // Go to order history page to get order number
        $sOrderNumber = $this->_checkOrderHistoryForOrderNumber();

        // Check order and user payment data
        $sOrderId = $this->_checkOrderAndPaymentData( $sOrderNumber, $blInvoice );

        // Check Paymorrow log
        $this->_checkPaymorrowLog( $sOrderId );
    }

    /**
     * Go to checkout step 3 - payment page and check if it has a valid, initialized expected paymorrow payment method.
     *
     * @param bool $blInvoice Use Invoice if true, Direct Debit otherwise.
     */
    protected function _goAndCheckPaymentStep( $blInvoice = true )
    {
        if ( !empty( $blInvoice ) ) {

            // Invoice case
            $sPaymentMethodId        = PAYMORROW_TAG_ID_DIV_INVOCE;
            $sRadioButtonId          = PAYMORROW_TAG_ID_RADIO_INVOCE;
            $sConfirmationCheckboxId = PAYMORROW_TAG_ID_CHECKBOX_INVOCE;
        } else {

            // Direct Debit case
            $sPaymentMethodId        = PAYMORROW_TAG_ID_DIV_DIRECT_DEBIT;
            $sRadioButtonId          = PAYMORROW_TAG_ID_RADIO_DIRECT_DEBIT;
            $sConfirmationCheckboxId = PAYMORROW_TAG_ID_CHECKBOX_DIRECT_DEBIT;
        }

        // Go to checkout payment step
        $this->_oMinkSession->visit( shopURL . "?cl=payment&{$this->_sShopIdParam}" );

        // Make sure the page is loaded and Paymorrow payment method initialized
        $this->assertElementPresent(
            '//section[@id="content"]//li[contains(@class, "step3 active")]',
            'Checkout payment page should be open.'
        );
        $this->assertElementPresent(
            sprintf( '//div[@id="%s"]', $sPaymentMethodId ),
            'Expected paymorrow payment method should be in the list.'
        );

        $this->click( sprintf( '//input[@id="%s"]', $sRadioButtonId ) );

        $this->assertElementPresent(
            sprintf( '//div[@id="%s"]//input[@id="%s"]', $sPaymentMethodId, $sConfirmationCheckboxId ),
            'Paymorrow payment form is initialized.'
        );
        $this->assertElementVisible(
            sprintf( '//input[@id="%s"]', $sConfirmationCheckboxId ),
            'Paymorrow form has agreement checkbox visible.'
        );

        // Additional fields to check for Direct Debit case
        if ( empty( $blInvoice ) ) {
            $this->assertElementVisible(
                sprintf( '//input[@id="%s"]', PAYMORROW_TAG_ID_IBAN ),
                'Bank account field should be visible.'
            );
            $this->assertElementVisible(
                sprintf( '//input[@id="%s"]', PAYMORROW_TAG_ID_BIC ),
                'BIC code field should be visible.'
            );
        }
    }

    /**
     * Assuming payment page is loaded with Paymorrow payment form initialized, submit the form.
     * Wait till user is on order confirmation page and submit order.
     *
     * @param bool $blInvoice Use Invoice if true, Direct Debit otherwise.
     */
    protected function _payWithPaymorrowMethodAndConfirmOrder( $blInvoice = true )
    {
        if ( !empty( $blInvoice ) ) {

            // Invoice case
            $sRadioButtonId          = PAYMORROW_TAG_ID_RADIO_INVOCE;
            $sConfirmationCheckboxId = PAYMORROW_TAG_ID_CHECKBOX_INVOCE;
        } else {

            // Direct Debit case
            $sRadioButtonId          = PAYMORROW_TAG_ID_RADIO_DIRECT_DEBIT;
            $sConfirmationCheckboxId = PAYMORROW_TAG_ID_CHECKBOX_DIRECT_DEBIT;
        }

        // Choose Paymorrow method to pay and confirm the form
        $this->click( sprintf( '//input[@id="%s"]', $sRadioButtonId ) );
        $this->click( sprintf( '//input[@id="%s"]', $sConfirmationCheckboxId ) );

        // Additional fields to fill for Direct Debit case
        if ( empty( $blInvoice ) ) {
            $this->type( sprintf( '//input[@id="%s"]', PAYMORROW_TAG_ID_IBAN ), PAYMORROW_TEST_VAL_IBAN );
            $this->type( sprintf( '//input[@id="%s"]', PAYMORROW_TAG_ID_BIC ), PAYMORROW_TEST_VAL_BIC );
        }

        $this->clickAndWait( '//button[@id="paymentNextStepBottom"]' );

        // Check if user is on order confirmation page
        $this->assertElementNotPresent(
            sprintf( '//input[@id="%s"]', $sConfirmationCheckboxId ),
            'User should be on order confirmation page already.'
        );
        $this->assertElementPresent(
            '//form[@id="orderConfirmAgbBottom"]',
            'Order confirmation form should be present on the page.'
        );
        $this->assertElementPresent(
            '//form[@id="orderConfirmAgbBottom"]//button[@type="submit"]',
            '"Order now" button should be present on the order confirmation page.'
        );

        // Confirm order
        if ( $this->_oPage->find( 'xpath', '//input[@id="oxdownloadableproductsagreement"]' ) ) {
            $this->click( '//input[@id="oxdownloadableproductsagreement"]' );
        }

        $this->assertElementPresent(
            '//form[@id="orderConfirmAgbBottom"]//button[@type="submit"]',
            'There should be an "Order now" button.'
        );
        $this->clickAndWait( '//form[@id="orderConfirmAgbBottom"]//button[@type="submit"]' );

        $this->assertElementPresent( '//div[@id="thankyouPage"]', 'User should be on "Thank You" page.' );
    }

    /**
     * Go to order history page to check if it contains orders, check and get last order number.
     *
     * @return string Order number
     */
    protected function _checkOrderHistoryForOrderNumber()
    {
        // Go to order history and check if it exists
        $this->_oMinkSession->visit( shopURL . "en/order-history/?{$this->_sShopIdParam}" );
        $this->assertElementPresent(
            '//ul[@class="orderList"]/li/table[@class="orderitems"]',
            'Order history should be not empty.'
        );

        // Find and check latest order number placeholder
        $oOrderNumberHolder = $this->_oPage->find(
            'xpath',
            '//ul[@class="orderList"]/li/table[@class="orderitems"]//span[contains(@id, "accOrderNo_")]'
        );
        $this->assertNotNull( $oOrderNumberHolder );

        // Get and check order number exists and is a numeric value
        $sOrderNumber = trim( $oOrderNumberHolder->getText() );
        $this->assertFalse( empty( $sOrderNumber ), 'Order number should not be empty.' );
        $this->assertTrue( is_numeric( $sOrderNumber ), 'Order number should be a numeric value.' );

        return $sOrderNumber;
    }

    /**
     * Load order by number, check if it is valid.
     * Load user payment related to the order and check if it is valid and has Paymorrow data fields set.
     *
     * @param string $sOrderNumber
     * @param bool   $blInvoice Use Invoice if true, Direct Debit otherwise.
     *
     * @return string Order ID
     */
    protected function _checkOrderAndPaymentData( $sOrderNumber, $blInvoice = true )
    {
        // Load order
        $oOrder   = $this->_loadOrderByNumber( $sOrderNumber );
        $sOrderId = $oOrder->getId();

        // Check order fields
        $this->assertFalse( empty( $sOrderId ), 'Order should exist and be loaded by number.' );
        $this->assertFalse( empty( $oOrder->oxorder__oxpaymentid->value ), 'User payment Id should not be empty.' );

        // Load user payment
        $oUserPayment   = $this->_loadUserPayment( $oOrder->oxorder__oxpaymentid->value );
        $sUserPaymentId = $oUserPayment->getId();

        // Check user payment
        $this->assertFalse( empty( $sUserPaymentId ), 'User payment data should exist abd be loaded by ID.' );

        if ( !empty( $blInvoice ) ) {
            $this->assertFalse(
                empty( $oUserPayment->oxuserpayments__oxpspaymorrowbankname->value ),
                'User payment should have a bank name.'
            );
            $this->assertFalse(
                empty( $oUserPayment->oxuserpayments__oxpspaymorrowiban->value ),
                'User payment should have an IBAN code.'
            );
            $this->assertfalse(
                empty( $oUserPayment->oxuserpayments__oxpspaymorrowbic->value ),
                'User payment should have a BIC code.'
            );
        }

        $this->assertfalse(
            empty( $oUserPayment->oxuserpayments__oxpspaymorroworderid->value ),
            'User payment should have a temporary order ID value.'
        );

        return $sOrderId;
    }

    /**
     * Check if Paymorrow log dir and confirmed order file exist.
     * Check if current order is also in logs and has confirmed status.
     *
     * @param string $sOrderId
     */
    protected function _checkPaymorrowLog( $sOrderId )
    {
        $sShopPath = oxPATH;

        // Build and check logs folder path
        $sPaymorrowLogsPath = $sShopPath . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR .
                              'paymorrow' . DIRECTORY_SEPARATOR;

        $this->assertTrue( is_dir( $sPaymorrowLogsPath ), 'Paymorrow logs folder should exist.' );

        // Check confirmed orders log file
        $sConfirmedOrdersLogPath = sprintf(
            '%soxpspaymorrow_PM_handleConfirmOrderResponseOK_data-%s_log.txt', $sPaymorrowLogsPath, date( 'Y-m-d' )
        );

        $this->assertFileExists(
            $sConfirmedOrdersLogPath, 'Paymorrow log should contain latest confirmed orders data.'
        );

        // Get confirmed orders log and make sure it has current order confirmed
        $sLog = file_get_contents( $sConfirmedOrdersLogPath );

        $this->assertFalse( empty( $sLog ), 'Confirmed orders log should be not empty.' );
        $this->assertContains(
            sprintf( 'order_id: %s' . PHP_EOL . ' order_status: ACCEPTED_CONFIRMED', $sOrderId ),
            $sLog,
            'Confirmed orders log should contain the order with confirmed status.'
        );
    }


    /*-----------------*
     *- Debug helpers -*
     *-----------------*/

    /**
     * Find element and print its textual content.
     *
     * @param string $sLocator
     */
    protected function _elementDump( $sLocator )
    {
        $oElement = $this->_oPage->find( 'xpath', $sLocator );

        if ( is_null( $oElement ) ) {
            $sContent = '!_NOT_FOUND_!';
        } else {
            $sContent = $oElement->getText();
        }

        printf( PHP_EOL . 'LOCATOR "%s": "%s"' . PHP_EOL, $sLocator, $sContent );
    }


    /*-------------------------------------*
     *- Helpers not related to assertions -*
     *-------------------------------------*/

    /**
     * Load order by order number.
     *
     * @param string $sOrderNumber
     *
     * @return oxOrder
     */
    protected function _loadOrderByNumber( $sOrderNumber )
    {
        /** @var oxOrder $oOrder */
        $oOrder = oxNew( 'oxOrder' );
        $oOrder->assignRecord(
            sprintf(
                "SELECT * FROM `%s` WHERE `OXORDERNR` = %s",
                $oOrder->getViewName(),
                $sOrderNumber
            )
        );

        return $oOrder;
    }

    /**
     * Load user payment by ID.
     *
     * @param string $sUserPaymentId
     *
     * @return oxUserPayment
     */
    protected function _loadUserPayment( $sUserPaymentId )
    {
        /** @var oxUserPayment $oUserPayment */
        $oUserPayment = oxNew( 'oxUserPayment' );
        $oUserPayment->load( $sUserPaymentId );

        return $oUserPayment;
    }

    /**
     * Go to basket and add voucher with a given code.
     *
     * @param string $sVoucherCode
     */
    protected function _addVoucher( $sVoucherCode )
    {
        // Go to basket and make sure it has a voucher field
        $this->_oMinkSession->visit( shopURL . 'en/cart/?' . $this->_sShopIdParam );
        $this->assertElementPresent(
            '//div[@id="coupon"]//input[@name="voucherNr"]',
            'There should be a voucher box and field on non empty basket page.'
        );

        // Fill in the voucher code and submit the form
        $this->type( '//div[@id="coupon"]//input[@name="voucherNr"]', trim( (string) $sVoucherCode ) );
        $this->clickAndWait( '//div[@id="coupon"]//button[@class="submitButton"]' );
    }

    /**
     * Remove everything that is still in user basket.
     */
    protected function _clearBasket()
    {
        // Go to basket
        $this->_oMinkSession->visit( shopURL . 'en/cart/?' . $this->_sShopIdParam );

        // It is contains articles, remove all
        if ( $this->_oPage->find( 'xpath', '//input[@id="checkAll"]' ) ) {
            $this->click( '//input[@id="checkAll"]' );
            $this->clickAndWait( '//button[@id="basketRemove"]' );
        }

        // Go to home page
        $this->_oMinkSession->visit( shopURL . '?' . $this->_sShopIdParam );
    }
}
