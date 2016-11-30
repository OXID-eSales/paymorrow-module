<?php

/**
 * Class ServiceCaller
 */
class ServiceCaller
{
    /**
     * Switches active shop
     *
     * @param $sShopId
     */
    public function setActiveShop($sShopId)
    {
        $oConfig = oxRegistry::getConfig();
        if ($sShopId && $oConfig->getEdition() == 'EE') {
            $oConfig->setShopId($sShopId);
        }
    }

    /**
     * Switches active language
     *
     * @param $sLang
     * @throws Exception
     */
    public function setActiveLanguage($sLang)
    {
        if ($sLang) {
            $oLang = oxRegistry::getLang();
            $aLanguages = $oLang->getLanguageIds();
            $iLanguageId = array_search($sLang, $aLanguages );
            if ($iLanguageId === false) {
                throw new Exception("Language $sLang was not found or is not active in shop");
            }
            oxRegistry::getLang()->setBaseLanguage($iLanguageId);
        }
    }

    /**
     * Calls service
     *
     * @param $sServiceClass
     * @return mixed
     * @throws Exception
     */
    public function callService($sServiceClass)
    {
        $oService = $this->_createService($sServiceClass);

        return $oService->init();
    }

    /**
     * Creates Service object. All services must implement ShopService interface
     *
     * @param string $sServiceClass
     * @throws Exception
     * @return ShopServiceInterface
     */
    protected function _createService($sServiceClass)
    {
        $this->_includeServiceFile($sServiceClass);
        $oService = new $sServiceClass();

        if ( !($oService instanceof ShopServiceInterface)) {
            throw new Exception("Service $sServiceClass does not implement ShopServiceInterface interface!");
        }

        return $oService;
    }

    /**
     * Includes service main class file
     *
     * @param $sServiceClass
     * @throws Exception
     */
    protected function _includeServiceFile($sServiceClass)
    {
        $sFile = realpath($sServiceClass.'/'.$sServiceClass.'.php');

        if (!file_exists($sFile)) {
            throw new Exception("Service $sServiceClass not found in path $sFile!");
        }

        require_once $sFile;
    }
}
