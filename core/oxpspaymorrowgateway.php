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
 * Class OxpsPaymorrowGateway.
 */
class OxpsPaymorrowGateway extends PaymorrowGateway
{

    /**
     * Magic call method making a default workflow call to Paymorrow API.
     *
     * @param string $sMethodName
     * @param array  $aArguments
     *
     * @return array
     */
    public function __call( $sMethodName, $aArguments )
    {
        // Get all gateway related instances: client, data provider and response, handler

        /** @var OxpsOxid2Paymorrow $oOxidToPm */
        $oOxidToPm = oxNew( 'OxpsOxid2Paymorrow' );

        /** @var OxpsPaymorrowClient|PaymorrowClient $oClient */
        $oClient = $this->getPmClient();

        /** @var OxpsPaymorrowEshopDataProvider $oDataProvider */
        $oDataProvider = $oOxidToPm->getEshopDataProvider();

        /** @var OxpsPaymorrowResponseHandler $oResponseHandler */
        $oResponseHandler = oxRegistry::get( 'OxpsPaymorrowResponseHandler' );

        // Set method URL
        $oClient->setEndPoint( $this->getEndPointUrl() . $sMethodName );

        // Collect auth data and perform a request
        $aResponseData = $oClient->sendRequest(
            array_merge( $oDataProvider->collectCommonData(), (array) reset( $aArguments ) )
        );

        // Check it response is OK or an error
        if ( isset( $aResponseData['response_status'] ) and ( $aResponseData['response_status'] === 'OK' ) ) {
            $sResponseHandlerMethodFormat = 'handle%sResponseOK';
        } else {
            $sResponseHandlerMethodFormat = 'handle%sResponseError';
        }

        // Call the response handler method
        $sResponseHandlerMethod = sprintf( $sResponseHandlerMethodFormat, ucfirst( $sMethodName ) );
        $oResponseHandler->$sResponseHandlerMethod( $aResponseData );

        // Return formatted response data
        return $this->prepareResponseData( $aResponseData );
    }


    /**
     * Non-static alias for parent method `addressHash`.
     *
     * @codeCoverageIgnore
     *
     * @param array $aData
     *
     * @return string
     */
    public function getAddressHash( array $aData )
    {
        return parent::addressHash( $aData );
    }
}
