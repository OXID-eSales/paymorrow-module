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
 * Class OxpsPaymorrowPrepareOrder
 */
class OxpsPaymorrowPrepareOrder extends oxUBase
{

    /**
     * A name for request/session fields to store selected payment method ID in.
     */
    const OXPS_PAYMENT_ID_FIELD = 'paymentid';

    /**
     * A name of POST field to store Paymorrow payment name.
     */
    const OXPS_PM_PAYMENT_NAME_FIELD = 'pm_paymentMethod_name';


    /**
     * Paymorrow function for Verifying Form Data against Paymorrow Services.
     * Also set selected payment method ID revealed by Paymorrow payment method name.
     */
    public function prepareOrder()
    {
        $this->setPaymorrowPaymentMethodId();

        /** @var OxpsPaymorrowRequestControllerProxy $pmGateWay */
        $pmGateWay = oxNew('OxpsPaymorrowRequestControllerProxy');

        $oUtils = oxRegistry::getUtils();
        $oUtils->setHeader("Content-Type: application/json");
        $oUtils->showMessageAndExit($pmGateWay->prepareOrder($_POST));
    }


    /**
     * Load payment instance by Paymorrow payment method name
     * and set it to both, request and session if not yet set.
     */
    protected function setPaymorrowPaymentMethodId()
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();

        /** @var oxPayment|OxpsPaymorrowOxPayment $payment */
        $payment = oxNew('oxPayment');
        $paymentIdField = self::OXPS_PAYMENT_ID_FIELD;
        $paymentName = (string) $config->getRequestParameter(self::OXPS_PM_PAYMENT_NAME_FIELD);

        if ($payment->loadByPaymorrowName($paymentName) and
            !$config->getRequestParameter($paymentIdField)
        ) {
            $this->appendPostData(array($paymentIdField => (string) $payment->getId()));
        }
    }

    /**
     * Add assoc array to POST request.
     *
     * @param array $data
     */
    protected function appendPostData(array $data)
    {
        $_POST = array_merge($_POST, $data);
    }
}
