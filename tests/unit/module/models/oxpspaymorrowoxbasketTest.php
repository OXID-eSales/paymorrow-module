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
 * Class Unit_Module_Models_OxpsPaymorrowOxBasketTest
 *
 * @see OxpsPaymorrowOxBasket
 */
class Unit_Module_Models_OxpsPaymorrowOxBasketTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowOxBasket|oxBasket
     */
    protected $SUT;


    /**
     * Set initial objects state.
     *
     * @return null|void
     */
    public function setUp()
    {
        parent::setUp();

        // SUT mock
        $this->SUT = $this->getMock(
            'OxpsPaymorrowOxBasket',
            array(
                '__construct', 'load', 'init', 'getProductVats', 'getContents',
                'getCosts', 'getPrice', '_getPaymentMethodSurcharge'
            )
        );

    }


    /**
     * Mocking Proxy Class to test Protected methods
     *
     * IDE - might underline methods with RED color, PLEASE IGNORE
     *
     * @param array $aParams
     *
     * @return OxpsPaymorrowOxBasket|oxBasket
     */
    protected function _getProxySUT( array $aParams = array() )
    {
        return $this->getProxyClass( 'OxpsPaymorrowOxBasket', $aParams );
    }


    public function testGetPaymorrowTotalAmount_noAdditionalPaymentSurcharge_returnCalculatedGrandTotal()
    {
        $this->SUT->expects( $this->once() )->method( '_getPaymentMethodSurcharge' )->with( $this->isNull() )->will(
            $this->returnValue( array() )
        );
        $this->SUT->expects( $this->once() )->method( 'getPrice' )->will(
            $this->returnValue( $this->_getPriceMock( 100.10, 19.0 ) )
        );

        $this->assertSame( 100.10, $this->SUT->getPaymorrowTotalAmount() );
    }

    public function testGetPaymorrowTotalAmount_additionalPaymentSurchargeAvailable_returnGrandTotalPlusSurchargeAmount()
    {
        $this->SUT->expects( $this->once() )->method( '_getPaymentMethodSurcharge' )->with( $this->isNull() )->will(
            $this->returnValue( $this->_getPriceMock( 5.0, 19.0 ) )
        );
        $this->SUT->expects( $this->once() )->method( 'getPrice' )->will(
            $this->returnValue( $this->_getPriceMock( 100.10, 19.0 ) )
        );

        $this->assertSame( 105.10, $this->SUT->getPaymorrowTotalAmount() );
    }


    public function testGetPaymorrowTotalVatAmount_noProductsVatsAndNoCostsSet_returnZero()
    {
        $this->assertSame( 0.0, $this->SUT->getPaymorrowTotalVatAmount() );
    }

    public function testGetPaymorrowTotalVatAmount_productsVatsSetButNoCostsSet_returnSumOfAllProductsVats()
    {
        $this->SUT->expects( $this->once() )->method( 'getProductVats' )->with( $this->equalTo( false ) )->will(
            $this->returnValue( array(322.0, 44.0, 3.21) )
        );

        $this->assertSame( 369.21, $this->SUT->getPaymorrowTotalVatAmount() );
    }

    public function testGetPaymorrowTotalVatAmount_productsAndCostsVatsSet_returnSumOfAllVats()
    {
        $this->SUT->expects( $this->once() )->method( 'getProductVats' )->with( $this->equalTo( false ) )->will(
            $this->returnValue( array(19.00, 124.79) )
        );

        $this->SUT->expects( $this->once() )->method( 'getCosts' )->with( $this->equalTo( null ) )->will(
            $this->returnValue(
                array(
                    'oxdelivery' => $this->_getPriceMock( 15.55, 19.0 ),
                    'oxpayment'  => $this->_getPriceMock( 5.0, 2.5 )
                )
            )
        );

        $this->assertSame( 146.39, $this->SUT->getPaymorrowTotalVatAmount() );
    }

    public function testGetPaymorrowTotalVatAmount_alsoPaymentSurchargeSet_returnSumOfAllVats()
    {
        $this->SUT->expects( $this->once() )->method( 'getProductVats' )->with( $this->equalTo( false ) )->will(
            $this->returnValue( array(19.00, 124.79) )
        );

        $this->SUT->expects( $this->once() )->method( 'getCosts' )->with( $this->equalTo( null ) )->will(
            $this->returnValue(
                array(
                    'oxdelivery' => $this->_getPriceMock( 15.55, 19.0 ),
                    'oxpayment'  => $this->_getPriceMock( 5.0, 2.5 )
                )
            )
        );

        $this->SUT->expects( $this->once() )->method( '_getPaymentMethodSurcharge' )->with( $this->isNull() )->will(
            $this->returnValue( $this->_getPriceMock( 5.0, 19.0 ) )
        );

        $this->assertSame( 147.19, $this->SUT->getPaymorrowTotalVatAmount() );
    }


    public function test_getPaymorrowBasketCurrency_shouldReturnSetCurrencyCHF()
    {
        $this->assertEquals( $this->SUT->getBasketCurrency()->name, $this->SUT->getPaymorrowBasketCurrency() );
    }


    /**
     * @param $aReturnValue array
     *
     * @return oxbasketitem
     */
    protected function _getBasketItemMock( $aReturnValue )
    {
        $oBasketMock = $this->getMock( 'oxbasketitem', array('load', '__construct', 'getPaymorrowBasketItemSummary') );

        $oBasketMock->expects( $this->once() )
            ->method( 'getPaymorrowBasketItemSummary' )
            ->will( $this->returnValue( $aReturnValue ) );

        return $oBasketMock;
    }


    public function test_getPaymorrowBasketLineItems_shouldReturnEmptryArrayWhenBasketContentsEmpty()
    {
        $this->SUT->expects( $this->once() )
            ->method( 'getContents' )
            ->will( $this->returnValue( array() ) );


        $this->assertEmpty( $this->SUT->getPaymorrowBasketLineItems() );
    }


    public function test_getPaymorrowBasketLineItems_shoudlReturnBasketLineItems()
    {
        $aResult = array(
            'rand1' => 'lineItem1',
            'rand2' => 'lineItem2',
            'rand3' => 'lineItem3',
        );

        $aBasketItems   = array();
        $aBasketItems[] = $this->_getBasketItemMock( array('rand1' => 'lineItem1') );
        $aBasketItems[] = $this->_getBasketItemMock( array('rand2' => 'lineItem2') );
        $aBasketItems[] = $this->_getBasketItemMock( array('rand3' => 'lineItem3') );

        $this->SUT->expects( $this->once() )
            ->method( 'getContents' )
            ->will( $this->returnValue( $aBasketItems ) );

        $this->assertEquals( $aResult, $this->SUT->getPaymorrowBasketLineItems() );
    }


    /**
     * @param     $dPrice
     * @param int $dVat
     *
     * @return oxPrice
     */
    protected function _getPriceMock( $dPrice, $dVat = 19 )
    {
        $oPrice = new oxPrice();
        $oPrice->setPrice( $dPrice, $dVat );

        return $oPrice;
    }


    public function test_getPaymorrowVouchersSummary_shouldReturnVoucherCostsSummary()
    {
        $oPrice = $this->_getPriceMock( 1021848, 0 );

        $iLineItem = 10;

        $SUT = $this->_getProxySUT();

        $SUT->setVoucherDiscount( 1021848 );

        $sPmPrefix = OxpsPaymorrowOxBasketItem::getPaymorrowBasketSummaryLineItemPrefix( $iLineItem );

        $aExpectedResult = array(
            $sPmPrefix . 'quantity'       => 1,
            $sPmPrefix . 'articleId'      => 'Voucher', // Used for gift card
            $sPmPrefix . 'name'           => 'VoucherCosts',
            $sPmPrefix . 'type'           => 'VOUCHER',
            $sPmPrefix . 'unitPriceGross' => $oPrice->getBruttoPrice(),
            $sPmPrefix . 'grossAmount'    => $oPrice->getBruttoPrice(),
            $sPmPrefix . 'vatAmount'      => 0.0,
            $sPmPrefix . 'vatRate'        => 0.0,
        );

        $this->assertEquals( $aExpectedResult, $SUT->_getPaymorrowVouchersSummary( $iLineItem ) );
    }


    public function test_getPaymorrowAdditionalCostsSummary_shouldReturnEmptyArrayWhenThereAreNoAdditionalCosts()
    {
        $SUT = $this->_getProxySUT();

        $this->assertEmpty( $SUT->_getPaymorrowAdditionalCostsSummary( 3 ) );
    }


    public function test_getPaymorrowAdditionalCostsSummary_shouldReturnDeliveryAndWrappingLineItemsCostsSummary()
    {
        $SUT = $this->_getProxySUT();

        $iLineItemInitial = 33;
        $iLineItem        = $iLineItemInitial;

        $oDeliveryPrice = $this->_getPriceMock( 234.12, 53 );
        $SUT->setCost( 'oxdelivery', $oDeliveryPrice );

        $oWrappingCosts = $this->_getPriceMock( 32, 1 );
        $SUT->setCost( 'oxwrapping', $oWrappingCosts );

        $sPmDeliveryPrefix = OxpsPaymorrowOxBasketItem::getPaymorrowBasketSummaryLineItemPrefix( $iLineItem );

        $SUT->setShipping( 'UNIT_TEST_SHIPING_ID' );

        $aDeliverySummary = array(
            $sPmDeliveryPrefix . 'quantity'       => 1,
            $sPmDeliveryPrefix . 'articleId'      => 'UNIT_TEST_SHIPING_ID', // Used for gift card
            $sPmDeliveryPrefix . 'name'           => 'DeliveryCosts',
            $sPmDeliveryPrefix . 'type'           => 'SHIPPING',
            $sPmDeliveryPrefix . 'unitPriceGross' => $oDeliveryPrice->getBruttoPrice(),
            $sPmDeliveryPrefix . 'grossAmount'    => $oDeliveryPrice->getBruttoPrice(),
            $sPmDeliveryPrefix . 'vatAmount'      => $oDeliveryPrice->getVatValue(),
            $sPmDeliveryPrefix . 'vatRate'        => $oDeliveryPrice->getVat(),
        );
        $iLineItem++; // Incrementing to avoid Line Items array keys clash

        $sPmWrappingPrefix = OxpsPaymorrowOxBasketItem::getPaymorrowBasketSummaryLineItemPrefix( $iLineItem );

        $aWrappingSummary = array(
            $sPmWrappingPrefix . 'quantity'       => 1,
            $sPmWrappingPrefix . 'articleId'      => 'Wrapping', // Used for gift card
            $sPmWrappingPrefix . 'name'           => 'Wrapping Costs',
            $sPmWrappingPrefix . 'type'           => 'GOODS',
            $sPmWrappingPrefix . 'unitPriceGross' => $oWrappingCosts->getBruttoPrice(),
            $sPmWrappingPrefix . 'grossAmount'    => $oWrappingCosts->getBruttoPrice(),
            $sPmWrappingPrefix . 'vatAmount'      => $oWrappingCosts->getVatValue(),
            $sPmWrappingPrefix . 'vatRate'        => $oWrappingCosts->getVat(),

        );

        $aExpectedResult = array_merge( $aDeliverySummary, $aWrappingSummary );

        $this->assertEquals( $aExpectedResult, $SUT->_getPaymorrowAdditionalCostsSummary( $iLineItemInitial ) );
    }


    public function test_getPaymorrowAdditionalCostsSummary_shouldReturnGiftCardAndPaymentCostsSummary()
    {
        $SUT = $this->_getProxySUT();

        $iLineItemInitial = 498;
        $iLineItem        = $iLineItemInitial;

        $oGiftCardPrice = $this->_getPriceMock( 123, 34 );
        $SUT->setCost( 'oxgiftcard', $oGiftCardPrice );

        $oPaymentCostsPrice = $this->_getPriceMock( 323.12, 44 );
        $SUT->setCost( 'oxpayment', $oPaymentCostsPrice );

        $oUnknownCostPrice = $this->_getPriceMock( 100.0, 10.0 );
        $SUT->setCost( 'someothercost', $oUnknownCostPrice );

        $sPmGiftCardPrefix = OxpsPaymorrowOxBasketItem::getPaymorrowBasketSummaryLineItemPrefix( $iLineItem );

        $aPmGiftCardSummary = array(
            $sPmGiftCardPrefix . 'quantity'       => 1,
            $sPmGiftCardPrefix . 'articleId'      => 'GiftCard', // Used for gift card
            $sPmGiftCardPrefix . 'name'           => 'Gift Card Costs',
            $sPmGiftCardPrefix . 'type'           => 'GOODS',
            $sPmGiftCardPrefix . 'unitPriceGross' => $oGiftCardPrice->getBruttoPrice(),
            $sPmGiftCardPrefix . 'grossAmount'    => $oGiftCardPrice->getBruttoPrice(),
            $sPmGiftCardPrefix . 'vatAmount'      => $oGiftCardPrice->getVatValue(),
            $sPmGiftCardPrefix . 'vatRate'        => $oGiftCardPrice->getVat(),
        );
        $iLineItem++; // Incrementing to avoid Line Items array keys clash

        $sPmPaymentPrefix = OxpsPaymorrowOxBasketItem::getPaymorrowBasketSummaryLineItemPrefix( $iLineItem );

        $aPmPaymentSummary = array(
            $sPmPaymentPrefix . 'quantity'       => 1,
            $sPmPaymentPrefix . 'articleId'      => 'PaymentId', // Used for gift card
            $sPmPaymentPrefix . 'name'           => 'PaymentCosts',
            $sPmPaymentPrefix . 'type'           => 'PAYMENT_FEE',
            $sPmPaymentPrefix . 'unitPriceGross' => $oPaymentCostsPrice->getBruttoPrice(),
            $sPmPaymentPrefix . 'grossAmount'    => $oPaymentCostsPrice->getBruttoPrice(),
            $sPmPaymentPrefix . 'vatAmount'      => $oPaymentCostsPrice->getVatValue(),
            $sPmPaymentPrefix . 'vatRate'        => $oPaymentCostsPrice->getVat(),
        );

        $aExpectedResult = array_merge( $aPmGiftCardSummary, $aPmPaymentSummary );

        $this->assertEquals( $aExpectedResult, $SUT->_getPaymorrowAdditionalCostsSummary( $iLineItemInitial ) );
    }


    public function test_getPaymorrowAdditionalCostsSummary_shouldReturnTsProtectionAndVoucherCostsSummary()
    {
        $SUT = $this->_getProxySUT();

        $oTsProtectionPrice = $this->_getPriceMock( 159.5, 11.0 );
        $SUT->setCost( 'oxtsprotection', $oTsProtectionPrice );

        $SUT->setVoucherDiscount( 1021848.0 );

        // Set surcharge related mocks
        $this->setSessionParam( 'paymentid', '' );
        $this->setSessionParam( 'pm_verify', array('paymentid' => 'invoice') );

        $oPaymentMock = $this->getMock( 'oxPayment', array('__construct', 'load', 'getId', 'calculate', 'getPrice') );
        $oPaymentMock->expects( $this->once() )->method( 'load' )->with( $this->equalTo( 'invoice' ) )->will(
            $this->returnValue( true )
        );
        $oPaymentMock->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'invoice' ) );
        $oPaymentMock->expects( $this->once() )->method( 'calculate' );
        $oPaymentMock->expects( $this->once() )->method( 'getPrice' )->will(
            $this->returnValue( $this->_getPriceMock( 5.0, 19.0 ) )
        );
        oxTestModules::addModuleObject( 'oxPayment', $oPaymentMock );

        $aExpectedResult = array(
            'lineItem_498_quantity'       => 1,
            'lineItem_498_articleId'      => 'TsProtection',
            'lineItem_498_name'           => 'TS Protection Costs',
            'lineItem_498_type'           => 'GOODS',
            'lineItem_498_unitPriceGross' => 159.5,
            'lineItem_498_grossAmount'    => 159.5,
            'lineItem_498_vatAmount'      => 15.81,
            'lineItem_498_vatRate'        => 11.0,

            'lineItem_499_quantity'       => 1,
            'lineItem_499_articleId'      => 'PaymentId',
            'lineItem_499_name'           => 'PaymentCosts',
            'lineItem_499_type'           => 'PAYMENT_FEE',
            'lineItem_499_unitPriceGross' => 5.0,
            'lineItem_499_grossAmount'    => 5.0,
            'lineItem_499_vatAmount'      => 0.8,
            'lineItem_499_vatRate'        => 19.0,

            'lineItem_500_quantity'       => 1,
            'lineItem_500_articleId'      => 'Voucher',
            'lineItem_500_name'           => 'VoucherCosts',
            'lineItem_500_type'           => 'VOUCHER',
            'lineItem_500_unitPriceGross' => 1021848.0,
            'lineItem_500_grossAmount'    => 1021848.0,
            'lineItem_500_vatAmount'      => 0.0,
            'lineItem_500_vatRate'        => 0.0,
        );

        $this->assertEquals( $aExpectedResult, $SUT->_getPaymorrowAdditionalCostsSummary( 498 ) );
    }


    public function testGetPaymentMethodSurcharge_paymentIdSetInSession_returnEmptyArray()
    {
        $SUT = $this->_getProxySUT();

        $this->setSessionParam( 'paymentid', 'invoice' );

        $this->assertSame( array(), $SUT->_getPaymentMethodSurcharge() );
    }

    public function testGetPaymentMethodSurcharge_noPaymentFormDataInSession_returnEmptyArray()
    {
        $SUT = $this->_getProxySUT();

        $this->setSessionParam( 'paymentid', '' );
        $this->setSessionParam( 'pm_verify', '' );

        $this->assertSame( array(), $SUT->_getPaymentMethodSurcharge() );
    }

    public function testGetPaymentMethodSurcharge_noNewPaymentIdInSession_returnEmptyArray()
    {
        $SUT = $this->_getProxySUT();

        $this->setSessionParam( 'paymentid', '' );
        $this->setSessionParam( 'pm_verify', array('some_field' => 'invoice') );

        $this->assertSame( array(), $SUT->_getPaymentMethodSurcharge() );
    }

    public function testGetPaymentMethodSurcharge_paymentMethodNotLoadedById_returnEmptyArray()
    {
        $SUT = $this->_getProxySUT();

        $this->setSessionParam( 'paymentid', '' );
        $this->setSessionParam( 'pm_verify', array('paymentid' => 'invoice') );

        $oPaymentMock = $this->getMock( 'oxPayment', array('__construct', 'load', 'getId', 'calculate', 'getPrice') );
        $oPaymentMock->expects( $this->once() )->method( 'load' )->with( $this->equalTo( 'invoice' ) )->will(
            $this->returnValue( false )
        );
        $oPaymentMock->expects( $this->never() )->method( 'getId' );
        $oPaymentMock->expects( $this->never() )->method( 'calculate' );
        $oPaymentMock->expects( $this->never() )->method( 'getPrice' );
        oxTestModules::addModuleObject( 'oxPayment', $oPaymentMock );

        $this->assertSame( array(), $SUT->_getPaymentMethodSurcharge() );
    }

    public function testGetPaymentMethodSurcharge_surchargePriceNotCalculated_returnEmptyArray()
    {
        $SUT = $this->_getProxySUT();

        $this->setSessionParam( 'paymentid', '' );
        $this->setSessionParam( 'pm_verify', array('paymentid' => 'invoice') );

        $oPaymentMock = $this->getMock( 'oxPayment', array('__construct', 'load', 'getId', 'calculate', 'getPrice') );
        $oPaymentMock->expects( $this->once() )->method( 'load' )->with( $this->equalTo( 'invoice' ) )->will(
            $this->returnValue( true )
        );
        $oPaymentMock->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'invoice' ) );
        $oPaymentMock->expects( $this->once() )->method( 'calculate' );
        $oPaymentMock->expects( $this->once() )->method( 'getPrice' )->will( $this->returnValue( null ) );
        oxTestModules::addModuleObject( 'oxPayment', $oPaymentMock );

        $this->assertSame( array(), $SUT->_getPaymentMethodSurcharge() );
    }

    public function testGetPaymentMethodSurcharge_surchargePriceCalculatedAndArgumentEmpty_returnSurchargePriceObject()
    {
        $SUT = $this->_getProxySUT();

        $this->setSessionParam( 'paymentid', '' );
        $this->setSessionParam( 'pm_verify', array('paymentid' => 'invoice') );

        $oPriceMock = $this->_getPriceMock( 5.0, 19.0 );

        $oPaymentMock = $this->getMock( 'oxPayment', array('__construct', 'load', 'getId', 'calculate', 'getPrice') );
        $oPaymentMock->expects( $this->once() )->method( 'load' )->with( $this->equalTo( 'invoice' ) )->will(
            $this->returnValue( true )
        );
        $oPaymentMock->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'invoice' ) );
        $oPaymentMock->expects( $this->once() )->method( 'calculate' );
        $oPaymentMock->expects( $this->once() )->method( 'getPrice' )->will( $this->returnValue( $oPriceMock ) );
        oxTestModules::addModuleObject( 'oxPayment', $oPaymentMock );

        $this->assertSame( $oPriceMock, $SUT->_getPaymentMethodSurcharge() );
    }

    public function testGetPaymentMethodSurcharge_surchargePriceCalculatedAndArgumentIsNumber_returnSurchargeLineItemArray()
    {
        $SUT = $this->_getProxySUT();

        $this->setSessionParam( 'paymentid', '' );
        $this->setSessionParam( 'pm_verify', array('paymentid' => 'invoice') );

        $oPriceMock = $this->_getPriceMock( 5.0, 19.0 );

        $oPaymentMock = $this->getMock( 'oxPayment', array('__construct', 'load', 'getId', 'calculate', 'getPrice') );
        $oPaymentMock->expects( $this->once() )->method( 'load' )->with( $this->equalTo( 'invoice' ) )->will(
            $this->returnValue( true )
        );
        $oPaymentMock->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'invoice' ) );
        $oPaymentMock->expects( $this->once() )->method( 'calculate' );
        $oPaymentMock->expects( $this->once() )->method( 'getPrice' )->will( $this->returnValue( $oPriceMock ) );
        oxTestModules::addModuleObject( 'oxPayment', $oPaymentMock );

        $this->assertSame(
            array(
                'lineItem_888_quantity'       => 1,
                'lineItem_888_articleId'      => 'PaymentId',
                'lineItem_888_name'           => 'PaymentCosts',
                'lineItem_888_type'           => 'PAYMENT_FEE',
                'lineItem_888_unitPriceGross' => 5.0,
                'lineItem_888_grossAmount'    => 5.0,
                'lineItem_888_vatAmount'      => 0.8,
                'lineItem_888_vatRate'        => 19.0,
            ),
            $SUT->_getPaymentMethodSurcharge( 888 )
        );
    }


    public function testValidateCostPriceAndGetItsData_priceIsEmpty_returnEmptyArray()
    {
        $SUT = $this->_getProxySUT();

        $this->assertSame( array(), $SUT->_validateCostPriceAndGetItsData( null, 1, 'cost' ) );
    }

    public function testValidateCostPriceAndGetItsData_priceIsOfNotExpectedType_returnEmptyArray()
    {
        $SUT = $this->_getProxySUT();

        $this->assertSame( array(), $SUT->_validateCostPriceAndGetItsData( 100.0, 1, 'cost' ) );
    }

    public function testValidateCostPriceAndGetItsData_priceObjectHasZeroPriceValue_returnEmptyArray()
    {
        $SUT = $this->_getProxySUT();

        $this->assertSame( array(), $SUT->_validateCostPriceAndGetItsData( new oxPrice( 0.0 ), 1, 'cost' ) );
    }


    public function testGetShippingId_shippingIdNotSet_returnTheDefaultValue()
    {
        $SUT = $this->getMock( $this->getProxyClassName( 'OxpsPaymorrowOxBasket' ), array('getShippingId') );
        $SUT->expects( $this->once() )->method( 'getShippingId' )->will( $this->returnValue( '' ) );

        $this->assertSame( 'shippingCosts', $SUT->_getShippingId() );
    }

    public function testGetShippingId_shippingIdSet_returnTheSetValue()
    {
        $SUT = $this->getMock( $this->getProxyClassName( 'OxpsPaymorrowOxBasket' ), array('getShippingId') );
        $SUT->expects( $this->once() )->method( 'getShippingId' )->will( $this->returnValue( 'oxdefault' ) );

        $this->assertSame( 'oxdefault', $SUT->_getShippingId() );
    }


    public function testGetPaymentId_paymentIdNotSet_returnTheDefaultValue()
    {
        $SUT = $this->getMock( $this->getProxyClassName( 'OxpsPaymorrowOxBasket' ), array('getPaymentId') );
        $SUT->expects( $this->once() )->method( 'getPaymentId' )->will( $this->returnValue( '' ) );

        $this->assertSame( 'PaymentId', $SUT->_getPaymentId() );
    }

    public function testGetPaymentId_paymentIdSet_returnTheSetValue()
    {
        $SUT = $this->getMock( $this->getProxyClassName( 'OxpsPaymorrowOxBasket' ), array('getPaymentId') );
        $SUT->expects( $this->once() )->method( 'getPaymentId' )->will( $this->returnValue( 'oxinvoice' ) );

        $this->assertSame( 'oxinvoice', $SUT->_getPaymentId() );
    }


    public function testGetTsProductId_tsProtectionIdNotSet_returnTheDefaultValue()
    {
        $SUT = $this->getMock( $this->getProxyClassName( 'OxpsPaymorrowOxBasket' ), array('getTsProductId') );
        $SUT->expects( $this->once() )->method( 'getTsProductId' )->will( $this->returnValue( '' ) );

        $this->assertSame( 'TsProtection', $SUT->_getTsProductId() );
    }

    public function testGetTsProductId_tsProtectionIdSet_returnTheSetValue()
    {
        $SUT = $this->getMock( $this->getProxyClassName( 'OxpsPaymorrowOxBasket' ), array('getTsProductId') );
        $SUT->expects( $this->once() )->method( 'getTsProductId' )->will( $this->returnValue( 'oxid' ) );

        $this->assertSame( 'oxid', $SUT->_getTsProductId() );
    }
}
