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
 * Class OxpsPaymorrowOxPaymentGateway extends oxPaymentGateway
 *
 * @see oxPaymentGateway
 */
class OxpsPaymorrowOxPaymentGateway extends OxpsPaymorrowOxPaymentGateway_parent
{

    const PAYMORROW_RESPONSE_ORDER_ID = 'order_id';

    const PAYMORROW_RESPONSE_BANK_NAME = 'paymentInstruction_recipientBankName';

    const PAYMORROW_RESPONSE_SDD_IBAN = 'paymentInstruction_recipientIBAN';

    const PAYMORROW_RESPONSE_SDD_BIC = 'paymentInstruction_recipientBIC';


    /**
     * Overridden function calls Paymorrow services: confirmOrder
     *
     * @param                              $dAmount
     * @param oxOrder|OxpsPaymorrowOxOrder $oOrder
     *
     * @return bool|mixed
     */
    public function executePayment($dAmount, & $oOrder)
    {
        /** @var oxUserPayment|OxpsPaymorrowOxUserPayment $oUserPayment */
        $oUserPayment = $this->_oPaymentInfo;

        if (is_object($oUserPayment) && $oUserPayment->isUserPaymentPaymorrowMethod()) {

            // Set real order ID (OXID field) to session to be used in order confirmation during payment.
            $oOrderId = $oOrder->getId();
            $oOrder->savePaymorrowTemporaryOrderIdToSession($oOrderId);

            /** @var OxpsPaymorrowModule $oModule */
            $oModule = oxRegistry::get('OxpsPaymorrowModule');

            /** @var OxpsPaymorrowRequestControllerProxy $oPmRequestControllerProxy */
            $oPmRequestControllerProxy = oxNew('OxpsPaymorrowRequestControllerProxy');
            $oPmRequestControllerProxy->confirmOrder($oModule->getPaymentMethodName(), $oOrderId);

            /** @var OxpsPaymorrowResponseHandler $oPmResponseHandler */
            $oPmResponseHandler = oxRegistry::get('OxpsPaymorrowResponseHandler');

            /**
             * If Order has an error after validation from Paymorrow services invalidate order and delete it
             * If Payment data save failed return an error
             */
            if ($oPmResponseHandler->hasErrors() or
                !$this->_savePaymorrowUserPaymentData($oOrder, $oPmResponseHandler)
            ) {
                $this->_handleOrderResponseErrors($oPmResponseHandler);

                return false;
            }
        }

        return $this->_OxpsPaymorrowOxPaymentGateway_executePayment_parent($dAmount, $oOrder);
    }


    /**
     * Saves Paymorrow User Payment data to oxUserPayments table
     *
     * @param oxOrder                      $oOrder
     * @param OxpsPaymorrowResponseHandler $oPmResponseHandler
     *
     * @return bool|string
     */
    protected function _savePaymorrowUserPaymentData( oxOrder $oOrder, $oPmResponseHandler )
    {
        $aPmResponse = $oPmResponseHandler->getResponse();

        $oUserPaymentId = $oOrder->oxorder__oxpaymentid->value;

        /** @var OxpsPaymorrowOxUserPayment|oxUserPayment $oUserPayment */
        $oUserPayment = oxNew( 'oxuserpayment' );
        $oUserPayment->load( $oUserPaymentId );

        $sPmBankName = $aPmResponse[self::PAYMORROW_RESPONSE_BANK_NAME];
        $sPmIbanCode = $aPmResponse[self::PAYMORROW_RESPONSE_SDD_IBAN];
        $sPmBicCode  = $aPmResponse[self::PAYMORROW_RESPONSE_SDD_BIC];
        $sPmOrderId  = $aPmResponse[self::PAYMORROW_RESPONSE_ORDER_ID];

        $oUserPayment->setPaymorrowBankName( $sPmBankName );
        $oUserPayment->setPaymorrowIBAN( $sPmIbanCode );
        $oUserPayment->setPaymorrowBIC( $sPmBicCode );
        $oUserPayment->setPaymorrowOrderId( $sPmOrderId );

        return $oUserPayment->save();
    }

    /**
     * Check order response for declination status and fields and error fields.
     * Set all relevant fields to session.
     * The session data is used to go with redirection to payment step and used to initialize payment forms.
     *
     * @param OxpsPaymorrowResponseHandler $oPmResponseHandler
     */
    protected function _handleOrderResponseErrors( $oPmResponseHandler )
    {
        $aInitDataForPaymentStep = array();

        // If order was declined, collect declination fields and unset error code ("unexpected" error)
        if ( $oPmResponseHandler->wasDeclined() ) {
            $aInitDataForPaymentStep = (array) $oPmResponseHandler->getDeclinationDataFromResponse();
        }

        $aErrorsData = (array) $oPmResponseHandler->getErrorDataFromResponse();

        // If there are errors add those to the data array
        if ( !empty( $aErrorsData ) ) {
            $aInitDataForPaymentStep = array_merge( $aInitDataForPaymentStep, $aErrorsData );
        }

        // If order declination data or errors were present, save the data to session
        if ( !empty( $aInitDataForPaymentStep ) ) {
            $oPmResponseHandler->setErrorCode( null );
            $this->_setSessionInitData( $aInitDataForPaymentStep );
        }
    }

    /**
     * Set array to session for payment form initialization.
     * This data is used to check if redirection to payment step is needed and also used to initialize payment forms.
     *
     * @codeCoverageIgnore
     *
     * @param array $aData
     */
    protected function _setSessionInitData( $aData )
    {
        oxRegistry::getSession()->setVariable( 'pm_init_data', $aData );
    }


    /**
     * Call parent executePayment function
     *
     * @codeCoverageIgnore
     *
     * @param $dAmount
     * @param $oOrder
     *
     * @return mixed
     */
    protected function _OxpsPaymorrowOxPaymentGateway_executePayment_parent( $dAmount, & $oOrder )
    {
        return parent::executePayment( $dAmount, $oOrder );
    }
}
