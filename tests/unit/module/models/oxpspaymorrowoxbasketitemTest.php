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
 * Class Unit_Module_Models_OxpsPaymorrowOxBasketItemTest
 *
 * @see OxpsPaymorrowOxBasketItem
 */
class Unit_Module_Models_OxpsPaymorrowOxBasketItemTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowOxBasketItem
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
            'OxpsPaymorrowOxBasketItem',
            array('__construct', 'load', 'getUser', 'init', 'getArticle', 'getTitle')
        );

    }


    /**
     * Mocking Proxy Class to test Protected methods
     *
     * IDE - might underline methods with RED color, PLEASE IGNORE
     *
     * @param array $aParams
     *
     * @return OxpsPaymorrowOxBasketItem|oxBasketItem
     */
    protected function _getProxySUT( array $aParams = array() )
    {
        return $this->getProxyClass(
            'OxpsPaymorrowOxBasketItem', array_merge( array('load', '__construct'), $aParams )
        );
    }


    public function testGetPaymorrowBasketSummaryLineItemPrefix_returnStringLineItemWithSetCount()
    {
        $this->assertSame( 'lineItem_26_', $this->SUT->getPaymorrowBasketSummaryLineItemPrefix( 26 ) );
    }


    public function testGetProductNumber_noRelatedArticle_returnEmptyString()
    {
        $this->SUT->expects( $this->once() )->method( 'getArticle' )->will( $this->returnValue( null ) );

        $this->assertSame( '', $this->SUT->getProductNumber() );
    }

    public function testGetProductNumber_relatedArticleLoaded_returnRelatedArticleNumber()
    {
        $oArticle = $this->getMock( 'oxArticle', array('__construct', 'load') );

        $oArticle->oxarticles__oxartnum = new oxField( 'Art_123' );

        $this->SUT->expects( $this->once() )->method( 'getArticle' )->will( $this->returnValue( $oArticle ) );

        $this->assertSame( 'Art_123', $this->SUT->getProductNumber() );
    }


    public function test_getPaymorrowBasketItemSummary_shouldBasketItemLineSummary()
    {
        // Article mock
        $oArticle = $this->getMock( 'oxArticle', array('__construct', 'load', 'getTitle') );
        $oArticle->oxarticles__oxartnum = new oxField( 'Art_123' );

        // SUT mock
        $this->SUT->expects( $this->any() )->method( 'getArticle' )->will( $this->returnValue( $oArticle ) );
        $this->SUT->expects( $this->once() )->method( 'getTitle' )->will( $this->returnValue( 'Kite' ) );

        $oPrice = new oxPrice();
        $oPrice->setPrice( 19493.11, 22 );

        $this->SUT->setPrice( $oPrice );


        $this->assertEquals(
            array(
                'lineItem_5_quantity'       => 0.0,
                'lineItem_5_articleId'      => 'Art_123',
                'lineItem_5_name'           => 'Kite',
                'lineItem_5_type'           => 'GOODS',
                'lineItem_5_unitPriceGross' => 19493.11,
                'lineItem_5_grossAmount'    => 0.0,
                'lineItem_5_vatAmount'      => 0.0,
                'lineItem_5_vatRate'        => 22.0,
            ),
            $this->SUT->getPaymorrowBasketItemSummary( 5 )
        );
    }
}
