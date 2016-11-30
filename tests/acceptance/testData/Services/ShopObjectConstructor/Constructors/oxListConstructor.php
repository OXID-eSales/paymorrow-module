<?php

/**
 * Class oxConfigCaller
 */
class oxListConstructor extends ObjectConstructor
{

    /**
     * Skip loading of config object, as it is already loaded
     *
     * @param $sOxId
     */
    public function load($sOxId) {
        $this->getObject()->init($sOxId, $this->_getTableName($sOxId));
    }

    /**
     * Calls object function with given parameters
     *
     * @param $sFunction
     * @param $aParameters
     * @return mixed
     */
    public function callFunction($sFunction, $aParameters)
    {
        if ($sFunction == 'getList') {
            $oObject = $this->getObject();
            $mResponse = $this->_formArrayFromList($oObject->getList());
        } else {
            $mResponse = parent::callFunction($sFunction, $aParameters);
        }

        return $mResponse;
    }

    /**
     * Returns formed array with data from given list
     *
     * @param $oList
     * @return array
     */
    protected function _formArrayFromList($oList)
    {
        $aData = array();
        foreach ($oList as $sKey => $oObject) {
            $aData[$sKey] = $this->_getObjectFieldValues($oObject);
        }

        return $aData;
    }

    /**
     * Returns object field values
     *
     * @param object $oObject
     * @return array
     */
    protected function _getObjectFieldValues($oObject)
    {
        $aData = array();
        $aFields = $oObject->getFieldNames();
        $sTableName = $this->_getTableName(get_class($oObject));
        foreach ($aFields as $sField) {
            $sFieldName = $sTableName.'__'.$sField;
            $aData[$sField] = $oObject->$sFieldName->value;
        }

        return $aData;
    }
}
