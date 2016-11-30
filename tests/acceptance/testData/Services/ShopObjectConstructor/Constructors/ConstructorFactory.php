<?php

require_once 'ObjectConstructor.php';

/**
 * Class CallerFactory
 */
class ConstructorFactory
{
    /**
     * @param string $sClassName
     * @return ObjectConstructor
     */
    public function getConstructor($sClassName)
    {
        $sConstructorClass = $this->_getConstructorClass($sClassName)?: 'ObjectConstructor';

        return new $sConstructorClass($sClassName);
    }

    /**
     * @param $sClassName
     * @return bool|string
     */
    protected function _getConstructorClass($sClassName)
    {
        $sConstructorClass = $sClassName . "Constructor";
        $sFile = realpath(__DIR__.'/'.$sConstructorClass.".php");

        if (file_exists($sFile)) {
            include_once($sFile);
            return $sConstructorClass;
        }

        return false;
    }
}
