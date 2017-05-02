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
 * Class OxpsPaymorrowOxPayment extends oxPayment
 *
 * @see oxPayment
 */
class OxpsPaymorrowOxPayment extends OxpsPaymorrowOxPayment_parent
{

    /**
     * @var array Paymorrow payment method type options.
     */
    protected $_aPaymorrowValidPaymentTypes = array(
        0 => 'pm_off',
        1 => 'pm_invoice',
        2 => 'pm_sdd', // Direct Debit
    );

    /**
     * Mapping Paymorrow payment method name to OXID eShop identifier for it.
     *
     * @var array
     */
    protected $paymorrowPaymentNameMap = array(
        'INVOICE' => 1,
        'SDD'     => 2,
    );


    /**
     * Check if current payment method is mapped to Paymorrow payment method.
     *
     * @return bool
     */
    public function isPaymentMappedToPaymorrowMethod()
    {
        $iPaymorrowMap = $this->getPaymorrowPaymentMap();

        return (($iPaymorrowMap == 1) OR ($iPaymorrowMap == 2));
    }

    /**
     * Check if current payment method, is active and mapped as Paymorrow payment method.
     * Also checks if merchant ID is configured.
     *
     * @return bool
     */
    public function isPaymorrowActiveAndMapped()
    {
        return ($this->isPaymorrowActive() and
                $this->isPaymentMappedToPaymorrowMethod() and
                oxRegistry::get('OxpsPaymorrowSettings')->getMerchantId());
    }

    /**
     * Check if current payment method is active as Paymorrow payment method.
     *
     * @return bool
     */
    public function isPaymorrowActive()
    {
        return (bool) $this->getPaymorrowActive();
    }

    /**
     * Set current payment method as disabled or active as Paymorrow payment method.
     *
     * @param integer $iActive - 0/1 | Disabled/Active
     */
    public function setPaymorrowActive($iActive)
    {
        $this->oxpayments__oxpspaymorrowactive = new oxField($iActive);
    }

    /**
     * Get payment method disabled or active status for being set as Paymorrow payment method.
     *
     * @return integer
     */
    public function getPaymorrowActive()
    {
        return $this->oxpayments__oxpspaymorrowactive->value;
    }

    /**
     * Get payment method type code as string for template injection.
     *
     * @return string 'pm_invoice' - Invoice, 'pm_sdd' - Direct Debit
     */
    public function getPaymorrowPaymentType()
    {
        $iType = $this->getPaymorrowPaymentMap();

        return $this->_aPaymorrowValidPaymentTypes[$iType];
    }

    /**
     * Get Paymorrow payment method ID.
     *
     * @return integer 1 - Invoice, 2 - Direct Debit
     */
    public function getPaymorrowPaymentMap()
    {
        return $this->oxpayments__oxpspaymorrowmap->value;
    }

    /**
     * Set Paymorrow payment method ID.
     *
     * 0 - Off / none
     * 1 - Paymorrow Invoice
     * 2 - Paymorrow Direct Debit
     */
    public function setPaymorrowPaymentMap($iType)
    {
        $blValidMapping = array_key_exists($iType, $this->_aPaymorrowValidPaymentTypes);

        if ($blValidMapping) {
            $this->oxpayments__oxpspaymorrowmap = new oxField($iType);
        }

        return $blValidMapping;
    }

    /**
     * Get payment method name.
     *
     * @return string
     */
    public function getTitle()
    {
        return (string) $this->oxpayments__oxdesc->value;
    }

    /**
     * Overridden parent method.
     * Ignores default payment method form error in case method is converted to a Paymorrow payment method.
     *
     * @param $aDynValue
     * @param $sShopId
     * @param $oUser
     * @param $dBasketPrice
     * @param $sShipSetId
     *
     * @return bool
     */
    public function isValidPayment($aDynValue, $sShopId, $oUser, $dBasketPrice, $sShipSetId)
    {
        /** @var OxpsPaymorrowOxPayment|oxPayment $this */

        $blIsValid = (bool) $this->_OxpsPaymorrowOxPayment_isValidPayment_parent(
            $aDynValue, $sShopId, $oUser, $dBasketPrice, $sShipSetId
        );

        $iErrorCode = (int) $this->getPaymentErrorNumber();

        // In case it is Paymorrow payment method unset error code and tell than validation passed.
        if ($this->isPaymorrowActive() and !$blIsValid and ($iErrorCode === 1)) {
            $this->_iPaymentError = null;

            return true;
        }

        return $blIsValid;
    }

    /**
     * Load active, mapped and selected Paymorrow payment method.
     * If several are selected loads it by sorting and last updated.
     *
     * @codeCoverageIgnore
     *
     * @return mixed
     */
    public function loadPaymorrowDefault()
    {
        /** @var OxpsPaymorrowOxPayment|oxPayment $this */

        $query = sprintf(
            "SELECT * FROM `%s`WHERE %s AND `OXCHECKED` = 1 ORDER BY `OXSORT` ASC, `OXTIMESTAMP` DESC LIMIT 1",
            $this->getViewName(),
            $this->getPaymorrowMethodWhereClause()
        );

        return $this->assignRecord($query);
    }

    /**
     * Load active, mapped Paymorrow payment method by it's name.
     * E.g. by value "INVOICE" or "SDD".
     *
     * @param string $methodName
     *
     * @codeCoverageIgnore
     *
     * @return bool
     */
    public function loadByPaymorrowName($methodName)
    {
        /** @var OxpsPaymorrowOxPayment|oxPayment $this */

        if (!array_key_exists($methodName, $this->paymorrowPaymentNameMap)) {
            return false;
        }

        $query = sprintf(
            "SELECT * FROM `%s` WHERE %s AND `OXPSPAYMORROWMAP` = %d ORDER BY `OXSORT` ASC, `OXTIMESTAMP` DESC LIMIT 1",
            $this->getViewName(),
            $this->getPaymorrowMethodWhereClause(),
            (int) $this->paymorrowPaymentNameMap[$methodName]
        );

        return $this->assignRecord($query);
    }


    /**
     * Get SQL "WHERE" clause for loading Paymorrow payment method - active, and mapped correctly.
     *
     * @codeCoverageIgnore
     *
     * @return string
     */
    protected function getPaymorrowMethodWhereClause()
    {
        return " `OXACTIVE` = 1 AND `OXPSPAYMORROWACTIVE` = 1 AND `OXPSPAYMORROWMAP` > 0 ";
    }


    /**
     * Parent `isValidPayment` call.
     *
     * @codeCoverageIgnore
     *
     * @param $aDynValue
     * @param $sShopId
     * @param $oUser
     * @param $dBasketPrice
     * @param $sShipSetId
     *
     * @return mixed
     */
    protected function _OxpsPaymorrowOxPayment_isValidPayment_parent($aDynValue, $sShopId, $oUser, $dBasketPrice,
                                                                     $sShipSetId)
    {
        return parent::isValidPayment($aDynValue, $sShopId, $oUser, $dBasketPrice, $sShipSetId);
    }
}
