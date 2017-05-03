<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 *
 * @link http://www.oxid-esales.com
 * @copyright (c) OXID eSales AG 2003-#OXID_VERSION_YEAR#
 */

class oxTranslator
{

    /**
     * @var $_iLanguage integer variable
     */
    protected $_iLanguage;

    /**
     * @var $_blAdmin bool variable
     */
    protected $_blAdmin;

    /**
     * @var $_aUntranslated array variable
     */
    protected $_aUntranslated = array();

    /**
     * $_aUntranslated setter
     *
     * @param array $aUntranslated
     */
    public function setUntranslated( $aUntranslated )
    {
        $this->_aUntranslated = $aUntranslated;
    }

    /**
     * $_aUntranslated getter
     *
     * @return array
     */
    public function getUntranslated()
    {
        return $this->_aUntranslated;
    }

    /**
     * @var $_aKeys array variable
     */
    protected $_aKeys;

    /**
     * @var $_sTranslationPattern string variable
     */
    protected $_sTranslationPattern = '%(?<key>[a-zA-Z0-9_]+)%';

    /**
     * $_sTranslationPattern setter
     *
     * @param string $sTranslationPattern
     */
    public function setTranslationPattern( $sTranslationPattern )
    {
        $this->_sTranslationPattern = $sTranslationPattern;
    }

    /**
     * $_sTranslationPattern getter
     *
     * @return string
     */
    public function getTranslationPattern()
    {
        return $this->_sTranslationPattern;
    }

    /**
     * $_aKeys setter
     *
     * @param array $aKeys
     */
    protected function _setKeys( $aKeys )
    {
        $this->_aKeys = $aKeys;
    }

    /**
     * $_aKeys getter
     *
     * @return array
     */
    protected function _getKeys()
    {
        return $this->_aKeys;
    }


    /**
     * Sets admin value
     *
     * @param $blAdmin
     *
     * @return null
     */
    public function setAdmin( $blAdmin )
    {
        $this->_blAdmin = $blAdmin;
    }

    /**
     * Returns Admin value
     *
     * @return bool
     */
    public function getAdmin()
    {
        return $this->_blAdmin;
    }

    /**
     * $_iLanguage setter
     *
     * @param integer $iLanguage
     */
    public function setLanguage( $iLanguage )
    {
        $this->_iLanguage = $iLanguage;
    }

    /**
     * $_iLanguage setter by language name
     *
     * @param string $sName
     */
    public function setLanguageByName( $sName )
    {
        $this->_iLanguage = $this->getLanguageIdByName( $sName );
    }

    /**
     * $_iLanguage getter
     *
     * @return integer
     */
    public function getLanguage()
    {
        return $this->_iLanguage;
    }

    /**
     * @param int  $iLanguage
     * @param bool $blAdmin
     */
    public function __construct( $iLanguage = 1, $blAdmin = false )
    {
        $this->setLanguage( $iLanguage );
        $this->setAdmin( $blAdmin );
    }


    public function translate( $sString  )
    {
        $aUntranslated = array();
        if ( !$this->_isTranslateAble( $sString ) ) {
            return $sString;
        }

        $iLang = $this->getLanguage();
        $blAdmin = $this->getAdmin();
        $aTranslations = array();
        $aKeys = $this->_getKeys();
        foreach ( $aKeys as $sKey ) {
            $aTranslations[$sKey] = oxRegistry::getLang()->translateString( $sKey, $iLang, $blAdmin );

            if ( $aTranslations[$sKey] == $sKey ) {
                $aUntranslated[] = $sKey;
            }
        }
        $this->setUntranslated( $aUntranslated );

        foreach ( $aKeys as $sKey => $sValue ) {
            if ( in_array( $sValue, $aUntranslated ) ) {
                $aNewKeys[$sKey] = $sValue;
            } else {
                $aNewKeys[$sKey] = "%$sValue%";
            }
        }
        return str_replace( $aNewKeys, $aTranslations, $sString );
    }


    /**
     * Checks if string can be translated
     *
     * @param $sString
     *
     * @return oxTranslator
     */
    protected function _isTranslateAble( $sString )
    {
        $sPattern = $this->getTranslationPattern();
        $aMatches = array();
        if ( is_array( $sString ) ) {
            $sString = implode( '_DELIMITER_', $sString );
        }
        preg_match_all( "¦{$sPattern}¦", $sString, $aMatches );

        if ( $aMatches['key'] > 0 ) {
            $this->_setKeys( $aMatches['key'] );
            return true;
        }
        return false;
    }

    /**
     * @param string $sName
     *
     * @return int
     */
    public function getLanguageIdByName( $sName )
    {
        return array_search( $sName, oxRegistry::getLang()->getLanguageNames() );
    }
}
