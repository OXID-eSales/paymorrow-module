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
 * Class OxpsPaymorrowClient.
 * Extends and implements abstract class AbstractPaymorrowClient.
 */
class OxpsPaymorrowClient extends AbstractPaymorrowClient
{

    /**
     * Overridden parent method.
     * Loads private key from the module settings to sign request data.
     *
     * @param string $sRequestString
     *
     * @return string
     */
    public function signRequest( $sRequestString )
    {
        $hPrivateKey = openssl_get_privatekey(
            oxRegistry::get( 'OxpsPaymorrowSettings' )->getPrivateKey()
        );

        openssl_sign( $sRequestString, $bSignature, $hPrivateKey );

        return bin2hex( $bSignature );
    }

    /**
     * Overridden parent method.
     * Loads public key (certificate) from the module settings to verify response data.
     *
     * @param string $sResponseString
     * @param string $sSignature
     *
     * @return bool
     */
    public function verifyResponse( $sResponseString, $sSignature )
    {
        $hPublicKey = openssl_get_publickey(
            oxRegistry::get( 'OxpsPaymorrowSettings' )->getPaymorrowKey()
        );

        return ( openssl_verify( $sResponseString, hex2bin( $sSignature ), $hPublicKey ) === 1 );
    }
}

if ( !function_exists( 'hex2bin' ) ) {

    /**
     * Implementation of hex2bin (which is missing For PHP version < 5.4.0)
     *
     * @codeCoverageIgnore
     *
     * @param string $sData
     *
     * @return string
     */
    function hex2bin( $sData )
    {
        static $mOld;

        if ( $mOld === null ) {
            $mOld = version_compare( PHP_VERSION, '5.2', '<' );
        }

        $blIsObject = false;

        if ( is_scalar( $sData ) || ( ( $blIsObject = is_object( $sData ) ) && method_exists(
                    $sData, '__toString'
                ) )
        ) {
            if ( $blIsObject && $mOld ) {
                ob_start();
                echo $sData;
                $sData = ob_get_clean();
            } else {
                $sData = (string) $sData;
            }
        } else {
            trigger_error(
                __FUNCTION__ . '() expects parameter 1 to be string, ' . gettype( $sData ) . ' given', E_USER_WARNING
            );

            return null; //null in this case
        }

        $iLength = strlen( $sData );

        if ( $iLength % 2 ) {
            trigger_error( __FUNCTION__ . '(): Hexadecimal input string must have an even length', E_USER_WARNING );

            return false;
        }

        if ( strspn( $sData, '0123456789abcdefABCDEF' ) != $iLength ) {
            trigger_error( __FUNCTION__ . '(): Input string must be hexadecimal string', E_USER_WARNING );

            return false;
        }

        return pack( 'H*', $sData );
    }
}
