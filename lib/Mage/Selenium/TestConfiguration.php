<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test run configuration
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_TestConfiguration
{
    /**
     * Data helper instance
     *
     * @var Mage_Selenium_Helper_Data
     */
    protected $_dataHelper = null;

    /**
     * Data generator helper instance
     *
     * @var Mage_Selenium_Helper_DataGenerator
     */
    protected $_dataGenerator = null;

    /**
     * Page helper instance
     *
     * @var Mage_Selenium_Helper_Page
     */
    protected $_pageHelper = null;

    /**
     * File helper instance
     *
     * @var Mage_Selenium_Helper_File
     */
    protected $_fileHelper = null;

    /**
     * Application helper instance
     *
     * @var Mage_Selenium_Helper_Application
     */
    protected $_applicationHelper = null;

    /**
     * Uimap helper instance
     *
     * @var Mage_Selenium_Helper_Uimap
     */
    protected $_uimapHelper = null;

    /**
     * Initialized browsers connections
     *
     * @var array[int]PHPUnit_Extensions_SeleniumTestCase_Driver
     */
    protected $_drivers = array();

    /**
     * Current browser connection
     *
     * @var PHPUnit_Extensions_SeleniumTestCase_Driver
     */
    public $driver = null;

    /**
     * Confiration object instance
     *
     * @var Mage_Selenium_TestConfiguration
     */
    public static $instance = null;

    /**
     * Test data
     *
     * @var array
     */
    protected $_testData = array();

    /**
     * Configuration data
     *
     * @var array
     */
    protected $_configData = array();

    /**
     * Constructor (defined as private to implement singleton)
     */
    private function __construct()
    {
    }

    /**
     * Destructor<br>
     * Extension: defines, browser need to be restarted or not.
     */
    public function  __destruct()
    {
        if ($this->getConfigValue('browsers/default/doNotKillBrowsers')
            != 'true' && $this->_drivers
        ) {
            foreach ($this->_drivers as $driver) {
                $driver->setContiguousSession(false);
                $driver->stop();
            }
        }
    }

    /**
     * Initializes test configuration
     *
     * @return Mage_Selenium_TestConfiguration
     */
    public static function initInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            self::$instance->init();
        }
        return self::$instance;
    }

    /**
     * Initializes test configuration instance which includes:
     * <li>Initialize configuration
     * <li>Initialize DataSets
     * <li>Initialize UIMap instance
     * <li>Initialize all drivers connections from configuration
     *
     * @return Mage_Selenium_TestConfiguration
     */
    public function init()
    {
        $this->_initConfig();
        $this->_initTestData();
        $this->getUimapHelper();
        $this->_initDrivers();
        return $this;
    }

    /**
     * Performs retrieving of file helper instance
     *
     * @return Mage_Selenium_Helper_File
     */
    public function getFileHelper()
    {
        if (is_null($this->_fileHelper)) {
            $this->_fileHelper = new Mage_Selenium_Helper_File($this);
        }
        return $this->_fileHelper;
    }

    /**
     * Performs retrieving of file helper instance
     *
     * @param Mage_Selenium_TestCase $testCase Current test case as object (by default = NULL)
     * @param Mage_Selenium_Helper_Application $applicationHelper Current tested application as object (by default = NULL)
     *
     * @return Mage_Selenium_Helper_Page
     */
    public function getPageHelper($testCase=null, $applicationHelper=null)
    {
        if (is_null($this->_pageHelper)) {
            $this->_pageHelper = new Mage_Selenium_Helper_Page($this);
        }
        if (!is_null($testCase)) {
            $this->_pageHelper->setTestCase($testCase);
        }
        if (!is_null($applicationHelper)) {
            $this->_pageHelper->setApplicationHelper($applicationHelper);
        }
        return $this->_pageHelper;
    }

    /**
     * Performs retrieving of Data Generator helper instance
     *
     * @return Mage_Selenium_Helper_DataGenerator
     */
    public function getDataGenerator()
    {
        if (is_null($this->_dataGenerator)) {
            $this->_dataGenerator = new Mage_Selenium_Helper_DataGenerator($this);
        }
        return $this->_dataGenerator;
    }

    /**
     * Performs retrieving of Data helper instance
     *
     * @return Mage_Selenium_Helper_Data
     */
    public function getDataHelper()
    {
        if (is_null($this->_dataHelper)) {
            $this->_dataHelper = new Mage_Selenium_Helper_Data($this);
        }
        return $this->_dataHelper;
    }

    /**
     * Performs retrieving of Application helper instance
     *
     * @return Mage_Selenium_Helper_File
     */
    public function getApplicationHelper()
    {
        if (is_null($this->_applicationHelper)) {
            $this->_applicationHelper = new Mage_Selenium_Helper_Application($this);
        }
        return $this->_applicationHelper;
    }

    /**
     * Performs retrieving of UIMap helper instance
     *
     * @return Mage_Selenium_Helper_Uimap
     */
    public function getUimapHelper()
    {
        if (is_null($this->_uimapHelper)) {
            $this->_uimapHelper = new Mage_Selenium_Helper_Uimap($this);
        }
        return $this->_uimapHelper;
    }

    /**
     * Initializes and loads configuration data
     *
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _initConfig()
    {
        $this->_loadConfigData();
        return $this;
    }

    /**
     * Initializes test data from default location
     *
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _initTestData()
    {
        $this->_loadTestData();
        return $this;
    }

    /**
     * Initializes all driver connections from configuration
     *
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _initDrivers()
    {
        $connections = $this->getConfigValue('browsers');
        foreach ($connections as $connection => $config) {
            $this->_addDriverConnection($config);
        }
        return $this;
    }

    /**
     * Initializes new driver connection with specific configuration
     *
     * @param array $connectionConfig Array of configuration data to start driver's connection
     *
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _addDriverConnection(array $connectionConfig)
    {
        $driver = new Mage_Selenium_Driver();
        $driver->setBrowser($connectionConfig['browser']);
        $driver->setHost($connectionConfig['host']);
        $driver->setPort($connectionConfig['port']);
        $driver->setContiguousSession(true);
        $this->_drivers[] = $driver;
        // @TODO implement interations outside
        $this->driver = $this->_drivers[0];
        return $this;
    }

    /**
     * Performs retrieving of value from Configuration
     *
     * @param string $path - XPath-like path to config value (by default = '')
     *
     * @return array
     */
    public function getConfigValue($path = '')
    {
        return $this->_descend($this->_configData, $path);
    }

    /**
     * Performs retrieving of value from DataSet by path
     *
     * @param string $path XPath-like path to DataSet value (by default = '')
     *
     * @return array|string
     */
    public function getDataValue($path = '')
    {
        return $this->_descend($this->_testData, $path);
    }

    /**
     * Get node|value by path
     *
     * @param array  $data Array of Configuration|DataSet data
     * @param string $path XPath-like path to Configuration|DataSet value
     *
     * @return array|string
     */
    protected function _descend($data, $path)
    {
        $pathArr = (!empty($path)) ? explode('/', $path) : '';
        $currNode = $data;
        if (!empty($pathArr)) {
            foreach ($pathArr as $node) {
                if (isset($currNode[$node])) {
                    $currNode = $currNode[$node];
                } else {
                    return false;
                }
            }
        }
        return $currNode;
    }

    /**
     * Performs loading and merging of DataSet files
     *
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _loadTestData()
    {
        $files = SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'data'
                . DIRECTORY_SEPARATOR . '*.yml';
        $this->_testData = $this->getFileHelper()->loadYamlFiles($files);
        return $this;
    }

    /**
     * Performs loading of Configuration files
     *
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _loadConfigData()
    {
        $files = array(
            'browsers.yml',
            'local.yml'
        );
        $configDir = SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
        foreach ($files as $file) {
            $fileData = $this->getFileHelper()->loadYamlFile($configDir . $file);
            if ($fileData) {
                $this->_configData = array_replace_recursive($this->_configData, $fileData);
            }
        }
        return $this;
    }

}
