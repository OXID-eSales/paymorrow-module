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
 * Class OxpsPaymorrowOxBasket extends oxBasket.
 *
 * @see oxBasket
 */
class OxpsPaymorrowOxBasket extends OxpsPaymorrowOxBasket_parent
{

    /**
     * Get line items array - summary of bought products, costs and vouchers.
     *
     * @for: OxpsPaymorrowEshopDataProvider
     *
     * @return array
     */
    public function getPaymorrowBasketLineItems()
    {
        /** @var OxpsPaymorrowOxBasket|oxBasket $this */

        $aBasketContents = $this->getContents();

        if ( empty( $aBasketContents ) ) {
            return array();
        }

        $iLineItemCount = 1; // must start from 1
        $aLineItems     = array();

        /** @var oxBasketItem|OxpsPaymorrowOxBasketItem $oBasketItem */
        foreach ( $aBasketContents as $oBasketItem ) {
            $aLineItems = array_merge( $aLineItems, $oBasketItem->getPaymorrowBasketItemSummary( $iLineItemCount ) );

            $iLineItemCount++;
        }

        /**
         * Checks for additional Payment, Wrapping, Gift Card, Delivery/Shipping, Vouchers,
         * TS Protection - costs
         */
        $aLineItems = array_merge( $aLineItems, $this->_getPaymorrowAdditionalCostsSummary( $iLineItemCount ) );

        return $aLineItems;
    }

    /**
     * Get basket currency name.
     *
     * @return string
     */
    public function getPaymorrowBasketCurrency()
    {
        return $this->getBasketCurrency()->name;
    }

    /**
     * Get basket total amount.
     * Also adds surcharge amount if it is needed.
     *
     * @return float
     */
    public function getPaymorrowTotalAmount()
    {
        /** @var OxpsPaymorrowOxBasket|oxBasket $this */

        $dTotalPrice = (double) $this->getPrice()->getPrice();

        // Add surcharge price in case it is not yet included in basket
        $oSurchargePrice = $this->_getPaymentMethodSurcharge();

        if ( !empty( $oSurchargePrice ) and is_object( $oSurchargePrice ) ) {
            $dTotalPrice += (double) $oSurchargePrice->getPrice();
        }

        return $dTotalPrice;
    }


    /**
     * Get sum of all VATs: all products and all costs.
     *
     * @return float
     */
    public function getPaymorrowTotalVatAmount()
    {
        /** @var OxpsPaymorrowOxBasket|oxBasket $this */

        // Products VAT
        $dTotalVat = (double) array_sum( (array) $this->getProductVats( false ) );

        // Get cost and add costs VATs
        $aCosts = (array) $this->getCosts();

        foreach ( $aCosts as $oPrice ) {
            $dTotalVat += (double) $this->_getVatForValidPrice( $oPrice );
        }

        // Add surcharge VAT amount in case it is not yet included in basket
        $dTotalVat += (double) $this->_getVatForValidPrice( $this->_getPaymentMethodSurcharge() );

        return $dTotalVat;
    }


    /**
     * Get VAT value for valid price object.
     * IF price is invalid returns zero.
     *
     * @param mixed|oxPrice $mPrice
     *
     * @return float
     */
    protected function _getVatForValidPrice( $mPrice )
    {
        if ( empty( $mPrice ) or !is_object( $mPrice ) ) {
            return 0.0;
        }

        return (double) $mPrice->getVatValue();
    }

    /**
     * Checks if there are any additional costs and returns Line Items array as summary.
     *
     * @param int $iLineItemCount
     *
     * @return array
     */
    protected function _getPaymorrowAdditionalCostsSummary( $iLineItemCount )
    {
        /** @var OxpsPaymorrowOxBasket|oxBasket $this */

        // Line Item numbering after all basket items were summarized
        $iLineItem = (int) $iLineItemCount;

        // Get cost and add costs VATs
        list( $aCostItems, $iLineItem ) = $this->_getDefaultCostsSummary( $iLineItem );

        // Get payment method surcharge item data in case payment form have just been posted
        $aItemData = (array) $this->_getPaymentMethodSurcharge( $iLineItem );

        if ( !empty( $aItemData ) ) {
            $aCostItems = array_merge( $aCostItems, $aItemData );
            $iLineItem++;
        }

        // Add voucher discount line item
        if ( $this->getVoucherDiscount() and $this->getVoucherDiscount()->getBruttoPrice() != 0 ) {
            $aCostItems = array_merge( $aCostItems, $this->_getPaymorrowVouchersSummary( $iLineItem ) );
        }

        return $aCostItems;
    }

    /**
     * Load default shop costs and extract lite items summary.
     *
     * @param int $iLineItem
     *
     * @return array Mixed array with line items array as first element and new line count as second element.
     */
    protected function _getDefaultCostsSummary( $iLineItem )
    {
        /** @var OxpsPaymorrowOxBasket|oxBasket $this */

        $aCostItems = array();

        $aCosts = (array) $this->getCosts();

        foreach ( $aCosts as $sCost => $oPrice ) {
            $aItemData = (array) $this->_validateCostPriceAndGetItsData( $oPrice, $iLineItem, $sCost );

            if ( !empty( $aItemData ) ) {
                $aCostItems = array_merge( $aCostItems, $aItemData );
                $iLineItem++;
            }
        }

        return array($aCostItems, $iLineItem);
    }

    /**
     * Validate cost price and get cost line item data array for a valid cost.
     *
     * @param null|oxPrice $mPrice
     * @param int          $iLineItem
     * @param string       $sCost
     *
     * @return array
     */
    protected function _validateCostPriceAndGetItsData( $mPrice, $iLineItem, $sCost )
    {
        if ( empty( $mPrice ) or !is_object( $mPrice ) or !$mPrice->getPrice() ) {
            return array();
        }

        return $aItemData = (array) $this->_getCostSummary( $iLineItem, $sCost, $mPrice );
    }

    /**
     * Get cost line item summary data as array.
     *
     * @param integer                              $iLineItem
     * @param string                               $sCostName
     * @param oxPrice $oPrice
     *
     * @return array
     */
    protected function _getCostSummary( $iLineItem, $sCostName, $oPrice )
    {
        $sPmPrefix = OxpsPaymorrowOxBasketItem::getPaymorrowBasketSummaryLineItemPrefix( $iLineItem );

        list( $sId, $sName, $sType ) = $this->_getCostMetadata( $sCostName );

        if ( empty( $sId ) ) {
            return array();
        }

        $this->filterCostPrice($oPrice, $sCostName);

        return array(
            $sPmPrefix . 'quantity'       => 1,
            $sPmPrefix . 'articleId'      => $sId,
            $sPmPrefix . 'name'           => $sName,
            $sPmPrefix . 'type'           => $sType,
            $sPmPrefix . 'unitPriceGross' => (double) $oPrice->getBruttoPrice(),
            $sPmPrefix . 'grossAmount'    => (double) $oPrice->getBruttoPrice(),
            $sPmPrefix . 'vatAmount'      => (double) $oPrice->getVatValue(),
            $sPmPrefix . 'vatRate'        => (double) $oPrice->getVat(),
        );
    }

    /**
     * A fix to cover eShop core bug #0006571
     * Perform VAT percent rounding for Wrapping Cost.
     *
     * @todo: Then the eShop core bug is fixed, remove this method and it's usage.
     *
     * @param oxPrice|\OxidEsales\EshopCommunity\Core\Price $price
     * @param string                                        $costName
     *
     * @codeCoverageIgnore
     */
    protected function filterCostPrice($price, $costName)
    {
        if (('oxwrapping' === $costName) and is_object($price) and method_exists($price, 'getVat')) {
            $vatPercent = (double) $price->getVat();
            $vatPercent = round($vatPercent * 10) / 10;
            $price->setVat($vatPercent);
        }
    }

    /**
     * Get costs metadata values.
     *
     * @param string $sCostName
     *
     * @return array Cost metadata array: ID, Name, Type
     */
    protected function _getCostMetadata( $sCostName )
    {
        $aCostsMap = array(
            'oxdelivery'     => array(
                $this->_getShippingId(),
                'DeliveryCosts',
                'SHIPPING'
            ),
            'oxwrapping'     => array(
                'Wrapping',
                'Wrapping Costs',
                'GOODS'
            ),
            'oxgiftcard'     => array(
                'GiftCard',
                'Gift Card Costs',
                'GOODS'
            ),
            'oxpayment'      => array(
                $this->_getPaymentId(),
                'PaymentCosts',
                'PAYMENT_FEE'
            ),
            'oxtsprotection' => array(
                $this->_getTsProductId(),
                'TS Protection Costs',
                'GOODS'
            ),
        );

        return array_key_exists( $sCostName, $aCostsMap ) ? $aCostsMap[$sCostName] : array();
    }

    /**
     * Get shipping method ID.
     * Default value is "shippingCosts".
     *
     * @return string
     */
    protected function _getShippingId()
    {
        /** @var OxpsPaymorrowOxBasket|oxBasket $this */

        $sId = (string) $this->getShippingId();

        if ( !empty( $sId ) ) {
            $sId = $this->_toUtf( $sId );
        } else {
            $sId = 'shippingCosts';
        }

        return $sId;
    }

    /**
     * Get payment method ID.
     * Default value is "PaymentId".
     *
     * @return string
     */
    protected function _getPaymentId()
    {
        /** @var OxpsPaymorrowOxBasket|oxBasket $this */

        $sId = (string) $this->getPaymentId();

        if ( !empty( $sId ) ) {
            $sId = $this->_toUtf( $sId );
        } else {
            $sId = 'PaymentId';
        }

        return $sId;
    }

    /**
     * Get TS protection ID.
     * Default value is "TsProtection".
     *
     * @return string
     */
    protected function _getTsProductId()
    {
        /** @var OxpsPaymorrowOxBasket|oxBasket $this */

        $sId = method_exists($this, 'getTsProductId')
            ? (string) $this->getTsProductId()
            : '';

        if ( !empty( $sId ) ) {
            $sId = $this->_toUtf( $sId );
        } else {
            $sId = 'TsProtection';
        }

        return $sId;
    }

    /**
     * Get payment method surcharge line item by loading it from posted Paymorrow payment form data.
     *
     * @param null|int $mLineItem If null, then surcharge price object is returned, else surcharge line item data array
     *
     * @return oxPrice|array
     */
    protected function _getPaymentMethodSurcharge( $mLineItem = null )
    {
        $oSession = oxRegistry::getSession();

        if ( $oSession->getVariable( 'paymentid' ) or
             ( !$aPaymentPostData = (array) $oSession->getVariable( 'pm_verify' ) ) or
             !array_key_exists( 'paymentid', $aPaymentPostData )
        ) {
            return array();
        }

        return $this->_formatPriceItemResponse(
            $this->_loadPaymentMethodSurcharge( (string) $aPaymentPostData['paymentid'] ),
            $mLineItem
        );
    }

    /**
     * If line item requested, then return line item data array, otherwise price object fdirectly.
     *
     * @param array|oxPrice $mPrice
     * @param null|int      $mLineItem
     *
     * @return array|oxPrice
     */
    protected function _formatPriceItemResponse( $mPrice, $mLineItem )
    {
        if ( !is_null( $mLineItem ) and is_object( $mPrice ) ) {
            return $this->_getCostSummary( (int) $mLineItem, 'oxpayment', $mPrice );
        }

        return $mPrice;
    }

    /**
     * Load payment method by ID and calculate its surcharge for the basket.
     *
     * @param string $sPaymentId
     *
     * @return array|oxPrice Empty array on failure, price object on success.
     */
    protected function _loadPaymentMethodSurcharge( $sPaymentId )
    {
        /** @var oxPayment $oPaymentMethod */
        $oPaymentMethod = oxNew( 'oxPayment' );

        if ( !$oPaymentMethod->load( $sPaymentId ) or !$oPaymentMethod->getId() ) {
            return array();
        }

        $oPaymentMethod->calculate( $this );
        $oSurchargePrice = $oPaymentMethod->getPrice();

        if ( empty( $oSurchargePrice ) ) {
            return array();
        }

        return $oSurchargePrice;
    }

    /**
     * Get voucher discount summary.
     *
     * @param $iLineItem - Paymorrow line item must be passed in order to properly merge line items.
     *
     * @return array
     */
    protected function _getPaymorrowVouchersSummary( $iLineItem )
    {
        $sPmPrefix = OxpsPaymorrowOxBasketItem::getPaymorrowBasketSummaryLineItemPrefix( $iLineItem );

        /** @var oxPrice $oVoucher */
        $oVoucher = $this->getVoucherDiscount();

        return array(
            $sPmPrefix . 'quantity'       => 1,
            $sPmPrefix . 'articleId'      => 'Voucher',
            $sPmPrefix . 'name'           => 'VoucherCosts',
            $sPmPrefix . 'type'           => 'VOUCHER',
            $sPmPrefix . 'unitPriceGross' => (double) $oVoucher->getBruttoPrice(),
            $sPmPrefix . 'grossAmount'    => (double) $oVoucher->getBruttoPrice(),
            $sPmPrefix . 'vatAmount'      => 0.0,
            $sPmPrefix . 'vatRate'        => 0.0,
        );
    }

    /**
     * Alias for encoding casting method.
     *
     * @codeCoverageIgnore
     * @see OxpsPaymorrowEshopDataProvider::toUtf
     *
     * @param string $sString
     *
     * @return string
     */
    protected function _toUtf( $sString )
    {
        return OxpsPaymorrowEshopDataProvider::toUtf( $sString );
    }
}
