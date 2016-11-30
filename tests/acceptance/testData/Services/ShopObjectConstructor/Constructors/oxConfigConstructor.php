<?php

/**
 * Class oxConfigCaller
 */
class oxConfigConstructor extends ObjectConstructor
{

    /**
     * Skip loading of config object, as it is already loaded
     *
     * @param $sOxId
     */
    public function load($sOxId) {}

    /**
     * Sets class parameters
     *
     * @param array $aClassParams
     * @return array
     */
    public function setClassParameters($aClassParams)
    {
        $aValues = array();
        foreach ($aClassParams as $sConfKey => $aConfParams) {
            if (is_int($sConfKey)) {
                $aValues[$aConfParams] = $this->getObject()->getConfigParam($aConfParams);
            } else {
                $aFormedParams = $this->_formSaveConfigParameters($sConfKey, $aConfParams);
                if ($aFormedParams) {
                    $this->callFunction("saveShopConfVar", $aFormedParams);
                }
            }
        }

        return $aValues;
    }

    /**
     * Returns created object to work with
     *
     * @param $sClassName
     * @return oxConfig
     */
    protected function _createObject($sClassName)
    {
        return oxRegistry::getConfig();
    }

    /**
     * Forms parameters for saveShopConfVar function from given parameters
     *
     * @param $sConfKey
     * @param $aConfParams
     * @return array|bool
     */
    private function _formSaveConfigParameters($sConfKey, $aConfParams)
    {
        $sType = $aConfParams['type'] ? $aConfParams['type'] : null;
        $sValue = $aConfParams['value'] ? $aConfParams['value'] : null;
        $sModule = $aConfParams['module'] ? $aConfParams['module'] : null;

        if (($sType == "arr" || $sType == 'aarr') && !is_array($sValue)) {
            $sValue = unserialize(htmlspecialchars_decode($sValue));
        }
        return !empty($sType) ? array($sType, $sConfKey, $sValue, null, $sModule) : false;
    }
}
