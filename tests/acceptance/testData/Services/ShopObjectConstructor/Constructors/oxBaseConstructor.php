<?php

/**
 * Class oxConfigCaller
 */
class oxBaseConstructor extends ObjectConstructor
{

    /**
     * Initiates object instead of loading it
     *
     * @param string $sOxId
     */
    protected function _loadById($sOxId)
    {
        return $this->getObject()->init($sOxId);
    }

    /**
     * Returns core table name instead of oxBase
     *
     * @param string $sClass
     * @return string
     */
    protected function _getTableName($sClass)
    {
        return $this->getObject()->getCoreTableName();
    }
}
