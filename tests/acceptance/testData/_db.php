<?php
/**
 * Full reinstall
 */

error_reporting( (E_ALL ^ E_NOTICE) | E_STRICT );
ini_set('display_errors', true);

echo "<h1>Full reinstall of OXID eShop</h1>";
class _config {
    function __construct(){
        include "config.inc.php";
        include "core/oxconfk.php";
    }
}
$_cfg = new _config();

if (!defined('OXID_VERSION_SUFIX')) {
    define('OXID_VERSION_SUFIX', '');
}

echo "<h2>Delete cookies</h2>";
echo "<ol>";
{
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $aCookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach($aCookies as $sCookie) {
            $sRawCookie = explode('=', $sCookie);
            setcookie(trim( $sRawCookie[0] ), '', time() - 10000, '/');
            echo "<li>".$sRawCookie[0]."</li>";
        }
    }
}
echo "</ol>";


echo "<hr>";


echo "<h2>Cleanup tmp directory</h2>";
echo "<ol>";
{
    echo "Temp dir: ".$_cfg->sCompileDir;
    delTree( $_cfg->sCompileDir );
}
echo "</ol>";


echo "<hr>";


echo "<h2>Install and configure database</h2>";
echo "<ol>";
{
    $_key      = $_cfg->sConfigKey;
    $sCharset = $_cfg->iUtfMode? 'utf8' : 'latin1';
    $oDB       = mysql_connect( $_cfg->dbHost, $_cfg->dbUser, $_cfg->dbPwd);

    if ($_cfg->iUtfMode) {
        mysql_query("alter schema character set utf8 collate utf8_general_ci",$oDB);
        mysql_query("set names 'utf8'",$oDB);
        mysql_query("set character_set_database=utf8",$oDB);
        mysql_query("set character set latin1",$oDB);//mysql_query("set character set utf8",$oDB);
        mysql_query("set character_set_connection = utf8",$oDB);
        mysql_query("set character_set_results = utf8",$oDB);
        mysql_query("set character_set_server = utf8",$oDB);
    } else {
        mysql_query("alter schema character set latin1 collate latin1_general_ci",$oDB);
        mysql_query("set character set latin1",$oDB);
    }




        $sShopId  = 'oxbaseshop';

    echo "<li>drop database '".$_cfg->dbName."'</li>";
    mysql_query( 'drop database `'.$_cfg->dbName.'`', $oDB);
    echo "<li>create database '".$_cfg->dbName."'</li>";
    mysql_query( 'create database `'.$_cfg->dbName.'` collate '.$sCharset.'_general_ci', $oDB);

    echo "<li>select database '".$_cfg->dbName."'</li>";
    mysql_select_db( $_cfg->dbName , $oDB);

    echo "<li>insert '".dirname(__FILE__).'/setup/sql'.OXID_VERSION_SUFIX.'/'.'database.sql'."'</li>";
    passthru ('mysql -h'.$_cfg->dbHost.' -u'.$_cfg->dbUser.' -p'.$_cfg->dbPwd.' '.$_cfg->dbName.'  --default-character-set='.$sCharset.' < '.dirname(__FILE__).'/setup/sql'.OXID_VERSION_SUFIX.'/'.'database.sql');

    if (isset($_GET['test']) || getenv('TEST')) {
        $sFile = dirname(__FILE__).'/../tests/testsql/testdata'.OXID_VERSION_SUFIX.'.sql';
    } else {
        $sFile = dirname(__FILE__).'/setup/sql'.OXID_VERSION_SUFIX.'/'.'demodata.sql';
    }

    echo "<li>insert $sFile in $sCharset mode</li>";
    passthru ('mysql -h'.$_cfg->dbHost.' -u'.$_cfg->dbUser.' -p'.$_cfg->dbPwd.' '.$_cfg->dbName.'  --default-character-set='.$sCharset.' < '.$sFile);

    echo "<li>set configuration parameters</li>";
    mysql_query( "delete from oxconfig where oxvarname in ('iSetUtfMode','blLoadDynContents','sShopCountry')", $oDB);
    mysql_query( "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values ".
        "('config1', '{$sShopId}', 'iSetUtfMode',       'str',  ENCODE('0', '{$_key}') ),".
        "('config2', '{$sShopId}', 'blLoadDynContents', 'bool', ENCODE('1', '{$_key}') ),".
        "('config3', '{$sShopId}', 'sShopCountry',      'str',  ENCODE('de','{$_key}') )" , $oDB);

    if($sSerial) {

        require_once "core/oxserial.php";

        $oSerial = new oxSerial();
        $oSerial->setEd($iEdition);
        $oSerial->isValidSerial($sSerial);

        echo "<li>add demo serial '{$sSerial}'</li>";

        mysql_query( "update oxshops set oxserial = '{$sSerial}'", $oDB);
        mysql_query( "delete from oxconfig where oxvarname in ('aSerials','sTagList','IMD','IMA','IMS')", $oDB);
        mysql_query( "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values ".
            "('serial1', '{$sShopId}', 'aSerials', 'arr', ENCODE('". serialize(array($sSerial))         ."','{$_key}') ),".
            "('serial2', '{$sShopId}', 'sTagList', 'str', ENCODE('". time()                             ."','{$_key}') ),".
            "('serial3', '{$sShopId}', 'IMD',      'str', ENCODE('". $oSerial->getMaxDays($sSerial)     ."','{$_key}') ),".
            "('serial4', '{$sShopId}', 'IMA',      'str', ENCODE('". $oSerial->getMaxArticles($sSerial) ."','{$_key}') ),".
            "('serial5', '{$sShopId}', 'IMS',      'str', ENCODE('". $oSerial->getMaxShops($sSerial)    ."','{$_key}') )" , $oDB);
    }

    if ($_cfg->iUtfMode) {
        echo "<li>convert shop config to utf8</li>";

        $rs = mysql_query("select oxvarname, oxvartype, DECODE( oxvarvalue, '{$_key}') as oxvarvalue 
                           from oxconfig 
                           where oxvartype in ('str', 'arr', 'aarr') 
                           #AND oxvarname != 'aCurrencies'
                           ", $oDB);

        $aCnv =array();
        while ( $aRow = mysql_fetch_assoc($rs) ) {

            if ( $aRow['oxvartype'] == 'arr' || $aRow['oxvartype'] == 'aarr' ) {
                $aRow['oxvarvalue'] = unserialize( $aRow['oxvarvalue'] );
            }
            $aRow['oxvarvalue'] = to_utf8($aRow['oxvarvalue']);
            $aCnv[] = $aRow;
        }

        foreach ( $aCnv as $oCnf ) {
            $_vnm = $oCnf['oxvarname'];
            $_val = $oCnf['oxvarvalue'];
            if ( is_array($_val) ) {
                $_val = mysql_real_escape_string(serialize($_val),$oDB);
            } elseif(is_string($_val)) {
                $_val = mysql_real_escape_string($_val,$oDB);
            }

            mysql_query("update oxconfig set oxvarvalue = ENCODE( '{$_val}','{$_key}') where oxvarname = '{$_vnm}';",$oDB);
        }

        // Change currencies value to same as after 4.6 setup because previous encoding break it.
            mysql_query("
           	REPLACE INTO `oxconfig` 
           	(`OXID`, `OXSHOPID`, `OXMODULE`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) 
           	VALUES
            ('3c4f033dfb8fd4fe692715dda19ecd28', 'oxbaseshop', '', 'aCurrencies', 'arr', 0x4dbace2972e14bf2cbd3a9a45157004422e928891572b281961cdebd1e0bbafe8b2444b15f2c7b1cfcbe6e5982d87434c3b19629dacd7728776b54d7caeace68b4b05c6ddeff2df9ff89b467b14df4dcc966c504477a9eaeadd5bdfa5195a97f46768ba236d658379ae6d371bfd53acd9902de08a1fd1eeab18779b191f3e31c258a87b58b9778f5636de2fab154fc0a51a2ecc3a4867db070f85852217e9d5e9aa60507);
           	           
           ");
    }


}
echo "</ol>";

echo "<hr>",
    "<h3><a target='shp' href='".$_cfg->sShopURL."'>to Shop &raquo; </a></h3>",
    "<h3><a target='adm' href='".$_cfg->sShopURL."/admin/'>to Admin &raquo; </a></h3>";

function delTree( $dir, $rmBaseDir = false ) {
    $files = array_diff( scandir( $dir ), array('.', '..') );
    foreach ($files as $file) {
        ( is_dir( "$dir/$file" ) ) ? delTree( "$dir/$file", true ) : @unlink( "$dir/$file" );
    }
    if ( $rmBaseDir ) {
        @rmdir( $dir );
    }
}

function to_utf8($in)
{
    if (is_array($in)) {
        foreach ($in as $key => $value) {
            $out[to_utf8($key)] = to_utf8($value);
        }
    } elseif(is_string($in)) {
        return iconv( 'iso-8859-15', 'utf-8', $in );
    } else {
        return $in;
    }
    return $out;
}

function needToTurnVarnishOn($_cfg) {
    return (bool) $_cfg->turnOnVarnish || $_GET['RP'] || getenv('RP');
}

function turnVarnishOn($oDB) {
    mysql_query(
            "DELETE from oxconfig WHERE oxshopid = 1 AND oxvarname in ('iLayoutCacheLifeTime', 'blReverseProxyActive');"
            ,$oDB
    );
    mysql_query(
        "INSERT INTO oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue)
         values   ('35863f223f91930177693956aafe69e6', 1, 'iLayoutCacheLifeTime', 'str', 0xB00FB55D),
                  ('dbcfca66eed01fd43963443d35b109e0', 1, 'blReverseProxyActive',  'bool', 0x07);"
        , $oDB
    );
}

function updateSerialEnableVarnish($oDB) {
    mysql_query("
        UPDATE `oxconfig`
        SET `OXVARVALUE` = 0x4dba322c77e44ef7ced6aca7b8550246ca6fd31de97aa3193dcde8b5c5640c55d9b0387c5886f00375cd11bb89a1d33dbe05938f9b
        WHERE `OXVARNAME` = 'aSerials';
        , $oDB
    ");

    mysql_query("
        UPDATE `oxshops`
        SET oxserial = 'EF7FV-B9TA8-3R3SD-MZNU4-7NWM3-AN7AU';"
        , $oDB
    );

    mysql_query(
        "UPDATE `oxconfig` SET `OXVARVALUE` = 0xfbc1b45c WHERE `OXVARNAME` = 'IMA';"
        , $oDB
    );

    mysql_query(
        "UPDATE `oxconfig` SET `OXVARVALUE` = 0xb0c6 WHERE `OXVARNAME` = 'IMD';"
        , $oDB
    );

    mysql_query(
        "UPDATE `oxconfig` SET `OXVARVALUE` = 0x80e8 WHERE `OXVARNAME` = 'IMS';"
        , $oDB
    );
}
