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
 * Class OxpsPaymorrowInit.
 */
class OxpsPaymorrowInit extends oxWidget
{

    /**
     * Widget template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'oxpspaymorrowinit.tpl';


    /**
     * Get Paymorrow controller prepareOrder dispatcher.
     *
     * @return string
     */
    public function getPaymorrowControllerPrepareOrderProcessPaymentURL()
    {
        return $this->_getShopBaseLink() . 'cl=oxpspaymorrowprepareorder&fnc=prepareOrder';
    }

    /**
     * Get URL for Paymorrow dynamic JavaScript inclusion.
     *
     * @return string
     */
    public function getPaymorrowResourceControllerJavaScript()
    {
        return $this->_getShopBaseLink() . 'cl=oxpspaymorrowresource&fnc=getPaymorrowJavaScript';
    }

    /**
     * Get URL for Paymorrow dynamic CSS inclusion.
     *
     * @return string
     */
    public function getPaymorrowResourceControllerCSS()
    {
        return $this->_getShopBaseLink() . 'cl=oxpspaymorrowresource&fnc=getPaymorrowCSS';
    }

    /**
     * Returns a JavaScript with Paymorrow payment methods initialization functions wrapped in document.ready.
     *
     * @nice-to-have Use a template for JS generation rather than doing in within PHP.
     *
     * @return string
     */
    public function getPaymorrowJavaScriptPmInitFull()
    {
        $sPmPrintData = $this->_getPaymorrowPrintData();
        $sPmControllerPrepareOrder = $this->getPaymorrowControllerPrepareOrderProcessPaymentURL();
        $sSelectedMethod = $this->getSelectedPaymorrowMethod();
        $sNextButtonId = 'paymentNextStepBottom';

        $sSDD = sprintf(
            'pmInitFull("SDD", "pmsdd", "rb_payment_sdd", "dl_payment_sdd", "payment", %s, "%s", %s, "%s");',
            $sPmPrintData,
            $sPmControllerPrepareOrder,
            ($sSelectedMethod == 'pm_sdd') ? 'true' : 'false',
            $sNextButtonId
        );

        $sInvoice = sprintf(
            'pmInitFull("INVOICE", "pminvoice", "rb_payment_invoice", "dl_payment_invoice", "payment", %s, "%s", %s, "%s");',
            $sPmPrintData,
            $sPmControllerPrepareOrder,
            ($sSelectedMethod == 'pm_invoice') ? 'true' : 'false',
            $sNextButtonId
        );

        return sprintf('$(document).ready(function(){%s%s});', $sSDD, $sInvoice);
    }

    /**
     * Get a code of selected Paymorrow payment method.
     * First session is checked for what user have selected, then payment methods configuration is checked.
     * It applies only on payment methods linked to Paymorrow.
     *
     * @return string Paymorrow payment method code or empty string.
     */
    public function getSelectedPaymorrowMethod()
    {
        $sSessionPaymentId = (string) oxRegistry::getSession()->getVariable( 'paymentid' );

        /** @var OxpsPaymorrowOxPayment|oxPayment $oPayment */
        $oPayment = oxNew( 'OxpsPaymorrowOxPayment' );

        if ( empty( $sSessionPaymentId ) or !$oPayment->load( $sSessionPaymentId ) or
             !$oPayment->isPaymorrowActiveAndMapped()
        ) {
            $oPayment->loadPaymorrowDefault();
        }

        return (string) $oPayment->getPaymorrowPaymentType();
    }


    /**
     * Get a clean base URL of an active (sub-)shop suitable to pass to Paymorrow.
     *
     * @return string
     */
    protected function _getShopBaseLink()
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        return (string) str_replace('&amp;', '&', $config->getShopSecureHomeURL());
    }

    /**
     * Get Paymorrow payment forms initialization data in JSON format.
     * It calls OxpsPaymorrowEshopDataProvider -> printPmData.
     *
     * @return string
     */
    protected function _getPaymorrowPrintData()
    {
        /** @var OxpsOxid2Paymorrow $oOxidToPm */
        $oOxidToPm = oxNew( 'OxpsOxid2Paymorrow' );

        return $oOxidToPm->getPaymorrowPrintData();
    }
}
