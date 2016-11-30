<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once dirname(__FILE__) . "/../bootstrap.php";
require_once 'ServiceCaller.php';
require_once 'ShopServiceInterface.php';

try {
    $oxConfig = oxRegistry::getConfig();

    $oServiceCaller = new ServiceCaller();

    $oServiceCaller->setActiveShop($oxConfig->getRequestParameter('shp'));
    $oServiceCaller->setActiveLanguage($oxConfig->getRequestParameter('lang'));
    $mResponse = $oServiceCaller->callService($oxConfig->getRequestParameter('service'));

    echo serialize($mResponse);
} catch (Exception $e) {
    echo "EXCEPTION: ".$e->getMessage();
}
