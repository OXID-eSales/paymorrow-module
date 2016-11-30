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
 * Class OxpsPaymorrowOxUserPayment extend oxUserPayment
 *
 * @see oxUserPayment
 */
class OxpsPaymorrowOxUserPayment extends OxpsPaymorrowOxUserPayment_parent
{

    /**
     * Set Paymorrow bank name.
     *
     * @param $sBankName
     */
    public function setPaymorrowBankName( $sBankName )
    {
        $this->oxuserpayments__oxpspaymorrowbankname = new oxField( $sBankName );
    }

    /**
     * Get Paymorrow bank name.
     *
     * @return string
     */
    public function getPaymorrowBankName()
    {
        return $this->oxuserpayments__oxpspaymorrowbankname->value;
    }

    /**
     * Set Paymorrow transaction IBAN code.
     *
     * @param string $sIBAN
     */
    public function setPaymorrowIBAN( $sIBAN )
    {
        $this->oxuserpayments__oxpspaymorrowiban = new oxField( $sIBAN );
    }

    /**
     * Get Paymorrow transaction IBAN code.
     *
     * @return string
     */
    public function getPaymorrowIBAN()
    {
        return $this->oxuserpayments__oxpspaymorrowiban->value;
    }

    /**
     * Set Paymorrow transaction BIC code.
     *
     * @param string $sBIC
     */
    public function setPaymorrowBIC( $sBIC )
    {
        $this->oxuserpayments__oxpspaymorrowbic = new oxField( $sBIC );
    }

    /**
     * Get Paymorrow transaction BIC code.
     *
     * @return string
     */
    public function getPaymorrowBIC()
    {
        return $this->oxuserpayments__oxpspaymorrowbic->value;
    }

    /**
     * Set Paymorrow order ID received after `confirmOrder`.
     *
     * @param string $sId
     */
    public function setPaymorrowOrderId( $sId )
    {
        $this->oxuserpayments__oxpspaymorroworderid = new oxField( $sId );
    }

    /**
     * Get Paymorrow order ID received after `confirmOrder`.
     *
     * @return string
     */
    public function getPaymorrowOrderId()
    {
        return $this->oxuserpayments__oxpspaymorroworderid->value;
    }


    /**
     * Overridden `load` function - loads additional Paymorrow fields in oxUserPayments table.
     * Loads user payment object.
     *
     * @param string $sOxId
     *
     * @return mixed
     */
    public function load( $sOxId )
    {
        $sSQL = sprintf(
            'SELECT `OXID`, `OXUSERID`, `OXPAYMENTSID`, DECODE( `OXVALUE`, "%s" ) AS `OXVALUE`,
              `OXPSPAYMORROWBANKNAME`, `OXPSPAYMORROWIBAN`, `OXPSPAYMORROWBIC`, `OXPSPAYMORROWORDERID`
              FROM `oxuserpayments` WHERE `OXID` = %s',
            $this->getPaymentKey(),
            oxDb::getDb()->quote( $sOxId )
        );

        return $this->assignRecord( $sSQL );
    }

    /**
     * Check if user payment method is mapped to a Paymorrow payment method.
     *
     * @return bool
     */
    public function isUserPaymentPaymorrowMethod()
    {
        $sPaymentId = $this->oxuserpayments__oxpaymentsid->value;

        /** @var OxpsPaymorrowOxPayment|oxPayment $oxPayment */
        $oxPayment = oxNew( 'oxPayment' );
        $oxPayment->load( $sPaymentId );

        return $oxPayment->isPaymorrowActiveAndMapped();
    }

    /**
     * Get related payment method.
     * Used in custom paymorrow email templates.
     *
     * @return oxPayment
     */
    public function getPaymorrowOxPayment()
    {
        $sPaymentId = $this->oxuserpayments__oxpaymentsid->value;

        $oPayment = oxNew( 'oxPayment' );
        $oPayment->load( $sPaymentId );

        return $oPayment;
    }
}
