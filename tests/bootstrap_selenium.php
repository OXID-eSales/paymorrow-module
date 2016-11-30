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

error_reporting((E_ALL ^ E_NOTICE) | E_STRICT);
ini_set('display_errors', true);

chdir(dirname(__FILE__));

require_once "bootstrap_config.php";

if (!defined('oxPATH')) {
    die('Path to tested shop (oxPATH) is not defined');
}

define ('OXID_PHP_UNIT', true);

define ('oxCCTempDir', oxPATH . '/oxCCTempDir/');
if (!is_dir(oxCCTempDir)) {
    mkdir(oxCCTempDir, 0777, 1);
} else {
    array_map('unlink', glob(oxCCTempDir . "/*"));
}

function getShopBasePath()
{
    return oxPATH;
}

require_once getShopBasePath() . 'core/oxconfigfile.php';

// Get db instance.
$oConfigFile = new oxConfigFile(getShopBasePath() . "config.inc.php");

require_once 'unit/test_utils.php';
require_once getShopBasePath() . 'core/oxfunctions.php';

// Get db instance.
$oConfigFile = new oxConfigFile(getShopBasePath() . "config.inc.php");

oxRegistry::set("OxConfigFile", $oConfigFile);
oxRegistry::set("oxConfig", new oxConfig());

require_once 'acceptance/library/oxShopPreparation.php';
require_once 'acceptance/library/oxTestCase.php';
require_once './vendor/autoload.php';

define( 'hostUrl', getenv('SELENIUM_SERVER')? getenv('SELENIUM_SERVER') : $sSeleniumServerIp );
define('browserName', getenv('BROWSER_NAME')? getenv('BROWSER_NAME') : $sBrowserName );

$sShopUrl = getenv('SELENIUM_TARGET')? getenv('SELENIUM_TARGET') : $sShopUrl;

define ( 'SELENIUM_SCREENSHOTS_PATH', getenv('SELENIUM_SCREENSHOTS_PATH')? getenv('SELENIUM_SCREENSHOTS_PATH') : $sSeleniumScreenShotsPath );
define ( 'SELENIUM_SCREENSHOTS_URL', getenv('SELENIUM_SCREENSHOTS_URL')? getenv('SELENIUM_SCREENSHOTS_URL') : $sSeleniumScreenShotsUrl );

if (SELENIUM_SCREENSHOTS_PATH && !is_dir(SELENIUM_SCREENSHOTS_PATH)) {
    mkdir(SELENIUM_SCREENSHOTS_PATH, 0777, 1);
}

$sShopUrl = $sShopUrl ? $sShopUrl : $oConfigFile->sShopURL;
define('shopURL', rtrim($sShopUrl, '/').'/');

define ('oxTESTSUITEDIR', 'acceptance');

$oShopPreparation = new oxShopPreparation();
$oShopPreparation->setTemporaryFolder(oxCCTempDir);
$oShopPreparation->dumpDB('orig_db_dump');


/**
 * Merge 2 files and return output stored
 *
 * @param string $sFile1 File path
 * @param string $sFile2 File path
 *
 * @return string
 */
function merge_files($sFile1, $sFile2)
{
    if (file_exists($sFile1) && file_exists($sFile2)) {
        $sNewFileName = md5($sFile1) . ".sql";
        $sFileContents1 = file_get_contents($sFile1);
        $sFileContents2 = file_get_contents($sFile2);
        file_put_contents($sNewFileName, $sFileContents2 . PHP_EOL . $sFileContents1);
    }

    return $sNewFileName;
}

$sFirstShop = 'acceptance/use_first_shop.sql';
$sLastShop = 'acceptance/use_last_shop.sql';

$sDemoDataFileName = merge_files("acceptance/demodata_paymorrow.sql", $sFirstShop);
$sApiKeysFileName = merge_files("acceptance/paymorrowApiKeys.sql", $sFirstShop);
$aDemoDataFiles = array($sDemoDataFileName, $sApiKeysFileName);

if (isSUBSHOP) {
    $aDemoDataFiles[] = merge_files($sDemoDataFileName, $sLastShop);
    $aDemoDataFiles[] = merge_files('acceptance/copy_paymorrow_config_to_subshop.sql', $sLastShop);
    $aDemoDataFiles[] = merge_files('acceptance/adjust_demodata.sql', $sLastShop);
}

if (!SKIPSHOPSETUP) {
    foreach ($aDemoDataFiles as $sFileName) {
        if (file_exists($sFileName)) {
            $oShopPreparation->import($sFileName);
            echo "inserting $sFileName ". PHP_EOL;
        } else {
            echo $sFileName . " does not exist in " . __DIR__;
        }
    }
}

register_shutdown_function(function () {
    if (!SKIPSHOPRESTORE) {
        $oShopPreparation = new oxShopPreparation();
        $oShopPreparation->setTemporaryFolder(oxCCTempDir);
        $oShopPreparation->restoreDB("orig_db_dump");
    }
});