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
 * Class OxpsPaymorrowPayment extends Payment
 *
 * @see Payment
 */
class OxpsPaymorrowPayment extends OxpsPaymorrowPayment_parent
{

    /**
     * Overridden parent method.
     * Additionally checks if Paymorrow request was successful.
     * It is needed for detecting cases, when JavaScript is disabled in user browser and no requests were sent.
     *
     * @return mixed
     */
    public function validatePayment()
    {
        // Remove payment methods initialization data from session of any is set
        $this->_unsetSessionInitData();

        $mReturn = $this->_OxpsPaymorrowPayment_validatePayment_parent();

        if ($this->_isPaymorrowPayment(oxRegistry::getConfig()->getRequestParameter('paymentid')) and
            $this->_isPaymentResponseSessionInvalid()
        ) {
            oxRegistry::getSession()->setVariable('payerror', 1);

            return null;
        }

        return $mReturn;
    }


    /**
     * Delete session key with Paymorrow init data.
     * It is set on order confirmation errors, used to redirect user to the payment spe and passed to forms init.
     *
     * @codeCoverageIgnore
     */
    protected function _unsetSessionInitData()
    {
        oxRegistry::getSession()->deleteVariable('pm_init_data');
    }

    /**
     * Load payment method by ID and check if it is mapped as active Paymorrow method.
     *
     * @param string $iId
     *
     * @return bool
     */
    protected function _isPaymorrowPayment($iId)
    {
        /** @var OxpsPaymorrowOxPayment|oxPayment $oPayment */
        $oPayment = oxNew('oxPayment');

        // Load selected payment method and check if it is Paymorrow
        return ($oPayment->load($iId) and $oPayment->isPaymorrowActiveAndMapped());
    }

    /**
     * Check payment response in session fot errors.
     *
     * @return bool True is response is invalid, false otherwise.
     */
    protected function _isPaymentResponseSessionInvalid()
    {
        /** @var OxpsPaymorrowModule $oModule */
        $oModule = oxRegistry::get('OxpsPaymorrowModule');

        // Get Paymorrow response from session
        $aPaymorrowResponse = $oModule->getPaymorrowResponseData();

        // The response must exist and be valid
        return (
            !isset($aPaymorrowResponse['order_status'], $aPaymorrowResponse['response_status']) or
            !in_array($aPaymorrowResponse['order_status'], array('VALIDATED', 'ACCEPTED')) or
            ($aPaymorrowResponse['response_status'] !== 'OK')
        );
    }


    /**
     * Parent `validatePayment` call.
     *
     * @codeCoverageIgnore
     *
     * @return mixed
     */
    protected function _OxpsPaymorrowPayment_validatePayment_parent()
    {
        return parent::validatePayment();
    }
}
