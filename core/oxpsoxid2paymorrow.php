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
 * Class OxpsOxid2Paymorrow.
 * OXID to Paymorrow gateway bootstrap.
 */
class OxpsOxid2Paymorrow
{

    /**
     * Get Paymorrow module settings instance.
     *
     * @return OxpsPaymorrowSettings
     */
    public function getPaymorrowSettings()
    {
        return oxNew( 'OxpsPaymorrowSettings' );
    }

    /**
     * Get Paymorrow gateway instance.
     *
     * @return OxpsPaymorrowGateway|PaymorrowGateway
     */
    public function getPaymorrowGateway()
    {
        return oxNew( 'OxpsPaymorrowGateway' );
    }

    /**
     * Get request controller instance.
     *
     * @return RequestController
     */
    public function getPaymorrowRequestController()
    {
        return oxNew( 'RequestController' );
    }

    /**
     * Get Paymorrow client instance.
     *
     * @return OxpsPaymorrowClient
     */
    public function getPaymorrowClient()
    {
        return oxNew( 'OxpsPaymorrowClient' );
    }

    /**
     * Get data provider instance.
     *
     * @return OxpsPaymorrowEshopDataProvider
     */
    public function getEshopDataProvider()
    {
        return oxNew( 'OxpsPaymorrowEshopDataProvider' );
    }

    /**
     * Get built resource proxy with merchant and endpoint url set.
     *
     * @return PaymorrowResourceProxy
     */
    public function getBuiltPaymorrowResourceProxy()
    {
        /** @var  $oPmResourceProxy PaymorrowResourceProxy */
        $oPmResourceProxy = oxNew( 'PaymorrowResourceProxy' );

        $oPmSettings = $this->getPaymorrowSettings();

        $oPmResourceProxy->setMerchantId( $oPmSettings->getMerchantId() );
        $oPmResourceProxy->setEndPointUrl( $oPmSettings->getPaymorrowResourcePath() );

        return $oPmResourceProxy;
    }

    /**
     * Collects user data for Paymorrow JavaScript library.
     *
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function getPaymorrowPrintData()
    {
        return $this->getEshopDataProvider()->printPmData();
    }

    /**
     * Get initialized Paymorrow gateway instance.
     *
     * @return OxpsPaymorrowGateway
     */
    public function buildPaymorrowGateway()
    {
        $oPmGateway = $this->getPaymorrowGateway();
        $oPmGateway->setPmClient( $this->getPaymorrowClient() );
        $oPmGateway->setEshopDataProvider( $this->getEshopDataProvider() );
        $oPmGateway->setEndPointUrl( $this->getPaymorrowSettings()->getEndPointURL() );

        /**
         * Using singleton pattern in order to get access to this object later
         * and check response from Paymorrow
         */
        $oPmGateway->setResponseHandler( oxRegistry::get( 'OxpsPaymorrowResponseHandler' ) );

        return $oPmGateway;
    }

    /**
     * Get built Paymorrow request controller with Paymorrow gateway set.
     *
     * @return RequestController
     */
    public function getBuiltPaymorrowRequestController()
    {
        $oPmRequestController = $this->getPaymorrowRequestController();

        $oPmGateway = $this->buildPaymorrowGateway();

        $oPmRequestController->setGateway( $oPmGateway );

        return $oPmRequestController;
    }

    /**
     * Get Paymorrow error handler instance.
     *
     * @return OxpsPaymorrowErrorHandler
     */
    public function getPaymorrowErrorHandler()
    {
        return oxNew( 'OxpsPaymorrowErrorHandler' );
    }
}
