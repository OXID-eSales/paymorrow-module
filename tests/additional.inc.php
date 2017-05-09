<?php

// Include Paymorrow test config
include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'paymorrow_config.php');


// Fill in Paymorrow Test Merchant ID and API keys
if (!empty(PAYMORROW_CONFIG)) {
    /** @var \OxidEsales\EshopCommunity\Core\Config $config */
    $config = \OxidEsales\EshopCommunity\Core\Registry::getConfig();
    $config->setConfigParam('paymorrowMerchantIdTest', PAYMORROW_SETTING_MERCHANT_ID);
    $config->setConfigParam('paymorrowKeysJson', PAYMORROW_SETTING_ALL_KEYS);
    $config->setConfigParam('paymorrowPrivateKeyTest', PAYMORROW_SETTING_PRIVATE_KEY);
    $config->setConfigParam('paymorrowPublicKeyTest', PAYMORROW_SETTING_PUBLIC_KEY);
    $config->setConfigParam('paymorrowPaymorrowKeyTest', PAYMORROW_SETTING_PM_PUBLIC_KEY);
    $config->setConfigParam('paymorrowLoggingEnabled', true);
}
