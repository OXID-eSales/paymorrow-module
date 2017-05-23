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
 * Class Unit_Module_Core_OxpsPaymorrowResourceCacheTest
 *
 * @see OxpsPaymorrowResourceCache
 */
class Unit_Module_Core_OxpsPaymorrowResourceCacheTest extends OxidTestCase
{

    /**
     * @var OxpsPaymorrowResourceCache
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
        $this->SUT = $this->getMock( 'OxpsPaymorrowResourceCache', array('__construct') );
    }

    /**
     * Clean up state after test.
     */
    public function tearDown()
    {
        $sTempFolder = oxRegistry::getConfig()->getConfigParam( 'sCompileDir' ) . DIRECTORY_SEPARATOR . 'test_media';

        if ( is_file( $sTempFolder . DIRECTORY_SEPARATOR . 'test.css' ) ) {
            unlink( $sTempFolder . DIRECTORY_SEPARATOR . 'test.css' );
        }

        if ( is_file( $sTempFolder . DIRECTORY_SEPARATOR . 'my-test.js' ) ) {
            unlink( $sTempFolder . DIRECTORY_SEPARATOR . 'my-test.js' );
        }

        if ( is_dir( $sTempFolder ) ) {
            rmdir( $sTempFolder );
        }

        parent::tearDown();
    }


    public function testGetCacheFolder_nothingSet_returnDefaultNameMedia()
    {
        $this->assertSame( 'media', $this->SUT->getCacheFolder() );
    }

    public function testGetCacheFolder_nameSet_returnTheSetValue()
    {
        $this->SUT->setCacheFolder( 'my_cache' );

        $this->assertSame( 'my_cache', $this->SUT->getCacheFolder() );
    }


    public function testGetCacheTtl_nothingSet_returnDefaultTtl3600()
    {
        $this->assertSame( 14400, $this->SUT->getCacheTtl() );
    }

    public function testGetCacheTtl_ttlValueSet_returnTheSetIntegerValue()
    {
        $this->SUT->setCacheTtl( '7200' );

        $this->assertSame( 7200, $this->SUT->getCacheTtl() );
    }


    public function testPush_resourceFolderDoesNotExist_createsResourceFolder()
    {
        $sCacheFolder = oxRegistry::getConfig()->getConfigParam( 'sCompileDir' ) . DIRECTORY_SEPARATOR . 'test_media';

        $this->SUT->setCacheFolder( 'test_media' );

        $this->assertFalse( is_dir( $sCacheFolder ) );

        $this->SUT->push( '/css/test.css', array('body' => '/* CSS */') );

        $this->assertTrue( is_dir( $sCacheFolder ) );
    }

    public function testPush_resourceFolderCouldNotBeCreated_returnFalse()
    {
        $this->SUT->setCacheFolder( 'test_media/1/2/3"x``<>?' );

        $this->assertFalse( $this->SUT->push( '/css/test.css', array('body' => '/* CSS */') ) );
    }

    public function testPush_resourceFolderExists_savesSerializedDataToTheFile()
    {
        $sCacheFolder = oxRegistry::getConfig()->getConfigParam( 'sCompileDir' ) . DIRECTORY_SEPARATOR . 'test_media';

        $this->SUT->setCacheFolder( 'test_media' );

        $iReturn = $this->SUT->push( '/css/test.css', array('body' => '/* CSS */') );

        $this->assertFalse( empty( $iReturn ) );
        $this->assertTrue( is_integer( $iReturn ) );
        $this->assertTrue( is_file( $sCacheFolder . DIRECTORY_SEPARATOR . 'test.css' ) );
        $this->assertSame(
            'a:1:{s:4:"body";s:9:"/* CSS */";}',
            file_get_contents( $sCacheFolder . DIRECTORY_SEPARATOR . 'test.css' )
        );
    }


    public function testPop_resourceFolderCouldNotBeCreated_returnFalse()
    {
        $this->SUT->setCacheFolder( 'test_media/1/2/3"x``<>?' );

        $this->assertFalse( $this->SUT->pop( 'my-test.js' ) );
    }

    public function testPop_cacheFileDoesNotExist_returnFalse()
    {
        $this->SUT->setCacheFolder( 'test_media' );

        $this->assertFalse( $this->SUT->pop( 'my-test.js' ) );
    }

    public function testPop_cacheFileHasExpired_deleteItAndReturnFalse()
    {
        $sCacheFolder = oxRegistry::getConfig()->getConfigParam( 'sCompileDir' ) . DIRECTORY_SEPARATOR . 'test_media';

        $this->SUT->setCacheFolder( 'test_media' );
        $this->SUT->setCacheTtl( 0 );

        mkdir( $sCacheFolder );
        file_put_contents( $sCacheFolder . DIRECTORY_SEPARATOR . 'my-test.js', 'a:1:{s:4:"body";s:8:"/* JS */";}' );

        $this->assertTrue( is_file( $sCacheFolder . DIRECTORY_SEPARATOR . 'my-test.js' ) );

        $this->assertFalse( $this->SUT->pop( 'my-test.js' ) );

        $this->assertFalse( is_file( $sCacheFolder . DIRECTORY_SEPARATOR . 'my-test.js' ) );
    }

    public function testPop_cacheFileExistsAndIsValid_returnUnSerializedFileContent()
    {
        $sCacheFolder = oxRegistry::getConfig()->getConfigParam( 'sCompileDir' ) . DIRECTORY_SEPARATOR . 'test_media';

        $this->SUT->setCacheFolder( 'test_media' );

        mkdir( $sCacheFolder );
        file_put_contents( $sCacheFolder . DIRECTORY_SEPARATOR . 'my-test.js', 'a:1:{s:4:"body";s:8:"/* JS */";}' );

        $this->assertTrue( is_file( $sCacheFolder . DIRECTORY_SEPARATOR . 'my-test.js' ) );

        $this->assertSame( array('body' => '/* JS */'), $this->SUT->pop( 'my-test.js' ) );
    }


    public function testCleanCache_removeAllREsourceCacheFolderFiles()
    {
        $sCacheFolder = realpath(oxRegistry::getConfig()->getConfigParam( 'sCompileDir' )) . DIRECTORY_SEPARATOR . 'test_media';

        $this->SUT->setCacheFolder( 'test_media' );

        mkdir( $sCacheFolder );
        file_put_contents( $sCacheFolder . DIRECTORY_SEPARATOR . 'test.css', 'a:1:{s:4:"body";s:9:"/* CSS */";}' );
        file_put_contents( $sCacheFolder . DIRECTORY_SEPARATOR . 'my-test.js', 'a:1:{s:4:"body";s:8:"/* JS */";}' );

        $this->assertTrue( is_file( $sCacheFolder . DIRECTORY_SEPARATOR . 'test.css' ) );
        $this->assertTrue( is_file( $sCacheFolder . DIRECTORY_SEPARATOR . 'my-test.js' ) );

        $this->SUT->cleanCache();

        $this->assertFalse( is_file( $sCacheFolder . DIRECTORY_SEPARATOR . 'test.css' ) );
        $this->assertFalse( is_file( $sCacheFolder . DIRECTORY_SEPARATOR . 'my-test.js' ) );
        $this->assertTrue( is_dir( $sCacheFolder ) );
    }
}
