<?php

class oxObjectValidator
{

    /**
     * @var array
     */
    private $_aErrors = array();

    /**
     * @param $sClass
     * @param $aExpectedParams
     * @param null $sOxId
     * @return bool
     */
    public function validate($sClass, $aExpectedParams, $sOxId = null)
    {
        $aObjectParams = $this->_getObjectParameters($sClass, array_keys($aExpectedParams), $sOxId);

        $blResult = true;
        foreach ($aExpectedParams as $sKey => $sExpectedValue) {
            $sObjectValue = $aObjectParams[$sKey];
            if ($sExpectedValue !== $sObjectValue) {
                $this->_setError("'$sExpectedValue' != '$sObjectValue' on key '$sKey'");
                $blResult = false;
            }
        }

        return $blResult;
    }

    /**
     * Returns formed error message if parameters was not valid
     *
     * @return string
     */
    public function getErrorMessage()
    {
        $sMessage = '';
        $aErrors = $this->_getErrors();
        if (!empty($aErrors)) {
            $sMessage = "Expected and actual parameters do not match: \n";
            $sMessage .= implode("\n", $aErrors);
        }

        return $sMessage;
    }

    /**
     * Sets error message to error stack
     *
     * @param array $sMessage
     */
    protected function _setError($sMessage)
    {
        $this->_aErrors[] = $sMessage;
    }

    /**
     * Returns errors array
     *
     * @return array
     */
    protected function _getErrors()
    {
        return $this->_aErrors;
    }

    /**
     * Returns object parameters
     *
     * @param $sClass
     * @param array $aObjectParams
     * @param string $sOxId
     * @param string $sShopId
     * @return array
     */
    protected function _getObjectParameters($sClass, $aObjectParams, $sOxId = null, $sShopId = null)
    {
        $oServiceCaller = new oxServiceCaller();
        $oServiceCaller->setParameter('cl', $sClass);

        $sOxId = $sOxId ? $sOxId : 'lastInsertedId';
        $oServiceCaller->setParameter('oxid', $sOxId);
        $oServiceCaller->setParameter('classparams', $aObjectParams);

        return $oServiceCaller->callService('ShopObjectConstructor', $sShopId);
    }
}