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
 * Class OxpsPaymorrowOrder extends order
 *
 * @see order
 */
class OxpsPaymorrowOrder extends OxpsPaymorrowOrder_parent
{

    /**
     * Overridden method - checks type of payment
     *
     * @return mixed
     */
    public function render()
    {
        $this->_setPaymorrowTypeOfPayment();
        $this->_checkForErrorsToRedirect();

        return $this->_OxpsPaymorrowOrder_render_parent();
    }


    /**
     * Overrides `oxpayments__oxdesc` in 4th Order step
     * under line "Type of Payment" and sets it to Paymorrow: Invoice/Direct Debit
     */
    protected function _setPaymorrowTypeOfPayment()
    {
        /** @var OxpsPaymorrowOxPayment|oxPayment $oxPayment */
        $oxPayment = $this->getPayment();

        if (($oxPayment instanceof oxPayment) and $oxPayment->isPaymorrowActiveAndMapped()) {
            $this->_updatePaymentDescription($oxPayment);
        }
    }

    /**
     * Update payment model description fields with custom translations and set the payment.
     *
     * @param OxpsPaymorrowOxPayment|oxPayment $oxPayment
     */
    protected function _updatePaymentDescription(oxPayment $oxPayment)
    {
        $oLang = oxRegistry::getLang();

        if ($oxPayment->getPaymorrowPaymentType() == 'pm_invoice') {
            $oxPayment->oxpayments__oxdesc = new oxField(
                $oLang->translateString('PAYMORROW_PAYMENT_METHOD_NAME_INVOICE')
            );
        } elseif ($oxPayment->getPaymorrowPaymentType() == 'pm_sdd') {
            $oxPayment->oxpayments__oxdesc = new oxField(
                $oLang->translateString('PAYMORROW_PAYMENT_METHOD_NAME_DIRECT_DEBIT')
            );
        }

        $this->_oPayment = $oxPayment;
    }

    /**
     * Overridden for Paymorrow Services validation
     *
     * @param $iSuccess
     *
     * @return string
     */
    protected function _getNextStep( $iSuccess )
    {
        /** @var OxpsPaymorrowResponseHandler $oPmResponseHandler */
        $oPmResponseHandler = oxRegistry::get( 'OxpsPaymorrowResponseHandler' );

        /**
         * If after validating confirmOrder against Paymorrow services there are errors,
         * return to order step and render error message
         */
        if ( $oPmResponseHandler->hasErrors() ) {
            $iErrorCode = $oPmResponseHandler->getErrorCode();

            /** @var OxpsPaymorrowErrorHandler $oPmErrorHandler */
            $oPmErrorHandler = oxNew( 'OxpsPaymorrowErrorHandler' );

            $oPmErrorHandler->redirectWithError( $iErrorCode );
        }

        /**
         * At this step assuming everything went fine
         * we need to delete SESSION variables created by Paymorrow
         * to avoid any incompatibilities if user has decided to
         * order again
         *
         * @doc: oxid_js_plugin.doc - 7.1 Controlling of customer’s browser session storage
         *
         * sess_challenge is deleted by `ThankYou` controller
         */
        $oSession = oxRegistry::getSession();
        $oSession->deleteVariable('pm_verify');
        $oSession->deleteVariable('pm_response');
        $oSession->deleteVariable('pm_responseINVOICE');
        $oSession->deleteVariable('pm_responseSDD');
        $oSession->deleteVariable('pm_order_transaction_id');
        $oSession->deleteVariable('pm_order_transaction_idINVOICE');
        $oSession->deleteVariable('pm_order_transaction_idSDD');

        // Set payment method error instead of shipping method error
        if ( $iSuccess == 4 ) {
            $iSuccess = 5;
        }

        return $this->_OxpsPaymorrowOrder_getNextStep_parent( $iSuccess );
    }

    /**
     * Check session for init error and redirect to payment step is any found.
     * The error or order declination entries are set during order confirmation.
     * This data is used on payment step ho inform user and handle payment forms and it is always unset there.
     *
     * @codeCoverageIgnore
     */
    protected function _checkForErrorsToRedirect()
    {
        if ( oxRegistry::getSession()->getVariable( 'pm_init_data' ) ) {
            $sUrl = oxRegistry::getConfig()->getShopCurrentUrl() . "cl=payment";
            $sUrl = oxRegistry::get( "oxUtilsUrl" )->processUrl( $sUrl );

            oxRegistry::getUtils()->redirect( $sUrl, false );
        }
    }


    /**
     * Calls parent _getNextStep method
     *
     * @codeCoverageIgnore
     *
     * @param integer $iSuccess
     */
    protected function _OxpsPaymorrowOrder_getNextStep_parent( $iSuccess )
    {
        return parent::_getNextStep( $iSuccess );
    }

    /**
     * Calls parent render() method
     *
     * @codeCoverageIgnore
     *
     * @return mixed
     */
    protected function _OxpsPaymorrowOrder_render_parent()
    {
        return parent::render();
    }
}
