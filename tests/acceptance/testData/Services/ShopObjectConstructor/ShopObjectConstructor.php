<?php

require_once 'Constructors/ConstructorFactory.php';

/**
 * Shop constructor class for modifying shop environment during testing
 * Class ShopConstructor
 */
class ShopObjectConstructor implements ShopServiceInterface
{
    /**
     * Loads object, sets class parameters and calls function with parameters.
     * classParams can act two ways - if array('param' => 'value') is given, it sets the values to given keys
     * if array('param', 'param') is passed, values of these params are returned.
     * classParams are only returned if no function is called. Otherwise function return value is returned.
     *
     * @return mixed
     */
    public function init()
    {
        $oxConfig = oxRegistry::getConfig();

        $oConstructorFactory = new ConstructorFactory();
        $oConstructor = $oConstructorFactory->getConstructor($oxConfig->getRequestParameter("cl"));

        $oConstructor->load($oxConfig->getRequestParameter("oxid"));

        if ($oxConfig->getRequestParameter('classparams')) {
            $mResult = $oConstructor->setClassParameters( $oxConfig->getRequestParameter('classparams') );
        }

        if ($oxConfig->getRequestParameter('fnc')) {
            $mResult = $oConstructor->callFunction($oxConfig->getRequestParameter('fnc'), $oxConfig->getRequestParameter('functionparams'));
        }

        return $mResult;
    }
}
