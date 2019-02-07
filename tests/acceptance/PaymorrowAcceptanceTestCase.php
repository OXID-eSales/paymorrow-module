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
 * Test different checkout cases using Paymorrow payment methods.
 */
class PaymorrowAcceptanceTestCase extends OxidEsales\TestingLibrary\AcceptanceTestCase
{
    /**
     * @var bool
     */
    protected $_blStartMinkSession = false;

    /**
     * @var null|\Behat\Mink\Session
     */
    protected $_oMinkSession = null;

    /**
     * @var null|DocumentElement
     */
    protected $_oPage = null;

    /**
     * How much more time wait for these tests.
     */
    protected $_iWaitTimeMultiplier = 9;

    /**
     * Prepare shop for testing.
     */
    public function setUp()
    {
        parent::setUp();

        if (!PAYMORROW_CONFIG) {
            throw new \Exception( 'Paymorrow tests are not configured. Please set it up in "paymorrow_config.php" file.' . PHP_EOL );
        }

        $this->activateTheme('azure');

        // Start Mink session wuth Selenium driver
        $this->startMinkSession( 'selenium' );

        // Get page
        $this->_oMinkSession = $this->getMinkSession();
        $this->_oPage        = $this->_oMinkSession->getPage();
    }

    /**
     * Adds Paymorrow configuration
     *
     * @param string $testSuitePath
     *
     * @throws \Exception
     */
    public function addTestData($testSuitePath)
    {
        parent::addTestData($testSuitePath);

        if (!defined('PAYMORROW_CONFIG')) {
            $this->loadModuleSettingsFromFile();
        }
        $this->callShopSC('oxConfig', null, null, array(
            'paymorrowMerchantIdTest' => array(
                'type' => 'str',
                'value' => PAYMORROW_SETTING_MERCHANT_ID,
                'module' => 'module:oxpspaymorrow'
            ),
            'paymorrowKeysJson' => array(
                'type' => 'str',
                'value' => PAYMORROW_SETTING_ALL_KEYS,
                'module' => 'module:oxpspaymorrow'
            ),
            'paymorrowPrivateKeyTest' => array(
                'type' => 'str',
                'value' => PAYMORROW_SETTING_PRIVATE_KEY,
                'module' => 'module:oxpspaymorrow'
            ),
            'paymorrowPublicKeyTest' => array(
                'type' => 'str',
                'value' => PAYMORROW_SETTING_PUBLIC_KEY,
                'module' => 'module:oxpspaymorrow'
            ),
            'paymorrowPaymorrowKeyTest' => array(
                'type' => 'str',
                'value' => PAYMORROW_SETTING_PM_PUBLIC_KEY,
                'module' => 'module:oxpspaymorrow'
            ),
            'paymorrowLoggingEnabled' => array(
                'type' => 'bool',
                'value' => true,
                'module' => 'module:oxpspaymorrow'
            )
        ));
    }

    /**
     * Loads module settings file
     *
     * @throws \Exception
     */
    private function loadModuleSettingsFromFile()
    {
        $filePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'paymorrow_config.php';
        if (file_exists($filePath)) {
            include $filePath;
        }

        if ( !PAYMORROW_CONFIG ) {
            throw new \Exception( 'Paymorrow tests are not configured. Please set it up in "paymorrow_config.php" file.' . PHP_EOL );
        }
    }
}
