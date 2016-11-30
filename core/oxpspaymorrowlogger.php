<?php
/**
 * This file is part of the OXID module for Paymorrow payment.
 *
 * The OXID module for Paymorrow payment is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 *
 * The OXID eShop module for Paymorrow payment is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General
 * Public License for more details.
 *
 * Linking this library statically or dynamically with other modules is making a
 * combined work based on this library. Thus, the terms and conditions of the GNU
 * General Public License cover the whole combination.
 * As a special exception, the copyright holders of this library give you
 * permission to link this library with independent modules to produce an
 * executable, regardless of the license terms of these independent modules, and
 * to copy and distribute the resulting executable under terms of your choice,
 * provided that you also meet, for each linked independent module, the terms and
 * conditions of the license of that module. An independent module is a module
 * which is not derived from or based on this library. If you modify this library,
 * you may extend this exception to your version of the library, but you are not
 * obliged to do so. If you do not wish to do so, delete this exception statement
 * from your version.
 *
 * You should have received a copy of the GNU General Public License along with
 * the OXID module for Paymorrow payment. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Class OxpsPaymorrowLogger.
 */
class OxpsPaymorrowLogger
{

    const PAYMORROW_LOG_DIR_NAME = 'paymorrow';

    const PAYMORROW_DEFAULT_LOG_FILENAME = 'oxpspaymorrow_log.txt';


    /**
     * OxpsPaymorrowLogger instance
     *
     * @var OxpsPaymorrowLogger
     */
    private static $_instance = null;


    /**
     * @var string name for the log file
     */
    protected $_sFileName = self::PAYMORROW_DEFAULT_LOG_FILENAME;

    /**
     * @var string - Error Log Path
     */
    protected $_sErrorLogPath = null;


    public function __construct()
    {
        // Setting Default Error Log path
        $sPath = oxRegistry::getConfig()->getLogsDir() . self::PAYMORROW_LOG_DIR_NAME . DIRECTORY_SEPARATOR;

        $this->setErrorLogPath( $sPath );
    }


    /**
     * Set error log directory path.
     *
     * @param string $sErrorLogPath
     */
    public function setErrorLogPath( $sErrorLogPath )
    {
        $this->_sErrorLogPath = $sErrorLogPath;
    }

    /**
     * Method that writes the given info to the log.
     *
     * @param array $aInfo
     *
     * @return bool|string
     */
    public function log( array $aInfo )
    {
        /** @var  $oPmSettings OxpsPaymorrowSettings */
        $oPmSettings = oxNew( 'OxpsPaymorrowSettings' );

        return $oPmSettings->isLoggingEnabled()
            ? $this->_saveToFile( $this->_toConvertToString( $aInfo ) )
            : false;
    }

    /**
     * Logs Paymorrow to file with type and date appended.
     *
     * @param array  $aInfo
     * @param string $sType
     */
    public function logWithType( array $aInfo, $sType = '' )
    {
        $this->setFileName( sprintf( "oxpspaymorrow_%s_data-%s_log.txt", $sType, date( 'Y-m-d' ) ) );
        $this->log( $aInfo );
    }

    /**
     * Get error log file contents.
     *
     * @return string
     */
    public function getContents()
    {
        $sErrorLogPath = $this->getErrorLogPathWithName();

        $sLog = '';

        if ( is_file( $sErrorLogPath ) ) {
            $fErrorLog = fopen( $sErrorLogPath, "r" );

            while ( !feof( $fErrorLog ) ) {
                $sLog .= trim( fgets( $fErrorLog, 4096 ) ) . PHP_EOL;
            }

            fclose( $fErrorLog );
        }

        return $sLog;
    }

    /**
     * Get all error log files contents.
     *
     * @return string
     */
    public function getAllContents()
    {
        $sLog    = '';
        $sFormat = "----------------------------------------------------------------------\n" .
                   "%s\n" .
                   "----------------------------------------------------------------------\n\n" .
                   "%s\n\n";

        $sLogDirectoryPath = $this->getErrorLogPath();
        $hDir              = @opendir( $sLogDirectoryPath );

        if ( !empty( $hDir ) ) {
            while ( false !== ( $sFileName = readdir( $hDir ) ) ) {
                $sLog .= $this->_getFormattedContents( $sLogDirectoryPath, $sFileName, $sFormat );
            }
        }

        return $sLog;
    }

    /**
     * Get error log directory full path.
     * Also creates missing log folders is needed.
     *
     * @return bool|string
     */
    public function getErrorLogPath()
    {
        $sLogPath = $this->_sErrorLogPath;

        if ( !is_dir( $sLogPath ) ) {
            mkdir( $sLogPath, 0777, true );
        }

        return $sLogPath;
    }

    /**
     * Get full path of current error log file.
     *
     * @return string
     */
    public function getErrorLogPathWithName()
    {
        return $this->getErrorLogPath() . $this->getFileName();
    }

    /**
     * Set the name of current log file.
     *
     * @param string $sFilename
     */
    public function setFileName( $sFilename )
    {
        $this->_sFileName = $sFilename;
    }

    /**
     * Get the name of current log file.
     * If filename is not set returns the default filename.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->_sFileName ? $this->_sFileName : self::PAYMORROW_DEFAULT_LOG_FILENAME;
    }


    /**
     * Saves give data to file.
     *
     * @param string $sData Data to be written to file
     *
     * @return boolean True if save was successful
     */
    protected function _saveToFile( $sData )
    {
        return file_put_contents(
            $this->getErrorLogPathWithName(),
            $sData,
            FILE_APPEND
        );
    }

    /**
     * Converts the data array to string and returns it.
     *
     * @param $aArray array that needs to be converted
     *
     * @return string
     */
    protected function _toConvertToString( $aArray )
    {
        $sResult = date( "Y-m-d H:i:s", oxRegistry::get( "oxUtilsDate" )->getTime() ) . PHP_EOL;
        $sResult .= str_repeat( '=', 60 ) . PHP_EOL;

        foreach ( $aArray as $sKey => $sValue ) {
            $sResult .= " $sKey: $sValue" . PHP_EOL;
        }

        $sResult .= str_repeat( '=', 60 ) . PHP_EOL . PHP_EOL;

        return $sResult;
    }

    /**
     * Get formatted log file content.
     *
     * @param string $sLogDirectoryPath Log folder path.
     * @param string $sFileName         Log file name.
     * @param string $sFormat           Output format string.
     *
     * @return string
     */
    protected function _getFormattedContents( $sLogDirectoryPath, $sFileName, $sFormat )
    {
        $sFilePath = $sLogDirectoryPath . '/' . $sFileName;

        if ( in_array( $sFileName, array('.', '..', '.htaccess') ) or !is_file( $sFilePath ) ) {
            return '';
        }

        $this->setFileName( $sFileName );

        return sprintf( $sFormat, $sFileName, $this->getContents() );
    }
}
