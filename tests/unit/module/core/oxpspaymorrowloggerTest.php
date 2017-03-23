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
 * Class Unit_Module_Core_OxpsPaymorrowLoggerTest
 *
 * @see OxpsPaymorrowLogger
 */
class Unit_Module_Core_OxpsPaymorrowLoggerTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowLogger
     */
    protected $SUT;


    /**
     * Set initial objects state.
     *
     * @return null|void
     */
    public function setUp()
    {
        parent::setUp();

        // SUT mock
        $this->SUT = $this->getMock( 'OxpsPaymorrowLogger', array('__construct', 'load', 'init') );

        \org\bovigo\vfs\vfsStreamWrapper::register();
        \org\bovigo\vfs\vfsStreamWrapper::setRoot(new \org\bovigo\vfs\vfsStreamDirectory('log_root'));

        //vfsStreamWrapper::register();
        //vfsStreamWrapper::setRoot( new vfsStreamDirectory( 'log_root' ) );
    }


    /**
     * Mocking Proxy Class to test Protected methods
     *
     * IDE - might underline methods with RED color, PLEASe IGNORE
     *
     * @param array $aParams
     *
     * @return oxpspaymorrowlogger
     */
    protected function _getProxySUT( array $aParams = array() )
    {
        return $this->getProxyClass( 'oxpspaymorrowlogger', $aParams );
    }


    public function test_GetFileName_shouldReturnConstantFileName()
    {
        /** @var oxpsPaymorrowLogger $oPmLogger */
        $oPmLogger = $this->getMock( 'oxpsPaymorrowLogger', array('__construct') );

        $this->assertEquals( $oPmLogger->getFileName(), $this->SUT->getFileName() );
    }


    public function test_setGetFileName_shouldReturnSetFileName()
    {
        $this->SUT->setFileName( 'UNIT_TEST_FILENAME.txt' );

        $this->assertEquals( 'UNIT_TEST_FILENAME.txt', $this->SUT->getFileName() );
    }


    /**
     * Testing if logging to file works
     *
     * Procedure:
     * - 1. Setting custom file name for SUT -> logging message
     * - 2. Reading same file to string
     * - 3. Comparing if both SUT and read file string contents match
     * - 4. Tearing down (deleting files)
     */
    public function test_log_shouldReturnLoggedArrayFromFile()
    {
        $this->setConfigParam('paymorrowLoggingEnabled', true);

        $sUnitTestFileName = 'unit_test_log.txt';

        $this->SUT->setFileName( $sUnitTestFileName );

        $sUnitTestFilePath = $this->SUT->getErrorLogPathWithName();

        $aArrayToLog = array('TEST' => "TESTING_LOG_MESSAGE");


        /**
         * Logging Message
         */
        $this->SUT->log( $aArrayToLog );

        $sLoggedMessage = '';

        if ( file_exists( $sUnitTestFilePath ) ) {
            $fUnitTestFile = fopen( $sUnitTestFilePath, "r" );
            while ( !feof( $fUnitTestFile ) ) {
                $sLoggedMessage .= trim( fgets( $fUnitTestFile, 4096 ) ) . PHP_EOL;
            }
            fclose( $fUnitTestFile );
        } else {
            $sLoggedMessage = 'Test file does not exist !';
        }

        $this->assertEquals( $sLoggedMessage, $this->SUT->getContents() );

        /**
         * Tear down
         */
        unlink( $sUnitTestFilePath );
    }


    public function test_logWithType_shouldCreateLogFileByDate()
    {
        $this->setConfigParam('paymorrowLoggingEnabled', true);

        $sType = 'UnitTest';

        $this->SUT->logWithType( array(), $sType );

        $sErrorLogPath = $this->SUT->getErrorLogPathWithName();

        $sFileName = sprintf( "oxpspaymorrow_%s_data-%s_log.txt", 'UnitTest', date( 'Y-m-d' ) );

        $this->assertStringEndsWith( $sFileName, $sErrorLogPath );

        //Tear Down
        $this->assertTrue( unlink( $sErrorLogPath ), 'Failed to Tear down: ' . $sErrorLogPath );
    }


    public function test_getErrorLogPath_shouldCreateNewErrorFolderForErrorLogs()
    {
        $sFileName = 'unit_test_log.txt';

        $sDirName = 'unitTest';

        $this->SUT->setFileName( $sFileName );

        $sLogPath = oxRegistry::getConfig()->getLogsDir()
                    . DIRECTORY_SEPARATOR
                    . $sDirName;

        $this->SUT->setErrorLogPath( $sLogPath );

        $this->SUT->getErrorLogPath();


        $this->assertTrue( file_exists( $sLogPath ) );

        //Tear Down
        $this->assertTrue( rmdir( $sLogPath ), 'Failed to Tear down: ' . $sLogPath );
    }


    public function testGetAllContents_cannotOpenLogDir_returnEmptyString()
    {
        $SUT = $this->getMock( 'OxpsPaymorrowLogger', array('__construct', 'load', 'init', 'getErrorLogPath') );
        $SUT->expects( $this->once() )->method( 'getErrorLogPath' )->will( $this->returnValue( '/fake/path/' ) );

        $this->assertSame( '', $SUT->getAllContents() );
    }

    public function testGetAllContents_logDirOpened_returnAllItsFilesContents()
    {
        $sPath = $this->SUT->getErrorLogPath();

        file_put_contents( $sPath . 'log1.txt', 'LOG FILE ONE' );
        file_put_contents( $sPath . 'log2.log', 'SOME LOG TWO' );

        $sAllLog = $this->SUT->getAllContents();

        $this->assertContains( 'LOG FILE ONE', $sAllLog );
        $this->assertContains( 'SOME LOG TWO', $sAllLog );

        unlink( $sPath . 'log1.txt' );
        unlink( $sPath . 'log2.log' );
    }
}
