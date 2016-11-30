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
 * Class OxpsPaymorrowOxBasketItem extends oxBasketItem
 *
 * @see oxBasketItem
 */
class OxpsPaymorrowOxBasketItem extends OxpsPaymorrowOxBasketItem_parent
{

    const PAYMORROW_LINE_ITEM_PREFIX = "lineItem_%d_";


    /**
     * Paymorrow Line Item prefix builder
     *
     * @param $iLineItemCount
     *
     * @return string
     */
    public static function getPaymorrowBasketSummaryLineItemPrefix( $iLineItemCount )
    {
        return sprintf( self::PAYMORROW_LINE_ITEM_PREFIX, $iLineItemCount );
    }

    /**
     * Get related article number.
     *
     * @return string
     */
    public function getProductNumber()
    {
        /** @var $this OxpsPaymorrowOxBasketItem|oxBasketItem */

        /** @var oxArticle $oArticle */
        $oArticle = $this->getArticle();

        return isset( $oArticle->oxarticles__oxartnum->value ) ? (string) $oArticle->oxarticles__oxartnum->value : '';
    }

    /**
     * Compiles summary data array of basket item for Paymorrow.
     *
     * @param int $iLineItemCount
     *
     * @return array
     */
    public function getPaymorrowBasketItemSummary( $iLineItemCount )
    {
        /** @var OxpsPaymorrowOxBasketItem|oxBasketItem $this */

        $sPaymorrowLineItemPrefix = self::getPaymorrowBasketSummaryLineItemPrefix( $iLineItemCount );

        return array(
            $sPaymorrowLineItemPrefix . 'quantity'       => (double) $this->getAmount(),
            $sPaymorrowLineItemPrefix . 'articleId'      => $this->_toUtf( $this->getProductNumber() ),
            $sPaymorrowLineItemPrefix . 'name'           => $this->_toUtf( $this->getTitle(), 50 ),
            $sPaymorrowLineItemPrefix . 'type'           => 'GOODS',
            $sPaymorrowLineItemPrefix . 'unitPriceGross' => (double) $this->getUnitPrice()->getBruttoPrice(),
            $sPaymorrowLineItemPrefix . 'grossAmount'    => (double) $this->getPrice()->getBruttoPrice(),
            $sPaymorrowLineItemPrefix . 'vatAmount'      => (double) $this->getPrice()->getVatValue(),
            $sPaymorrowLineItemPrefix . 'vatRate'        => (double) $this->getVatPercent(),
        );
    }

    /**
     * Alias for encoding casting method.
     *
     * @codeCoverageIgnore
     * @see OxpsPaymorrowEshopDataProvider::toUtf
     *
     * @param string   $sString
     * @param null|int $mLimitLength
     *
     * @return string
     */
    protected function _toUtf( $sString, $mLimitLength = null )
    {
        return OxpsPaymorrowEshopDataProvider::toUtf( $sString, $mLimitLength );
    }
}
