<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * PHPUnit_Framework_TestCase implementation for adding and testing all selenium tests from this dir
 */
class AllTestsSelenium extends PHPUnit_Framework_TestCase
{
    /**
     * Test suite
     *
     * @return object
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit');

        //adding ACCEPTANCE Tests
        if ( !( $sFilter = getenv( 'TEST_FILE_FILTER' ) ) ) {
            $sFilter = '*';
        }
        $aTestFiles = glob( oxTESTSUITEDIR."/{$sFilter}Test.php" );

        foreach ( $aTestFiles as $sFilename) {
            include_once $sFilename;
            $sFilename = oxTESTSUITEDIR.'_'.str_replace("/", "_", str_replace( array( ".php", oxTESTSUITEDIR.'/'), "", $sFilename));
            $suite->addTestSuite( $sFilename);
        }

        return $suite;
    }
}
