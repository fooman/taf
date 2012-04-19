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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * An extended test case implementation that add useful helper methods
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_TestCase extends PHPUnit_Extensions_SeleniumTestCase
{
    /**
     * Testcase error
     * @var boolean
     */
    protected $_error = false;

    /**
     * Configuration object instance
     * @var Mage_Selenium_TestConfiguration
     */
    protected $_testConfig = null;

    /**
     * Data helper instance
     * @var Mage_Selenium_Helper_Data
     */
    protected $_dataHelper = null;

    /**
     * Application helper instance
     * @var Mage_Selenium_Helper_Application
     */
    protected $_applicationHelper = null;

    /**
     * UIMap helper instance
     * @var Mage_Selenium_Helper_Uimap
     */
    protected $_uimapHelper = null;

    /**
     * Page helper instance
     * @var Mage_Selenium_Helper_Page
     */
    protected $_pageHelper = null;

    /**
     * Parameters helper instance
     * @var Mage_Selenium_Helper_Params
     */
    protected $_paramsHelper = null;

    /**
     * @var array
     */
    protected $_testHelpers = array();

    /**
     * Array of instanced helpers
     * @var array
     */
    protected $_helpers = array();

    /**
     * Data generator helper instance
     * @var Mage_Selenium_Helper_DataGenerator
     */
    protected $_dataGenerator = null;

    /**
     * Error and success messages on page
     * @var array
     */
    protected static $_messages = null;

    /**
     * Timeout const
     * @var int
     */
    protected $_browserTimeoutPeriod = 40000;

    /*
     * @var string
     */
    protected $_firstPageAfterAdminLogin = 'dashboard';

    /**
     * The name of the test case.
     * @var string
     */
    protected $name = null;

    /**
     * @var    array
     */
    protected $data = array();

    /**
     * @var PHPUnit_Framework_TestResult
     */
    protected $result;

    /**
     * @var array
     */
    protected $dependencies = array();

    /**
     * Whether or not this test is running in a separate PHP process.
     * @var boolean
     */
    protected $inIsolation = false;

    /**
     * The name of the expected Exception.
     * @var mixed
     */
    protected $expectedException = null;

    /**
     * The message of the expected Exception.
     * @var string
     */
    protected $expectedExceptionMessage = '';

    /**
     * The code of the expected Exception.
     * @var integer
     */
    protected $expectedExceptionCode;

    /**
     * @var array
     */
    protected $dependencyInput = array();

    protected $captureScreenshotOnFailure = true;
    protected $screenshotPath = SELENIUM_TESTS_SCREENSHOTDIR;
    protected $screenshotUrl = SELENIUM_TESTS_SCREENSHOTDIR;

    /**
     * Success message Xpath
     * @staticvar string
     */
    protected static $xpathSuccessMessage = "//*/descendant::*[normalize-space(@class)='success-msg'][string-length(.)>1]";

    /**
     * Error message Xpath
     * @staticvar string
     */
    protected static $xpathErrorMessage = "//*/descendant::*[normalize-space(@class)='error-msg'][string-length(.)>1]";

    /**
     * Notice message Xpath
     * @staticvar string
     */
    protected static $xpathNoticeMessage = "//*/descendant::*[normalize-space(@class)='notice-msg'][string-length(.)>1]";

    /**
     * Error message Xpath
     * @staticvar string
     */
    protected static $xpathValidationMessage = "//*/descendant::*[normalize-space(@class)='validation-advice' and not(contains(@style,'display: none;'))][string-length(.)>1]";

    /**
     * Field Name xpath with ValidationMessage
     * @staticvar string
     */
    protected static $xpathFieldNameWithValidationMessage = "/ancestor::*[2]//label/descendant-or-self::*[string-length(text())>1]";

    /**
     * Loading holder XPath
     * @staticvar string
     */
    protected static $xpathLoadingHolder = "//div[@id='loading-mask' and not(contains(@style,'display: none'))]";

    /**
     * Log Out link
     * @staticvar string
     */
    protected static $xpathLogOutAdmin = "//div[@class='header-right']//a[@class='link-logout']";

    /**
     * Admin Logo Xpath
     * @staticvar string
     */
    protected static $xpathAdminLogo = "//img[@class='logo' and contains(@src,'logo.gif')]";

    /**
     * Incoming Message Close button Xpath
     * @staticvar string
     */
    protected static $xpathIncomingMessageClose = "//*[@id='message-popup-window' and @class='message-popup show']//a[span='close']";

    /**
     * 'Go to notifications' xpath in 'Latest Message' block
     * @staticvar string
     */
    protected static $xpathGoToNotifications = "//a[text()='Go to notifications']";

    /**
     * 'Cache Management' xpath link when cache are invalided
     * @staticvar string
     */
    protected static $xpathCacheInvalidated = "//a[text()='Cache Management']";

    /**
     * 'Index Management' xpath link when indexes are invalided
     * @staticvar string
     */
    protected static $xpathIndexesInvalidated = "//a[text()='Index Management']";

    /**
     * Qty elements in Table
     * @staticvar string
     */
    protected static $qtyElementsInTable = "//td[@class='pager']//span[contains(@id,'total-count')]";

    /**
     * @var string
     */
    const FIELD_TYPE_MULTISELECT = 'multiselect';

    /**
     * @var string
     */
    const FIELD_TYPE_DROPDOWN = 'dropdown';

    /**
     * @var string
     */
    const FIELD_TYPE_CHECKBOX = 'checkbox';

    /**
     * @var string
     */
    const FIELD_TYPE_RADIOBUTTON = 'radiobutton';

    /**
     * @var string
     */
    const FIELD_TYPE_INPUT = 'field';

    /**
     * URL of script which performs code coverage during testing
     *
     * @var string
     */
    protected $coverageScriptUrl = null;

    /**
     * Constructs a test case with the given name and browser to test execution
     *
     * @param  string $name Test case name (by default = null)
     * @param  array  $data Test case data array (PHPUnit ONLY) (by default = array())
     * @param  string $dataName Name of Data set (PHPUnit ONLY) (by default = '')
     * @param  array  $browser Array of browser configuration settings: 'name', 'browser', 'host', 'port', 'timeout',
     * 'httpTimeout' (by default = array())
     *
     * @throws InvalidArgumentException
     */
    public function __construct($name = null, array $data = array(), $dataName = '', array $browser = array())
    {
        $this->_testConfig = Mage_Selenium_TestConfiguration::getInstance();
        $this->_dataHelper = $this->_testConfig->getDataHelper();
        $this->_dataGenerator = $this->_testConfig->getDataGenerator();
        $this->_applicationHelper = $this->_testConfig->getApplicationHelper();
        $this->_pageHelper = $this->_testConfig->getPageHelper($this, $this->_applicationHelper);
        $this->_uimapHelper = $this->_testConfig->getUimapHelper();

        if ($name !== null) {
            $this->name = $name;
        }
        $this->data = $data;
        $this->dataName = $dataName;

        $path = 'browsers/default/browserTimeoutPeriod';
        $this->_browserTimeoutPeriod = (!is_bool($this->_testConfig->getConfigValue($path)))
            ? $this->_testConfig->getConfigValue($path)
            : $this->_browserTimeoutPeriod;
        parent::__construct($name, $data, $dataName, $browser);
    }

    /**
     * Delegate method calls to the driver. Overridden to load test helpers
     *
     * @param string $command    Command (method) name to call
     * @param array  $arguments  Arguments to be sent to the called command (method)
     *
     * @return mixed
     */
    public function __call($command, $arguments)
    {
        $helper = substr($command, 0, strpos($command, 'Helper'));
        if ($helper) {
            $helper = $this->_loadHelper($helper);
            if ($helper) {
                return $helper;
            }
        }
        return parent::__call($command, $arguments);
    }

    /**
     * Loads specific driver for specified browser
     *
     * @param   array $browser Defines what kind of driver, for a what browser will be loaded
     *
     * @since   Method available since Release 3.3.0
     * @return  PHPUnit_Extensions_SeleniumTestCase_Driver
     */
    protected function getDriver(array $browser)
    {
        $driver = $this->_testConfig->driver;
        $driver->setTestCase($this);
        $driver->setTestId($this->testId);
        // @TODO we need separate driver connections if admin url doesn't start with frontend url
        $driver->setBrowserUrl($this->_applicationHelper->getBaseUrl());
        $driver->start();
        $this->drivers[] = $driver;
        return $driver;
    }

    /**
     * Implementation of setUpBeforeClass() method in the object context, called as setUpBeforeTests()<br>
     * Used ONLY one time before execution of each class (tests in test class)
     * @staticvar boolean $_isFirst Internal variable, which described usage count of this one method
     */
    public function setUp()
    {
        static $_isFirst = true;

        // Clear messages before running test
        $this->clearMessages();

        if ($_isFirst) {
            if (strcmp($this->_testConfig->driver->getBrowser(), '*iexplore') === 0) {
                $this->useXpathLibrary('javascript-xpath');
                $this->allowNativeXpath(true);
            }
            $this->setUpBeforeTests();
            $_isFirst = false;
        }
    }

    /**
     * Function is called before all tests in a test class
     * and can be used for some precondition(s) for all tests
     */
    public function setUpBeforeTests()
    {

    }

    /**
     * This method is called when a test method did not execute successfully.
     *
     * @param Exception $e
     */
    protected function onNotSuccessfulTest(Exception $e)
    {
        $this->testId = implode('_', array(get_class($this), $this->getName(), $this->testId));
        $this->saveHtmlPage();
        parent::onNotSuccessfulTest($e);
    }

    /**
     * Checks if there was error during last operations
     * @TODO need to check this feature
     * @return boolean
     */
    public function hasError()
    {
        return $this->_error;
    }

    /**
     * Access/load helpers from the tests. Helper class name should be like "TestScope_HelperName"
     *
     * @param   string $testScope   Part of the helper class name which refers to the file with the needed helper
     * @param   string $helperName  Suffix that describes helper name (default = 'Helper')
     *
     * @throws UnexpectedValueException
     * @return  mixed Object of $helperName type
     */
    protected function _loadHelper($testScope, $helperName = 'Helper')
    {
        if (empty($testScope) || empty($helperName)) {
            throw new UnexpectedValueException('Helper name can\'t be empty');
        }

        $helperClassName = $testScope . '_' . $helperName;

        if (!isset($this->_testHelpers[$helperClassName])) {
            if (class_exists($helperClassName)) {
                $this->_testHelpers[$helperClassName] = new $helperClassName;
            } else {
                return false;
            }
        }

        if ($this->_testHelpers[$helperClassName] instanceof Mage_Selenium_TestCase) {
            $this->_testHelpers[$helperClassName]->appendParamsDecorator($this->getParamsDecorator());
        }

        return $this->_testHelpers[$helperClassName];
    }

    /**
     * Retrieve instance of helper
     *
     * @param  string $className
     *
     * @return Mage_Selenium_TestCase
     */
    public function helper($className)
    {
        $className = str_replace('/', '_', $className);
        if (strpos($className, '_Helper') === false) {
            $className .= '_Helper';
        }

        if (!isset($this->_helpers[$className])) {
            if (class_exists($className)) {
                $this->_helpers[$className] = new $className;
            } else {
                return false;
            }
        }

        if ($this->_helpers[$className] instanceof Mage_Selenium_TestCase) {
            $this->_helpers[$className]->appendParamsDecorator($this->getParamsDecorator());
        }

        return $this->_helpers[$className];
    }

    ################################################################################
    #                                                                              #
    #                               Assertions Methods                             #
    #                                                                              #
    ################################################################################
    /**
     * Asserts that $condition is true. Reports an error $message if $condition is false.
     * @static
     *
     * @param boolean $condition Condition to assert
     * @param string $message Message to report if the condition is false (by default = '')
     *
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public static function assertTrue($condition, $message = '')
    {
        $message = self::messagesToString($message);

        if (is_object($condition)) {
            $condition = (false === $condition->hasError());
        }

        self::assertThat($condition, self::isTrue(), $message);
    }

    /**
     * Asserts that $condition is false. Reports an error $message if $condition is true.
     * @static
     *
     * @param boolean $condition Condition to assert
     * @param string $message Message to report if the condition is true (by default = '')
     *
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public static function assertFalse($condition, $message = '')
    {
        $message = self::messagesToString($message);

        if (is_object($condition)) {
            $condition = (false === $condition->hasError());
        }

        self::assertThat($condition, self::isFalse(), $message);
    }

    ################################################################################
    #                                                                              #
    #                            Parameter helper methods                          #
    #                                                                              #
    ################################################################################
    /**
     * Append parameters decorator object
     *
     * @param Mage_Selenium_Helper_Params $paramsHelperObject Parameters decorator object
     *
     * @return Mage_Selenium_TestCase
     */
    public function appendParamsDecorator($paramsHelperObject)
    {
        $this->_paramsHelper = $paramsHelperObject;

        return $this;
    }

    /**
     * Retrieve instance of params helper
     * @return Mage_Selenium_Helper_Params
     */
    public function getParamsDecorator()
    {
        if (null === $this->_paramsHelper) {
            $this->_paramsHelper = new Mage_Selenium_Helper_Params();
        }
        return $this->_paramsHelper;
    }

    /**
     * Set parameter to decorator object instance
     *
     * @param   string $name   Parameter name
     * @param   string $value  Parameter value (null to unset)
     *
     * @return  Mage_Selenium_Helper_Params
     */
    public function addParameter($name, $value)
    {
        return $this->getParamsDecorator()->setParameter($name, $value);
    }

    /**
     * Define parameter %$paramName% from URL
     *
     * @param string $paramName
     * @param string|null $url
     *
     * @return integer|null
     */
    public function defineParameterFromUrl($paramName, $url = null)
    {
        if (is_null($url)) {
            $url = $this->getLocation();
        }
        $title_arr = explode('/', $url);
        $title_arr = array_reverse($title_arr);
        foreach ($title_arr as $key => $value) {
            if (preg_match("#$paramName$#i", $value) && isset($title_arr[$key - 1])) {
                return $title_arr[$key - 1];
            }
        }
        return null;
    }

    /**
     * Define parameter %id% from XPath Title
     *
     * @param string $xpathTR XPath of control with 'title' attribute to retrieve an ID
     *
     * @return integer|null
     */
    public function defineIdFromTitle($xpathTR)
    {
        $title = $this->getValue($xpathTR . '/@title');
        if (is_numeric($title)) {
            return $title;
        }

        return $this->defineIdFromUrl($title);
    }

    /**
     * Define parameter %id% from URL
     *
     * @param string|null $url
     *
     * @return integer|null
     */
    public function defineIdFromUrl($url = null)
    {
        return $this->defineParameterFromUrl('id', $url);
    }

    /**
     * Adds field ID to Message Xpath (sets %fieldId% parameter)
     *
     * @param string $fieldType Field type
     * @param string $fieldName Field name from UIMap
     */
    public function addFieldIdToMessage($fieldType, $fieldName)
    {
        $fieldXpath = $this->_getControlXpath($fieldType, $fieldName);
        if ($this->isElementPresent($fieldXpath . '/@id')) {
            $fieldId = $this->getAttribute($fieldXpath . '/@id');
            $fieldId = empty($fieldId) ? $this->getAttribute($fieldXpath . '/@name') : $fieldId;
        } else {
            $fieldId = $this->getAttribute($fieldXpath . '/@name');
        }
        $this->addParameter('fieldId', $fieldId);
    }

    ################################################################################
    #                                                                              #
    #                               Data helper methods                            #
    #                                                                              #
    ################################################################################
    /**
     * Gets node | value from DataSet by the specified path to data source
     *
     * @param string $path Path to data source (e.g. filename in ../data without .yml extension) (by default = '')
     *
     * @return array|string
     */
    protected function _getData($path = '')
    {
        return $this->_testConfig->getDataValue($path);
    }

    /**
     * Loads test data from DataSet, specified in the $dataSource
     *
     * @param string|array $dataSource Data source (e.g. filename in ../data without .yml extension)
     * @param array|null $override Value to override in original data from data source
     * @param string|array|null $randomize Value to randomize
     *
     * @return array
     */
    public function loadData($dataSource, $override = null, $randomize = null)
    {
        $data = $this->_getData($dataSource);

        if (!is_array($data)) {
            $this->fail('Data \'' . $dataSource . '\' is not loaded');
        }

        array_walk_recursive($data, array($this, 'setDataParams'));

        if (!empty($randomize)) {
            $randomize = (!is_array($randomize)) ? array($randomize) : $randomize;
            array_walk_recursive($data, array($this, 'randomizeData'), $randomize);
        }

        if (!empty($override) && is_array($override)) {
            $withSubArray = array();
            $withOutSubArray = array();
            foreach ($override as $key => $value) {
                if (preg_match('|/|', $key)) {
                    $withSubArray[$key]['subArray'] = preg_replace('#/[a-z0-9_]+$#i', '', $key);
                    $withSubArray[$key]['name'] = preg_replace('#^[a-z0-9_]+/#i', '', $key);
                    $withSubArray[$key]['value'] = $value;
                } else {
                    $withOutSubArray[$key] = $value;
                }
            }
            foreach ($withOutSubArray as $key => $value) {
                if (!$this->overrideData($key, $value, $data)) {
                    $data[$key] = $value;
                }
            }
            foreach ($withSubArray as $value) {
                if (!$this->overrideDataInSubArray($value['subArray'], $value['name'], $value['value'], $data)) {
                    $data[$value['subArray']][$value['name']] = $value['value'];
                }
            }
        }

        return $data;
    }

    /**
     * Generates random value as a string|text|email $type, with specified $length.<br>
     * Available $modifier:
     * <li>if $type = string - alnum|alpha|digit|lower|upper|punct
     * <li>if $type = text - alnum|alpha|digit|lower|upper|punct
     * <li>if $type = email - valid|invalid
     *
     * @param string $type Available types are 'string', 'text', 'email' (by default = 'string')
     * @param integer $length Generated value length (by default = 100)
     * @param string|array|null $modifier Value modifier, e.g. PCRE class (by default = null)
     * @param string|null $prefix Prefix to prepend the generated value (by default = null)
     *
     * @return mixed
     */
    public function generate($type = 'string', $length = 100, $modifier = null, $prefix = null)
    {
        $result = $this->_dataGenerator->generate($type, $length, $modifier, $prefix);
        return $result;
    }

    /**
     * Remove array elements that have '%noValue%' value
     *
     * @param array $array  Array of data for cleaning
     *
     * @return array|false
     */
    public function arrayEmptyClear($array)
    {
        if (!is_array($array)) {
            return false;
        }

        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $array[$k] = $this->arrayEmptyClear($v);
                if (count($array[$k]) == false) {
                    unset($array[$k]);
                }
            } else {
                if ($v === '%noValue%') {
                    unset($array[$k]);
                }
            }
        }

        return $array;
    }

    /**
     * Override data with index $key on-fly in the $overrideArray by new value (&$value)
     *
     * @param string $overrideKey Index of the target to override
     * @param string $overrideValue Value for override
     * @param array $overrideArray Target array, which contains some index(es) to override
     *
     * @return boolean
     */
    public function overrideData($overrideKey, $overrideValue, &$overrideArray)
    {
        $overrideResult = false;
        foreach ($overrideArray as $key => &$value) {
            if ($key === $overrideKey) {
                $overrideArray[$key] = $overrideValue;
                $overrideResult = true;
            } elseif (is_array($value)) {
                $result = $this->overrideData($overrideKey, $overrideValue, $value);
                if ($result || $overrideResult) {
                    $overrideResult = true;
                }
            }
        }

        return $overrideResult;
    }

    /**
     * @param string $subArray
     * @param string $overrideKey
     * @param string|array $overrideValue
     * @param array $overrideArray
     *
     * @return boolean
     */
    public function overrideDataInSubArray($subArray, $overrideKey, $overrideValue, &$overrideArray)
    {
        $overrideResult = false;
        foreach ($overrideArray as $key => &$value) {
            if (is_array($value)) {
                if ($key === $subArray) {
                    foreach ($value as $k => $v) {
                        if ($k === $overrideKey) {
                            $value[$k] = $overrideValue;
                            $overrideResult = true;
                        }
                        if (is_array($v)) {
                            $result = $this->overrideDataInSubArray($subArray, $overrideKey, $overrideValue, $value);
                            if ($result || $overrideResult) {
                                $overrideResult = true;
                            }
                        }
                    }
                } else {
                    $result = $this->overrideDataInSubArray($subArray, $overrideKey, $overrideValue, $value);
                    if ($result || $overrideResult) {
                        $overrideResult = true;
                    }
                }
            }
        }
        return $overrideResult;
    }

    /**
     * Randomize data with index $key on-fly in the $randomizeArray by new value (&$value)
     *
     * @param string $value Value for randomization (in this case - value will be as a suffix)
     * @param string $key Index of the target to randomize
     * @param array $randomizeArray Target array, which contains some index(es) to randomize
     */
    public function randomizeData(&$value, $key, $randomizeArray)
    {
        foreach ($randomizeArray as $randomizeField) {
            if ($randomizeField === $key) {
                $value = $this->generate('string', 5, ':lower:') . '_' . $value;
            }
        }
    }

    /**
     * Set data params
     *
     * @param string $value
     * @param string $key Index of the target to randomize
     */
    public function setDataParams(&$value, $key)
    {
        if (preg_match('/%randomize%/', $value)) {
            $value = preg_replace('/%randomize%/', $this->generate('string', 5, ':lower:'), $value);
        }
        if (preg_match('/^%longValue[0-9]+%$/', $value)) {
            $length = preg_replace('/[^0-9]/', '', $value);
            $value = preg_replace('/%longValue[0-9]+%/', $this->generate('string', $length, ':alpha:'), $value);
        }
        if (preg_match('/^%specialValue[0-9]+%$/', $value)) {
            $length = preg_replace('/[^0-9]/', '', $value);
            $value = preg_replace('/%specialValue[0-9]+%/', $this->generate('string', $length, ':punct:'), $value);
        }
        if (preg_match('/%currentDate%/', $value)) {
            $value = preg_replace('/%currentDate%/', date("n/j/y"), $value);
        }
    }

    ################################################################################
    #                                                                              #
    #                               Messages helper methods                        #
    #                                                                              #
    ################################################################################
    /**
     * Removes all added messages
     *
     * @param null|string $type
     */
    public function clearMessages($type = null)
    {
        if ($type && array_key_exists($type, Mage_Selenium_TestCase::$_messages)) {
            unset(Mage_Selenium_TestCase::$_messages[$type]);
        } elseif ($type == null) {
            Mage_Selenium_TestCase::$_messages = null;
        }
    }

    /**
     * Gets all messages on the page
     */
    protected function _parseMessages()
    {
        Mage_Selenium_TestCase::$_messages['success'] = $this->getElementsByXpath(self::$xpathSuccessMessage);
        Mage_Selenium_TestCase::$_messages['error'] = $this->getElementsByXpath(self::$xpathErrorMessage);
        Mage_Selenium_TestCase::$_messages['validation'] = $this->getElementsByXpath(self::$xpathValidationMessage,
                                                                                     'text',
                                                                                     self::$xpathFieldNameWithValidationMessage);
    }

    /**
     * Returns all messages (or messages of the specified type) on the page
     *
     * @param null|string $type Message type: validation|error|success
     *
     * @return array
     */
    public function getMessagesOnPage($type = null)
    {
        $this->_parseMessages();
        if ($type) {
            return Mage_Selenium_TestCase::$_messages[$type];
        }

        return Mage_Selenium_TestCase::$_messages;
    }

    /**
     * Returns all parsed messages (or messages of the specified type)
     *
     * @param null|string $type Message type: validation|error|success (default = null, for all messages)
     *
     * @return array|null
     */
    public function getParsedMessages($type = null)
    {
        if ($type) {
            return (isset(Mage_Selenium_TestCase::$_messages[$type]))
                ? Mage_Selenium_TestCase::$_messages[$type]
                : null;
        }
        return Mage_Selenium_TestCase::$_messages;
    }

    /**
     * Adds validation|error|success message(s)
     *
     * @param string $type Message type: validation|error|success
     * @param string|array $message Message text
     */
    public function addMessage($type, $message)
    {
        if (is_array($message)) {
            foreach ($message as $value) {
                Mage_Selenium_TestCase::$_messages[$type][] = $value;
            }
        } else {
            Mage_Selenium_TestCase::$_messages[$type][] = $message;
        }
    }

    /**
     * Adds a verification message
     *
     * @param string|array $message Message text
     */
    public function addVerificationMessage($message)
    {
        $this->addMessage('verification', $message);
    }

    /**
     * Verifies messages count
     *
     * @param integer $count Expected number of message(s) on the page
     * @param string $xpath XPath of a message(s) that should be evaluated (default = null)
     *
     * @return integer Number of nodes that match the specified $xpath
     */
    public function verifyMessagesCount($count = 1, $xpath = null)
    {
        if ($xpath === null) {
            $xpath = self::$xpathValidationMessage;
        }
        $this->_parseMessages();
        return $this->getXpathCount($xpath) == $count;
    }

    /**
     * Check if the specified message exists on the page
     *
     * @param string $message  Message ID from UIMap
     *
     * @return boolean
     */
    public function checkMessage($message)
    {
        $page = $this->getCurrentUimapPage();
        try {
            $messageLocator = $page->findMessage($message);
        } catch (Exception $e) {
            $errorMessage = 'Current location url: ' . $this->getLocation() . "\n"
                . 'Current page "' . $this->getCurrentPage() . '": '
                . $e->getMessage() . ' - "' . $message . '"' . "\n"
                . implode("\n", call_user_func_array('array_merge', $this->getMessagesOnPage()));
            $this->fail($errorMessage);
        }
        return $this->checkMessageByXpath($messageLocator);
    }

    /**
     * Checks if  message with the specified XPath exists on the page
     *
     * @param string $xpath XPath of message to checking
     *
     * @return boolean
     */
    public function checkMessageByXpath($xpath)
    {
        $this->_parseMessages();
        if ($xpath && $this->isElementPresent($xpath)) {
            return array("success" => true);
        }
        return array("success" => false, "xpath" => $xpath, "found" => self::messagesToString($this->getMessagesOnPage()));
    }

    /**
     * Checks if any 'error' message exists on the page
     *
     * @param string $message Error message ID from UIMap OR XPath of the error message (by default = null)
     *
     * @return boolean
     */
    public function errorMessage($message = null)
    {
        return (!empty($message))
            ? $this->checkMessage($message)
            : $this->checkMessageByXpath(self::$xpathErrorMessage);
    }

    /**
     * Checks if any 'success' message exists on the page
     *
     * @param string $message Success message ID from UIMap OR XPath of the success message (by default = null)
     *
     * @return boolean
     */
    public function successMessage($message = null)
    {
        return (!empty($message))
            ? $this->checkMessage($message)
            : $this->checkMessageByXpath(self::$xpathSuccessMessage);
    }

    /**
     * Checks if any 'validation' message exists on the page
     *
     * @param string $message Validation message ID from UIMap OR XPath of the validation message (by default = null)
     *
     * @return boolean
     */
    public function validationMessage($message = null)
    {
        return (!empty($message))
            ? $this->checkMessage($message)
            : $this->checkMessageByXpath(self::$xpathValidationMessage);
    }

    /**
     * Asserts that the specified message of the specified type is present on the current page
     *
     * @param string $type    success|validation|error
     * @param string $message Message text
     */
    public function assertMessagePresent($type, $message = null)
    {
        $method = strtolower($type) . 'Message';
        $result = $this->$method($message);
        if ($result["success"]) {
            $this->assertTrue(True);
        } else {
            $this->fail("Failed looking for '" . $result["xpath"] . "', found '" . $result["found"] . "' instead");
        }
    }

    /**
     * Assert there are no verification errors
     * @throws PHPUnit_Framework_ExpectationFailedException
     */
    public function assertEmptyVerificationErrors()
    {
        $verificationErrors = $this->getParsedMessages('verification');
        if ($verificationErrors) {
            $this->fail(implode("\n", $verificationErrors));
        }
    }

    ################################################################################
    #                                                                              #
    #                               Navigation helper methods                      #
    #                                                                              #
    ################################################################################
    /**
     * Navigates to the specified page in specified area.<br>
     * Page identifier must be described in the UIMap.
     *
     * @param string $area Area identifier (by default = 'frontend')
     * @param string $page Page identifier (by default = 'home')
     * @param boolean $validatePage
     *
     * @return Mage_Selenium_TestCase
     */
    public function goToArea($area = 'frontend', $page = 'home', $validatePage = true)
    {
        $this->setArea($area);
        $this->navigate($page, $validatePage);
        return $this;
    }

    /**
     * Navigates to the specified page in the current area.<br>
     * Page identifier must be described in the UIMap.
     *
     * @param string $page Page identifier
     * @param boolean $validatePage
     *
     * @return Mage_Selenium_TestCase
     */
    public function navigate($page, $validatePage = true)
    {
        $area = $this->getArea();
        try {
            $clickXpath = $this->getPageClickXpath($area, $page);

            if ($clickXpath && $this->isElementPresent($clickXpath)) {
                $this->click($clickXpath);
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
            } else {
                $this->open($this->getPageUrl($area, $page));
            }
            if ($validatePage) {
                $this->validatePage($page);
            }
        } catch (PHPUnit_Framework_Exception $e) {
            $this->_error = true;
        }

        return $this;
    }

    /**
     * Navigate to the specified admin page.<br>
     * Page identifier must be described in the UIMap. Opens "Dashboard" page by default.
     *
     * @param string $page Page identifier (by default = 'dashboard')
     * @param boolean $validatePage
     *
     * @return Mage_Selenium_TestCase
     */
    public function admin($page = 'dashboard', $validatePage = true)
    {
        $this->goToArea('admin', $page, $validatePage);
        return $this;
    }

    /**
     * Navigate to the specified frontend page<br>
     * Page identifier must be described in the UIMap. Opens "Home page" by default.
     *
     * @param string $page Page identifier (by default = 'home')
     * @param boolean $validatePage
     *
     * @return Mage_Selenium_TestCase
     */
    public function frontend($page = 'home', $validatePage = true)
    {
        $this->goToArea('frontend', $page, $validatePage);
        return $this;
    }

    ################################################################################
    #                                                                              #
    #                                Area helper methods                           #
    #                                                                              #
    ################################################################################
    /**
     * Gets current area<br>
     * Usage: to definition of area what operates in this time.
     * <li>Possible areas: frontend | admin
     * @return string
     */
    public function getArea()
    {
        return $this->_applicationHelper->getArea();
    }

    /**
     * Gets current location area<br>
     * Usage: to definition of area what operates in this time.
     * <li>Possible areas: frontend | admin
     * @return string
     */
    public function getCurrentLocationArea()
    {
        $currentArea = Mage_Selenium_TestCase::_getAreaFromCurrentUrl($this->_applicationHelper->getAreasConfig(),
                                                                      $this->getLocation());
        $this->setArea($currentArea);
        return $currentArea;
    }

    /**
     * Find area in areasConfig using full page URL
     * @static
     *
     * @param string $currentUrl Full URL to page
     * @param array $areasConfig Full area config
     *
     * @return string
     */
    protected static function _getAreaFromCurrentUrl($areasConfig, $currentUrl)
    {
        $currentArea = '';
        $currentUrl = preg_replace('|^http([s]{0,1})://|', '', preg_replace('|/index.php/?|', '/', $currentUrl));

        foreach ($areasConfig as $area => $areaConfig) {
            $areaUrl = preg_replace('|^http([s]{0,1})://|', '',
                                    preg_replace('|/index.php/?|', '/', $areaConfig['url']));
            if (strpos($currentUrl, $areaUrl) === 0) {
                $currentArea = $area;
                break;
            }
        }

        return $currentArea;
    }

    /**
     * Sets current area<br>
     * Usage: to setup area that will be used further
     * <li>Possible areas: frontend | admin
     *
     * @param string $area Area identifier ('admin'|'frontend')
     *
     * @return Mage_Selenium_TestCase
     */
    public function setArea($area)
    {
        $this->_applicationHelper->setArea($area);
        return $this;
    }

    ################################################################################
    #                                                                              #
    #                       UIMap of Page helper methods                           #
    #                                                                              #
    ################################################################################
    /**
     * Retrieves Page's data from UIMap by $pageKey
     *
     * @param string $area Area identifier ('admin'|'frontend')
     * @param string $pageKey UIMap page key
     *
     * @return Mage_Selenium_Uimap_Page
     */
    public function getUimapPage($area, $pageKey)
    {
        $page = $this->_uimapHelper->getUimapPage($area, $pageKey, $this->getParamsDecorator());

        if (!$page) {
            $this->fail('Can\'t find page "' . $pageKey . '" in area "' . $area . '"');
        }

        return $page;
    }

    /**
     * Retrieves current Page data from UIMap.
     * Gets current page name from an internal variable.
     * @return Mage_Selenium_Uimap_Page|null
     */
    public function getCurrentUimapPage()
    {
        return $this->getUimapPage($this->getArea(), $this->getCurrentPage());
    }

    /**
     * Retrieves current Page data from UIMap.
     * Gets current page name from the current URL.
     * @return Mage_Selenium_Uimap_Page|null
     */
    public function getCurrentLocationUimapPage()
    {
        $areasConfig = $this->_applicationHelper->getAreasConfig();
        $currentUrl = $this->getLocation();
        $mca = Mage_Selenium_TestCase::_getMcaFromCurrentUrl($areasConfig, $currentUrl);
        $area = Mage_Selenium_TestCase::_getAreaFromCurrentUrl($areasConfig, $currentUrl);
        $page = $this->_uimapHelper->getUimapPageByMca($area, $mca, $this->getParamsDecorator());

        if (!$page) {
            $this->fail('Can\'t find page for mca "' . $mca . '" in area "' . $area . '"');
        }
        return $page;
    }

    ################################################################################
    #                                                                              #
    #                             Page ID helper methods                           #
    #                                                                              #
    ################################################################################
    /**
     * Set PageID
     *
     * @param string $page
     *
     * @return \Mage_Selenium_TestCase
     */
    public function setCurrentPage($page)
    {
        $this->_pageHelper->setCurrentPage($page);
        return $this;
    }

    /**
     * Returns PageID of current page
     * @return string
     */
    public function getCurrentPage()
    {
        return $this->_pageHelper->getCurrentPage();
    }

    /**
     * Find PageID in UIMap in the current area using full page URL
     *
     * @param string  $url Full URL
     *
     * @return string|boolean
     */
    protected function _findCurrentPageFromUrl($url)
    {
        $areasConfig = $this->_applicationHelper->getAreasConfig();

        $mca = Mage_Selenium_TestCase::_getMcaFromCurrentUrl($areasConfig, $url);
        $area = Mage_Selenium_TestCase::_getAreaFromCurrentUrl($areasConfig, $url);
        $page = $this->_pageHelper->getPageByMca($area, $mca, $this->getParamsDecorator());
        if ($page) {
            return $page->getPageId();
        } else {
            $this->fail('Can\'t find page for url: ' . $url);
        }

        return false;
    }

    /**
     * Checks if the currently opened page is $page.<br>
     * Returns true if the specified page is the current page, otherwise returns false and sets the error message:
     * "Opened the wrong page: $currentPage (should be:$page)".<br>
     * Page identifier must be described in the UIMap.
     *
     * @param string $page Page identifier
     *
     * @return boolean
     */
    public function checkCurrentPage($page)
    {
        $currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
        if ($currentPage != $page) {
            $this->addVerificationMessage("Opened the wrong page: '" . $currentPage . "'(should be: '" . $page . "')");
            return false;
        }
        return true;
    }

    /**
     * Validates properties of the current page.
     *
     * @param string $page
     */
    public function validatePage($page = '')
    {
        if ($page) {
            $this->assertTrue($this->checkCurrentPage($page), $this->getParsedMessages());
        }
        if (!$page) {
            $page = $this->_findCurrentPageFromUrl($this->getLocation());
        }
        $this->assertTextNotPresent('Fatal error', 'Fatal error on page');
        $this->assertTextNotPresent('There has been an error processing your request',
                                    'Fatal error on page: \'There has been an error processing your request\'');
        $this->assertTextNotPresent('Notice:', 'Notice error on page');
        $this->assertTextNotPresent('Parse error', 'Parse error on page');
        if (!$this->isElementPresent(self::$xpathNoticeMessage)) {
            $this->assertTextNotPresent('Warning:', 'Warning on page');
        }
        $this->assertTextNotPresent('If you typed the URL directly', 'The requested page was not found.');
        $this->assertTextNotPresent('was not found', 'Something was not found:)');
        $this->assertTextNotPresent('Service Temporarily Unavailable', 'Service Temporarily Unavailable');
        $this->assertTextNotPresent('The page isn\'t redirecting properly', 'The page isn\'t redirecting properly');
        //@TODO
        //$this->assertSame($this->getUimapPage($this->getArea(), $page)->getTitle($this->getParamsDecorator()),
        //                  $this->getTitle(),
        //                  'Page title is unexpected');
        $this->setCurrentPage($page);
    }

    ################################################################################
    #                                                                              #
    #                       Page Elements helper methods                           #
    #                                                                              #
    ################################################################################
    /**
     * Get Module-Controller-Action-part of page URL
     * @static
     *
     * @param array $areasConfig Full area config
     * @param string $currentUrl Current URL
     *
     * @return mixed
     */
    protected static function _getMcaFromCurrentUrl($areasConfig, $currentUrl)
    {
        $mca = '';
        $currentArea = '';
        $baseUrl = '';
        $currentUrl = preg_replace('|^http([s]{0,1})://|', '', preg_replace('|/index.php/?|', '/', $currentUrl));
        foreach ($areasConfig as $area => $areaConfig) {
            $areaUrl = preg_replace('|^http([s]{0,1})://|', '',
                                    preg_replace('|/index.php/?|', '/', $areaConfig['url']));
            if (strpos($currentUrl, $areaUrl) === 0) {
                $baseUrl = $areaUrl;
                $currentArea = $area;
                break;
            }
        }
        if (strpos($currentUrl, $baseUrl) !== false) {
            $mca = trim(substr($currentUrl, strlen($baseUrl)), " /\\");
        }

        if ($mca && $mca[0] != '/') {
            $mca = '/' . $mca;
        }

        if ($currentArea == 'admin') {
            //Removes part of url that appears after pressing "Reset Filter" or "Search" button in grid
            //(when not using ajax to reload the page)
            $mca = preg_replace('|/filter/((\S)+)?/form_key/[A-Za-z0-9]+/?|', '/', $mca);
            //Delete secret key from url
            $mca = preg_replace('|/(index/)?key/[A-Za-z0-9]+/?|', '/', $mca);
            //Delete action part of mca if it's index
            $mca = preg_replace('|/index/?$|', '/', $mca);
        } elseif ($currentArea == 'frontend') {
            //Delete action part of mca if it's index
            $mca = preg_replace('|/index/?$|', '/', $mca);
        }
        return preg_replace('|^/|', '', $mca);
    }

    /**
     * Returns URL of the specified page
     *
     * @param string $area
     * @param string $page Page identifier
     *
     * @return string
     */
    public function getPageUrl($area, $page)
    {
        $pageData = $this->getUimapPage($area, $page);
        $url = $this->_applicationHelper->getBaseUrl() . $pageData->getMca();

        return $url;
    }

    /**
     * Return click xpath of the specified page
     *
     * @param string $area
     * @param string $page Page identifier
     *
     * @return string
     */
    public function getPageClickXpath($area, $page)
    {
        return $this->_pageHelper->getPageClickXpath($area, $page);
    }

    /**
     * Gets XPath of a control with the specified name and type.
     *
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     *
     * @throws OutOfRangeException
     * @return string
     */
    protected function _getControlXpath($controlType, $controlName)
    {
        $uimapPage = $this->getCurrentUimapPage();
        if (!$uimapPage) {
            throw new OutOfRangeException("Can't find specified form in UIMap array '"
                . $this->getLocation() . "', area['" . $this->getArea() . "']");
        }

        $method = 'find' . ucfirst(strtolower($controlType));

        try {
            $xpath = $uimapPage->$method($controlName);
        } catch (Exception $e) {
            $errorMessage = 'Current location url: ' . $this->getLocation() . "\n"
                . 'Current page "' . $this->getCurrentPage() . '": '
                . $e->getMessage() . ' - "' . $controlName . '"';
            $this->fail($errorMessage);
        }

        if (is_object($xpath) && method_exists($xpath, 'getXPath')) {
            $xpath = $xpath->getXPath();
        }

        return $xpath;
    }

    /**
     * Gets map data values to UIPage form
     *
     * @param array $fieldsets Array of fieldsets to fill
     * @param array $data Array of data to fill
     *
     * @return array
     */
    protected function _getFormDataMap($fieldsets, $data)
    {
        $dataMap = array();
        $uimapFields = array();

        foreach ($data as $dataFieldName => $dataFieldValue) {
            if ($dataFieldValue == '%noValue%') {
                continue;
            }
            foreach ($fieldsets as $fieldset) {
                $uimapFields[self::FIELD_TYPE_MULTISELECT] = $fieldset->getAllMultiselects();
                $uimapFields[self::FIELD_TYPE_DROPDOWN] = $fieldset->getAllDropdowns();
                $uimapFields[self::FIELD_TYPE_RADIOBUTTON] = $fieldset->getAllRadiobuttons();
                $uimapFields[self::FIELD_TYPE_CHECKBOX] = $fieldset->getAllCheckboxes();
                $uimapFields[self::FIELD_TYPE_INPUT] = $fieldset->getAllFields();
                foreach ($uimapFields as $fieldsType => $fieldsData) {
                    foreach ($fieldsData as $uimapFieldName => $uimapFieldValue) {
                        if ($dataFieldName == $uimapFieldName) {
                            $parent = $fieldset->getXpath();
                            if (!is_null($parent) && !$parent == '') {
                                $uimapFieldValue = str_ireplace('css=', ' ', $uimapFieldValue);
                            }
                            $dataMap[$dataFieldName] = array(
                                'type'  => $fieldsType,
                                'path'  => $parent . $uimapFieldValue,
                                'value' => $dataFieldValue
                            );
                            break 3;
                        }
                    }
                }
            }
        }

        return $dataMap;
    }

    ################################################################################
    #                                                                              #
    #                           Framework helper methods                           #
    #                                                                              #
    ################################################################################
    /**
     * Saves html content of current page
     *
     * @param null|string $fileName
     */
    public function saveHtmlPage($fileName = null)
    {
        if ($fileName == null) {
            $fileName = $this->testId;
        }
        $file = fopen(SELENIUM_TESTS_SCREENSHOTDIR . DIRECTORY_SEPARATOR . $fileName . '.html', 'a+');
        fputs($file,'<!-- Location = '.$this->_testConfig->driver->getLocation().' -->' . PHP_EOL);
        fputs($file, $this->_testConfig->driver->getHtmlSource());
        fflush($file);
        fclose($file);
    }

    /**
     * Clicks a control with the specified name and type.
     *
     * @param string $controlType Type of control (e.g. button|link|radiobutton|checkbox)
     * @param string $controlName Name of a control from UIMap
     * @param boolean $willChangePage Triggers page reloading. If clicking the control doesn't result<br>
     * in page reloading, should be false (by default = true).
     *
     * @return Mage_Selenium_TestCase
     */
    public function clickControl($controlType, $controlName, $willChangePage = true)
    {
        $xpath = $this->_getControlXpath($controlType, $controlName);

        if (empty($xpath)) {
            $this->fail('Xpath for control "' . $controlName . '" is empty');
        }

        if (!$this->isElementPresent($xpath)) {
            $this->fail('Control "' . $controlName . '" is not present on the page "' . $this->getCurrentPage() . '". '
                            . 'Type: ' . $controlType . ', xpath: ' . $xpath);
        }

        try {
            $this->click($xpath);

            if ($willChangePage) {
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                $this->addParameter('id', $this->defineIdFromUrl());
                $this->validatePage();
            }
        } catch (PHPUnit_Framework_Exception $e) {
            $this->fail($e->getMessage());
        }
        return $this;
    }

    /**
     * Click on button with specified name
     *
     * @param string $button Button's identifier (Name of a button from UIMap)
     * @param boolean $willChangePage Triggers page reloading. If clicking the control doesn't result<br>
     * in page reloading, should be false (by default = true).
     *
     * @return Mage_Selenium_TestCase
     */
    public function clickButton($button, $willChangePage = true)
    {
        $this->clickControl('button', $button, $willChangePage);

        return $this;
    }

    /**
     * Clicks a control with the specified name and type
     * and confirms the confirmation popup with the specified message.
     *
     * @param string $controlType Type of control (e.g. button|link)
     * @param string $controlName Name of a control from UIMap
     * @param string $message Confirmation message
     * @param bool   $willChangePage Triggers page reloading. If clicking the control doesn't result</br>
     * in page reloading, should be false (by default = true).
     *
     * @return boolean
     */
    public function clickControlAndConfirm($controlType, $controlName, $message, $willChangePage = true)
    {
        $buttonXpath = $this->_getControlXpath($controlType, $controlName);
        if ($this->isElementPresent($buttonXpath)) {
            $confirmation = $this->getCurrentUimapPage()->findMessage($message);
            $this->chooseCancelOnNextConfirmation();
            $this->click($buttonXpath);
            if ($this->isConfirmationPresent()) {
                $text = $this->getConfirmation();
                if ($text == $confirmation) {
                    $this->chooseOkOnNextConfirmation();
                    $this->click($buttonXpath);
                    $this->getConfirmation();
                    if ($willChangePage) {
                        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                        $this->validatePage();
                    }
                    return true;
                } else {
                    $this->addVerificationMessage("The confirmation text incorrect: {$text}");
                }
            } else {
                $this->addVerificationMessage('The confirmation does not appear');
                if ($willChangePage) {
                    $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                    $this->validatePage();
                }
                return true;
            }
        } else {
            $this->addVerificationMessage("There is no way to click on control(There is no '$controlName' control)");
        }

        return false;
    }

    /**
     * Submit form and confirms the confirmation popup with the specified message.
     *
     * @param string $buttonName Name of a button from UIMap
     * @param string $message Message ID from UIMap
     * @param bool   $willChangePage Triggers page reloading. If clicking the control doesn't result</br>
     * in page reloading, should be false (by default = true).
     *
     * @return boolean
     */
    public function clickButtonAndConfirm($buttonName, $message, $willChangePage = true)
    {
        return $this->clickControlAndConfirm('button', $buttonName, $message, $willChangePage);
    }

    /**
     * Searches a control with the specified name and type on the page.
     * If the control is present, returns true; otherwise false.
     *
     * @param string $controlType Type of control (e.g. button | link | radiobutton | checkbox)
     * @param string $controlName Name of a control from UIMap
     *
     * @return boolean
     */
    public function controlIsPresent($controlType, $controlName)
    {
        $xpath = $this->_getControlXpath($controlType, $controlName);

        if ($xpath == null) {
            $this->fail("Can't find control: [$controlType: $controlName]");
        }

        if ($this->isElementPresent($xpath)) {
            return true;
        }

        return false;
    }

    /**
     * Searches a button with the specified name on the page.
     * If the button is present, returns true; otherwise false.
     *
     * @param string $button Name of a button from UIMap
     *
     * @return boolean
     */
    public function buttonIsPresent($button)
    {
        return $this->controlIsPresent('button', $button);
    }

    /**
     * Open tab
     *
     * @param string $tabName Tab name as displayed on the page
     */
    public function openTab($tabName)
    {
        $waitAjax = false;
        $tabXpath = $this->_getControlXpath('tab', $tabName);
        if ($this->isElementPresent($tabXpath . '[@class]')) {
            $isTabOpened = $this->getAttribute($tabXpath . '/@class');
        } elseif ($this->isElementPresent($tabXpath . '/parent::*[@class]')) {
            $isTabOpened = $this->getAttribute($tabXpath . '/parent::*/@class');
        } else {
            $this->fail('Wrong xpath for tab');
        }
        if (!preg_match('/active/', $isTabOpened)) {
            if (preg_match('/ajax/', $isTabOpened)) {
                $waitAjax = true;
            }
            $this->clickControl('tab', $tabName, false);
            if ($waitAjax) {
                $this->pleaseWait();
            }
        }
    }

    /**
     * Gets all element(s) by XPath
     *
     * @param string $xpath General XPath of looking up element(s)
     * @param string $get What to get. Allowed params: 'text' or 'value' (by default = 'text')
     * @param string $additionalXPath Additional XPath (by default= '')
     *
     * @return array
     */
    public function getElementsByXpath($xpath, $get = 'text', $additionalXPath = '')
    {
        $elements = array();

        if (!empty($xpath)) {
            $totalElements = $this->getXpathCount($xpath);
            $pos = stripos(trim($xpath), 'css=');
            for ($i = 1; $i < $totalElements + 1; $i++) {
                if ($pos !== false && $pos == 0) {
                    $x = $xpath . ':nth(' . ($i - 1) . ')';
                } else {
                    $x = $xpath . '[' . $i . ']';
                }
                switch ($get) {
                    case 'value' :
                        $element = $this->getValue($x);
                        break;
                    case 'text' :
                        $element = $this->getText($x);
                        break;
                    default :
                        $this->fail('Possible values of the variable $get only "text" and "value"');
                        break;
                }

                if (!empty($element)) {
                    if ($additionalXPath) {
                        if ($this->isElementPresent($x . $additionalXPath)) {
                            $label = trim($this->getText($x . $additionalXPath), " *\t\n\r");
                        } else {
                            $label = $this->getAttribute($x . "@id");
                            $label = strrev($label);
                            $label = strrev(substr($label, 0, strpos($label, "-")));
                        }
                        if ($label) {
                            $element = '"' . $label . '": ' . $element;
                        }
                    }

                    $elements[] = $element;
                }
            }
        }

        return $elements;
    }

    /**
     * Gets an element by XPath
     *
     * @param string $xpath XPath of an element to look up
     * @param string $get What to get. Allowed params: 'text' or 'value' (by default = 'text')
     *
     * @return array
     */
    public function getElementByXpath($xpath, $get = 'text')
    {
        return array_shift($this->getElementsByXpath($xpath, $get));
    }

    /**
     * Returns number of nodes that match the specified CSS selector,
     * eg. "table" would give number of tables.
     *
     * @param string $locator CSS selector
     *
     * @return string
     */
    public function getCssCount($locator)
    {
        $script = "this.browserbot.evaluateCssCount('" . addslashes($locator) . "', this.browserbot.getDocument())";
        return $this->getEval($script);
    }

    /**
     * Returns number of nodes that match the specified xPath selector,
     * eg. "table" would give number of tables.
     *
     * @param string $locator xPath selector
     *
     * @return int|string
     */
    public function getXpathCount($locator)
    {
        $pos = stripos(trim($locator), 'css=');
        if ($pos !== false && $pos == 0) {
            return $this->getCssCount($locator);
        }
        return parent::getXpathCount($locator);
    }

    /**
     * Returns table column names
     *
     * @param string $tableXpath
     *
     * @return array
     */
    public function getTableHeadRowNames($tableXpath = '//table[@id]')
    {
        $xpath = $tableXpath . "//tr[normalize-space(@class)='headings']";
        if (!$this->isElementPresent($xpath)) {
            $this->fail('Incorrect table head xpath: ' . $xpath);
        }

        $cellNum = $this->getXpathCount($xpath . '/th');
        $headNames = array();
        for ($cell = 0; $cell < $cellNum; $cell++) {
            $cellLocator = $tableXpath . '.0.' . $cell;
            $headNames[$cell] = $this->getTable($cellLocator);
        }
        return array_diff($headNames, array(''));
    }

    /**
     * Returns table column ID based on the column name.
     *
     * @param string $columnName
     * @param string $tableXpath
     *
     * @return number
     */
    public function getColumnIdByName($columnName, $tableXpath = '//table[@id]')
    {
        return array_search($columnName, $this->getTableHeadRowNames($tableXpath)) + 1;
    }

    /**
     * Waits for the element to appear
     *
     * @param string|array $locator XPath locator or array of locators
     * @param integer $timeout Timeout period in seconds (by default = 40)
     *
     * @return boolean
     */
    public function waitForElement($locator, $timeout = 40)
    {
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {
            if (is_array($locator)) {
                foreach ($locator as $loc) {
                    if ($this->isElementPresent($loc)) {
                        return true;
                    }
                }
            } else {
                if ($this->isElementPresent($locator)) {
                    return true;
                }
            }
            sleep(1);
        }
        return false;
    }

    /**
     * Waits for the element(s) to be visible
     *
     * @param string|array $locator XPath locator or array of locators
     * @param integer $timeout Timeout period in seconds (by default = 40)
     *
     * @return boolean
     */
    public function waitForElementVisible($locator, $timeout = 40)
    {
        $iStartTime = time();
        while ($timeout > time() - $iStartTime) {
            if (is_array($locator)) {
                foreach ($locator as $loc) {
                    if ($this->isVisible($loc)) {
                        return true;
                    }
                }
            } else {
                if ($this->isVisible($locator)) {
                    return true;
                }
            }
            sleep(1);
        }
        return false;
    }

    /**
     * Waits for AJAX request to continue.<br>
     * Method works only if AJAX request was sent by Prototype or JQuery framework.
     *
     * @param integer $timeout Timeout period in milliseconds. If not set, uses a default period.
     *
     * @return void
     */
    public function waitForAjax($timeout = null)
    {
        if (is_null($timeout)) {
            $timeout = $this->_browserTimeoutPeriod;
        }
        $jsCondition = 'var c = function(){if(typeof selenium.browserbot.getCurrentWindow().Ajax != "undefined"){'
            . 'if(selenium.browserbot.getCurrentWindow().Ajax.activeRequestCount){return false;};};'
            . 'if(typeof selenium.browserbot.getCurrentWindow().jQuery != "undefined"){'
            . 'if(selenium.browserbot.getCurrentWindow().jQuery.active){return false;};};return true;};c();';
        $this->waitForCondition($jsCondition, $timeout);
    }

    /**
     * Submits the opened form.
     *
     * @param string $buttonName Name of the button, what intended to save (submit) form (from UIMap)
     * @param boolean $validate
     *
     * @return Mage_Selenium_TestCase
     */
    public function saveForm($buttonName, $validate = true)
    {
        $this->_parseMessages();
        foreach (Mage_Selenium_TestCase::$_messages as $key => $value) {
            Mage_Selenium_TestCase::$_messages[$key] = array_unique($value);
        }
        $success = self::$xpathSuccessMessage;
        $error = self::$xpathErrorMessage;
        $validation = self::$xpathValidationMessage;
        $types = array('success', 'error', 'validation');
        foreach ($types as $message) {
            if (array_key_exists($message, Mage_Selenium_TestCase::$_messages)) {
                $exclude = '';
                foreach (Mage_Selenium_TestCase::$_messages[$message] as $messageText) {
                    $exclude .= "[not(..//.='$messageText')]";
                }
                ${$message} .= $exclude;
            }
        }
        $this->clickButton($buttonName, false);
        $this->waitForElement(array($success, $error, $validation));
        $this->addParameter('id', $this->defineIdFromUrl());
        if ($validate) {
            $this->validatePage();
        }

        return $this;
    }

    /**
     * Performs scrolling to the specified element in the specified list(block) with the specified name.
     *
     * @param string $elementType Type of the element that should be visible after scrolling
     * @param string $elementName Name of the element that should be visible after scrolling
     * @param string $blockType Type of the block where to use scroll
     * @param string $blockName Name of the block where to use scroll
     *
     * @return null
     */
    public function moveScrollToElement($elementType, $elementName, $blockType, $blockName)
    {
        // Getting XPath of the element what should be visible after scrolling
        $specElementXpath = $this->_getControlXpath($elementType, $elementName);
        // Getting @ID of the element what should be visible after scrolling
        $specElementId = $this->getAttribute($specElementXpath . "/@id");

        // Getting XPath of the block where scroll is using
        $specFieldsetXpath = $this->_getControlXpath($blockType, $blockName);
        // Getting @ID of the block where scroll is using
        $specFieldsetId = $this->getAttribute($specFieldsetXpath . "/@id");

        // Getting offset position of the element what should be visible after scrolling
        $destinationOffsetTop = $this->getEval("this.browserbot.findElement('id=" . $specElementId . "').offsetTop");
        // Moving scroll bar to previously defined offset
        // Position (to the element what should be visible after scrolling)
        $this->getEval("this.browserbot.findElement('id=" . $specFieldsetId
                           . "').scrollTop = " . $destinationOffsetTop);
    }

    /**
     * Moves the specified element (with type = $elementType and name = $elementName)<br>
     * over the specified JS tree (with type = $blockType and name = $blockName)<br>
     * to position = $moveToPosition
     *
     * @param string $elementType Type of the element to move
     * @param string $elementName Name of the element to move
     * @param string $blockType Type of the block that contains JS tree
     * @param string $blockName Name of the block that contains JS tree
     * @param integer $moveToPosition Index of the position where element should be after moving (default = 1)
     */
    public function moveElementOverTree($elementType, $elementName, $blockType, $blockName, $moveToPosition = 1)
    {
        // Getting XPath of the element to move
        $specElementXpath = $this->_getControlXpath($elementType, $elementName);
        // Getting @ID of the element to move
        $specElementId = $this->getAttribute($specElementXpath . "/@id");

        // Getting XPath of the block what is a JS tree
        $specFieldsetXpath = $this->_getControlXpath($blockType, $blockName);
        // Getting @ID of the block what is a JS tree
        $specFieldsetId = $this->getAttribute($specFieldsetXpath . "/@id");

        // Getting offset position of the element to move
        $destinationOffsetTop = $this->getEval("this.browserbot.findElement('id=" . $specElementId . "').offsetTop");

        // Storing of current height of the block with JS tree
        $tmpBlockHeight = (integer)$this->getEval("this.browserbot.findElement('id="
                                                      . $specFieldsetId . "').style.height");

        // If element to move situated abroad of the current height, it will be increased
        if ($destinationOffsetTop >= $tmpBlockHeight) {
            $destinationOffsetTop = $destinationOffsetTop + 50;
            $this->getEval("this.browserbot.findElement('id=" . $specFieldsetId
                               . "').style.height='" . $destinationOffsetTop . "px'");
        }

        $this->clickAt($specElementXpath, '1,1');
        $blockTo = $specFieldsetXpath . '//li[' . $moveToPosition . ']//a//span';
        $this->mouseDownAt($specElementXpath, '1,1');
        $this->mouseMoveAt($blockTo, '1,1');
        $this->mouseUpAt($blockTo, '1,1');
        $this->clickAt($specElementXpath, '1,1');
    }

    /**
     * Searches for the specified data in specific the grid and opens the found item.
     *
     * @param array $data Array of data to look up
     * @param boolean $willChangePage Triggers page reloading. If clicking the control doesn't result<br>
     * in page reloading, should be false (by default = true).
     * @param string|null $fieldSetName Fieldset name that contains the grid (by default = null)
     *
     * @return boolean
     */
    public function searchAndOpen(array $data, $willChangePage = true, $fieldSetName = null)
    {
        $this->_prepareDataForSearch($data);
        $xpathTR = $this->search($data, $fieldSetName);

        if ($xpathTR) {
            if ($willChangePage) {
                $itemId = $this->defineIdFromTitle($xpathTR);
                $this->addParameter('id', $itemId);
                $this->click($xpathTR . "/td[contains(text(),'" . $data[array_rand($data)] . "')]");
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                $this->validatePage();
            } else {
                $this->click($xpathTR . "/td[contains(text(),'" . $data[array_rand($data)] . "')]");
                $this->waitForAjax($this->_browserTimeoutPeriod);
            }
            return true;
        }
        return false;
    }

    /**
     * Searches for the specified data in specific the grid and selects the found item.
     *
     * @param array $data Array of data to look up
     * @param string|null $fieldSetName Fieldset name that contains the grid (by default = null)
     */
    public function searchAndChoose(array $data, $fieldSetName = null)
    {
        $this->_prepareDataForSearch($data);
        $xpathTR = $this->search($data, $fieldSetName);
        if ($xpathTR) {
            $xpathTR .= "//input[contains(@class,'checkbox') or contains(@class,'radio')][not(@disabled)]";
            if ($this->getValue($xpathTR) == 'off') {
                $this->click($xpathTR);
            }
        } else {
            $this->fail('Cant\'t find item in grid for data: ' . print_r($data, true));
        }
    }

    /**
     * Prepare data array to search in grid
     *
     * @param array $data Array of data to look up
     * @param array $verifyFields
     *
     * @return array
     */
    protected function _prepareDataForSearch(array &$data, array $verifyFields = array('dropdown' => 'website'))
    {
        $data = $this->arrayEmptyClear($data);
        foreach ($verifyFields as $fieldType => $fieldName) {
            if (array_key_exists($fieldName, $data) && !$this->controlIsPresent($fieldType, $fieldName)) {
                unset($data[$fieldName]);
            }
        }

        return $data;
    }

    /**
     * Searches the specified data in the specific grid. Returns null or XPath of the found data.
     *
     * @param array $data Array of data to look up.
     * @param string|null $fieldSetName Fieldset name that contains the grid (by default = null)
     *
     * @return string|null
     */
    public function search(array $data, $fieldSetName = null)
    {
        if (!$data) {
            return null;
        }

        $waitAjax = true;
        $xpath = '';
        $xpathContainer = $this->getCurrentUimapPage();
        if ($fieldSetName) {
            try {
                $xpathContainer = $xpathContainer->findFieldset($fieldSetName);
                $xpath = $xpathContainer->getXpath();
            } catch (Exception $e) {
                $errorMessage = 'Current location url: ' . $this->getLocation() . "\n"
                    . 'Current page "' . $this->getCurrentPage() . '": '
                    . $e->getMessage() . ' - "' . $fieldSetName . '"';
                $this->fail($errorMessage);
            }
        }
        $resetXpath = $xpath . $xpathContainer->findButton('reset_filter');
        $jsName = $this->getAttribute($resetXpath . '/@onclick');
        $jsName = preg_replace('/\.[\D]+\(\)/', '', $jsName);
        $scriptXpath = "//script[contains(text(),\"$jsName.useAjax = ''\")]";
        if ($this->isElementPresent($scriptXpath)) {
            $waitAjax = false;
        }
        $this->click($resetXpath);
        if ($waitAjax) {
            $this->pleaseWait();
        } else {
            $this->waitForPageToLoad($this->_browserTimeoutPeriod);
            $this->validatePage();
        }

        //Forming xpath that contains string 'Total $number records found' where $number - number of items in table
        $totalCount = intval($this->getText($xpath . self::$qtyElementsInTable));
        $xpathPager = $xpath . self::$qtyElementsInTable . "[not(text()='" . $totalCount . "')]";

        $xpathTR = $this->formSearchXpath($data);

        if (!$this->isElementPresent($xpath . $xpathTR) && $totalCount > 20) {
            // Fill in search form and click 'Search' button
            $this->fillForm($data);
            $this->clickButton('search', false);
            $this->waitForElement($xpathPager);
        }

        if ($this->isElementPresent($xpath . $xpathTR)) {
            return $xpath . $xpathTR;
        }
        return null;
    }

    /**
     * Forming xpath that contains the data to look up
     *
     * @param array $data Array of data to look up
     *
     * @return string
     */
    public function formSearchXpath(array $data)
    {
        $xpathTR = "//table[@class='data']//tr";
        foreach ($data as $key => $value) {
            if (!preg_match('/_from/', $key) and !preg_match('/_to/', $key) and !is_array($value)) {
                if (strpos($value, "'")) {
                    $value = "concat('" . str_replace('\'', "',\"'\",'", $value) . "')";
                } else {
                    $value = "'" . $value . "'";
                }
                $xpathTR .= "[td[contains(text(),$value)]]";
            }
        }
        return $xpathTR;
    }

    /**
     * Fills any form with the provided data. Specific Tab can be filled only if $tabId is provided.
     *
     * @param array|string $data Array of data to fill or datasource name
     * @param string $tabId Tab ID from UIMap (by default = '')
     *
     * @throws InvalidArgumentException, OutOfRangeException
     * @return Mage_Selenium_TestCase|boolean
     */
    public function fillForm($data, $tabId = '')
    {
        if (is_string($data)) {
            $data = $this->loadData($data);
        }
        if (!is_array($data)) {
            throw new InvalidArgumentException('FillForm argument "data" must be an array!!!');
        }

        $formData = $this->getCurrentUimapPage()->getMainForm();

        if (!$formData) {
            throw new OutOfRangeException("Can't find main form in UIMap array '"
                . $this->getLocation() . "', area['" . $this->getArea() . "']");
        }

        if ($tabId && $formData->getTab($tabId)) {
            $fieldsets = $formData->getTab($tabId)->getAllFieldsets();
        } else {
            $fieldsets = $formData->getAllFieldsets();
        }
        $fieldsets->assignParams($this->getParamsDecorator());
        // if we have got empty UIMap but not empty dataset
        if (empty($fieldsets) && !empty($data)) {
            return false;
        }

        $formDataMap = $this->_getFormDataMap($fieldsets, $data);

        if ($tabId) {
            $this->openTab($tabId);
        }

        try {
            foreach ($formDataMap as $formFieldName => $formField) {
                switch ($formField['type']) {
                    case self::FIELD_TYPE_INPUT:
                        $this->_fillFormField($formField);
                        break;
                    case self::FIELD_TYPE_CHECKBOX:
                        $this->_fillFormCheckbox($formField);
                        break;
                    case self::FIELD_TYPE_DROPDOWN:
                        $this->_fillFormDropdown($formField);
                        break;
                    case self::FIELD_TYPE_RADIOBUTTON:
                        $this->_fillFormRadiobutton($formField);
                        break;
                    case self::FIELD_TYPE_MULTISELECT:
                        $this->_fillFormMultiselect($formField);
                        break;
                    default:
                        throw new PHPUnit_Framework_Exception('Unsupported field type');
                }
            }
        } catch (PHPUnit_Framework_Exception $e) {
            $errorMessage = isset($formFieldName) ? 'Problem with field \'' . $formFieldName . '\': ' . $e->getMessage()
                : $e->getMessage();
            $this->fail($errorMessage);
        }

        return true;
    }

    /**
     * Verifies values on the opened form
     *
     * @param array|string $data Array of data to verify or datasource name
     * @param string $tabName Defines a specific Tab on the page that contains the form to verify (by default = '')
     * @param array $skipElements Array of elements that will be skipped during verification <br>
     * (default = array('password'))
     *
     * @throws InvalidArgumentException, OutOfRangeException
     * @return boolean
     */
    public function verifyForm($data, $tabName = '', $skipElements = array('password'))
    {
        if (is_string($data)) {
            $data = $this->loadData($data);
        }
        if (!is_array($data)) {
            throw new InvalidArgumentException('FillForm argument "data" must be an array!!!');
        }

        $formData = $this->getCurrentUimapPage()->getMainForm();

        if (!$formData) {
            throw new OutOfRangeException("Can't find main form in UIMap array '"
                . $this->getLocation() . "', area['" . $this->getArea() . "']");
        }

        if ($tabName && $formData->getTab($tabName)) {
            $fieldsets = $formData->getTab($tabName)->getAllFieldsets();
        } else {
            $fieldsets = $formData->getAllFieldsets();
        }
        $fieldsets->assignParams($this->getParamsDecorator());
        //If we have got empty UIMap but not an empty dataset
        if (empty($fieldsets) && !empty($data)) {
            return false;
        }

        foreach ($data as $key => $value) {
            if (in_array($key, $skipElements) || $value === '%noValue%') {
                unset($data[$key]);
            }
        }
        $formDataMap = $this->_getFormDataMap($fieldsets, $data);

        $resultFlag = true;
        foreach ($formDataMap as $formFieldName => $formField) {
            switch ($formField['type']) {
                case self::FIELD_TYPE_INPUT:
                    if ($this->isElementPresent($formField['path'])) {
                        $val = $this->getValue($formField['path']);
                        if ($val != $formField['value']) {
                            $this->addVerificationMessage($formFieldName . ": The stored value is not equal to specified: ("
                                                              . $formField['value'] . "' != '" . $val . "')");
                            $resultFlag = false;
                        }
                    } else {
                        $this->addVerificationMessage('Can not find field (xpath:' . $formField['path'] . ')');
                        $resultFlag = false;
                    }
                    break;
                case self::FIELD_TYPE_CHECKBOX:
                case self::FIELD_TYPE_RADIOBUTTON:
                    if ($this->isElementPresent($formField['path'])) {
                        $isChecked = $this->isChecked($formField['path']);
                        $expectedVal = strtolower($formField['value']);
                        if (($isChecked && $expectedVal != 'yes') ||
                            (!$isChecked && !($expectedVal == 'no' || $expectedVal == ''))
                        ) {
                            $printVal = ($isChecked) ? 'yes' : 'no';
                            $this->addVerificationMessage($formFieldName . ": The stored value is not equal to specified: ("
                                                              . $expectedVal . "' != '" . $printVal . "')");
                            $resultFlag = false;
                        }
                    } else {
                        $this->addVerificationMessage('Can not find field (xpath:' . $formField['path'] . ')');
                        $resultFlag = false;
                    }
                    break;
                case self::FIELD_TYPE_DROPDOWN:
                    if ($this->isElementPresent($formField['path'])) {
                        $label = $this->getSelectedLabel($formField['path']);
                        if ($formField['value'] != $label) {
                            $this->addVerificationMessage($formFieldName . ": The stored value is not equal to specified: ("
                                                              . $formField['value'] . "' != '" . $label . "')");
                            $resultFlag = false;
                        }
                    } else {
                        $this->addVerificationMessage('Can not find field (xpath:' . $formField['path'] . ')');
                        $resultFlag = false;
                    }
                    break;
                case self::FIELD_TYPE_MULTISELECT:
                    if ($this->isElementPresent($formField['path'])) {
                        $selectedLabels = $this->getSelectedLabels($formField['path']);
                        $selectedLabels = array_map('trim', $selectedLabels, array(chr(0xC2) . chr(0xA0)));
                        $expectedLabels = explode(',', $formField['value']);
                        $expectedLabels = array_map('trim', $expectedLabels);
                        foreach ($expectedLabels as $value) {
                            if (!in_array($value, $selectedLabels)) {
                                $this->addVerificationMessage($formFieldName . ": The value '" . $value
                                                                  . "' is not selected. (Selected values are: '"
                                                                  . implode(', ', $selectedLabels) . "')");
                                $resultFlag = false;
                            }
                        }
                        if (count($selectedLabels) != count($expectedLabels)) {
                            $this->addVerificationMessage("Amounts of the expected options are not equal to selected: ('"
                                                              . $formField['value'] . "' != '"
                                                              . implode(', ', $selectedLabels) . "')");
                            $resultFlag = false;
                        }
                    } else {
                        $this->addVerificationMessage('Can not find field (xpath:' . $formField['path'] . ')');
                        $resultFlag = false;
                    }
                    break;
                default:
                    $this->addVerificationMessage('Unsupported field type');
                    $resultFlag = false;
            }
        }

        return $resultFlag;
    }

    /**
     * Fills a text field of ('field' | 'input') control type by typing a value.
     *
     * @param array $fieldData Array of a 'path' to control and 'value' to type
     *
     * @throws PHPUnit_Framework_Exception
     */
    protected function _fillFormField($fieldData)
    {
        if ($this->isElementPresent($fieldData['path']) && $this->isEditable($fieldData['path'])) {
            $this->type($fieldData['path'], $fieldData['value']);
            $this->waitForAjax();
        } else {
            throw new PHPUnit_Framework_Exception("Can't fill in the field: {$fieldData['path']}");
        }
    }

    /**
     * Fills 'multiselect' control by selecting the specified values.
     *
     * @param array $fieldData Array of a 'path' to control and 'value' to select
     *
     * @throws PHPUnit_Framework_Exception
     */
    protected function _fillFormMultiselect($fieldData)
    {
        $valuesArray = array();
        if ($this->waitForElement($fieldData['path'], 5) && $this->isEditable($fieldData['path'])) {
            $this->removeAllSelections($fieldData['path']);
            if (strtolower($fieldData['value']) == 'all') {
                $count = $this->getXpathCount($fieldData['path'] . '//option');
                for ($i = 1; $i <= $count; $i++) {
                    $valuesArray[] = $this->getText($fieldData['path'] . "//option[$i]");
                }
            } else {
                $valuesArray = explode(',', $fieldData['value']);
                $valuesArray = array_map('trim', $valuesArray);
            }
            foreach ($valuesArray as $value) {
                if ($value != null) {
                    if ($this->isElementPresent($fieldData['path'] . "//option[text()='" . $value . "']")) {
                        $this->addSelection($fieldData['path'], 'label=' . $value);
                    } else {
                        $this->addSelection($fieldData['path'], 'regexp:' . preg_quote($value));
                    }
                }
            }
        } else {
            throw new PHPUnit_Framework_Exception("Can't fill in the multiselect field: {$fieldData['path']}");
        }
    }

    /**
     * Fills the 'dropdown' control by selecting the specified value.
     *
     * @param array $fieldData Array of a 'path' to control and 'value' to select
     *
     * @throws PHPUnit_Framework_Exception
     */
    protected function _fillFormDropdown($fieldData)
    {
        $fieldXpath = $fieldData['path'];
        if ($this->isElementPresent($fieldXpath) && $this->isEditable($fieldXpath)) {
            if ($this->getSelectedValue($fieldXpath) != $fieldData['value']) {
                if ($this->isElementPresent($fieldXpath . "//option[text()='" . $fieldData['value'] . "']")) {
                    $this->select($fieldXpath, 'label=' . $fieldData['value']);
                } else {
                    $this->select($fieldXpath, 'regexp:' . preg_quote($fieldData['value']));
                }
                $this->waitForAjax();
            }
        } else {
            throw new PHPUnit_Framework_Exception("Can't fill in the dropdown field: {$fieldData['path']}");
        }
    }

    /**
     * Fills 'checkbox' control by selecting/unselecting it based on the specified value.
     *
     * @param array $fieldData Array of a 'path' to control and 'value' to select. Value can be 'Yes' or 'No'.
     *
     * @throws PHPUnit_Framework_Exception
     */
    protected function _fillFormCheckbox($fieldData)
    {
        if ($this->waitForElement($fieldData['path'], 5) && $this->isEditable($fieldData['path'])) {
            if (strtolower($fieldData['value']) == 'yes') {
                if (($this->getValue($fieldData['path']) == 'off') || ($this->getValue($fieldData['path']) == '0')) {
                    $this->click($fieldData['path']);
                    $this->waitForAjax();
                }
            } elseif (strtolower($fieldData['value']) == 'no') {
                if (($this->getValue($fieldData['path']) == 'on') || ($this->getValue($fieldData['path']) == '1')) {
                    $this->click($fieldData['path']);
                    $this->waitForAjax();
                }
            }
        } else {
            throw new PHPUnit_Framework_Exception("Can't fill in the checkbox field: {$fieldData['path']}");
        }
    }

    /**
     * Fills the 'radiobutton' control by selecting the specified value.
     *
     * @param array $fieldData Array of a 'path' to control and 'value' to select.<br>
     * Value should be 'Yes' to select the radiobutton.
     *
     * @throws PHPUnit_Framework_Exception
     */
    protected function _fillFormRadiobutton($fieldData)
    {
        if ($this->waitForElement($fieldData['path'], 5) && $this->isEditable($fieldData['path'])) {
            if (strtolower($fieldData['value']) == 'yes') {
                $this->click($fieldData['path']);
                $this->waitForAjax();
            } else {
                $this->uncheck($fieldData['path']);
                $this->waitForAjax();
            }
        } else {
            throw new PHPUnit_Framework_Exception("Can't fill in the radiobutton field: {$fieldData['path']}");
        }
    }

    ################################################################################
    #                                                                              #
    #                             Magento helper methods                           #
    #                                                                              #
    ################################################################################
    /**
     * Waits for "Please wait" animated gif to appear and disappear.
     *
     * @param integer $waitAppear Timeout in seconds to wait for the loader to appear (by default = 10)
     * @param integer $waitDisappear Timeout in seconds to wait for the loader to disappear (by default = 30)
     *
     * @return Mage_Selenium_TestCase
     */
    public function pleaseWait($waitAppear = 10, $waitDisappear = 30)
    {
        for ($second = 0; $second < $waitAppear; $second++) {
            if ($this->isElementPresent(Mage_Selenium_TestCase::$xpathLoadingHolder)) {
                break;
            }
            sleep(1);
        }

        for ($second = 0; $second < $waitDisappear; $second++) {
            if (!$this->isElementPresent(Mage_Selenium_TestCase::$xpathLoadingHolder)) {
                break;
            }
            sleep(1);
        }

        return $this;
    }

    /**
     * Logs in as a default admin user on back-end
     * @return Mage_Selenium_TestCase
     */
    public function loginAdminUser()
    {
        $loginData = array(
            'user_name' => $this->_applicationHelper->getDefaultAdminUsername(),
            'password'  => $this->_applicationHelper->getDefaultAdminPassword()
        );

        $this->admin('log_in_to_admin', false);

        $currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
        if ($currentPage != $this->_firstPageAfterAdminLogin) {
            $this->validatePage('log_in_to_admin');
            $this->fillForm($loginData);
            $this->clickButton('login', false);
            $this->waitForElement(array(self::$xpathAdminLogo,
                                       self::$xpathErrorMessage,
                                       self::$xpathValidationMessage));
            if ($this->_findCurrentPageFromUrl($this->getLocation()) != $this->_firstPageAfterAdminLogin) {
                $this->fail('Admin was not logged in');
            }
            if ($this->isElementPresent(self::$xpathGoToNotifications)
                && $this->waitForElement(self::$xpathIncomingMessageClose, 5)) {
                $this->click(self::$xpathIncomingMessageClose);
            }
            $this->validatePage($this->_firstPageAfterAdminLogin);
        }

        return $this;
    }

    /**
     * Logs out from back-end
     * @return Mage_Selenium_TestCase
     */
    public function logoutAdminUser()
    {
        if ($this->isElementPresent(self::$xpathLogOutAdmin)) {
            $this->click(self::$xpathLogOutAdmin);
            $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        }
        $this->validatePage('log_in_to_admin');

        return $this;
    }

    /**
     * Clears invalided cache in Admin
     */
    public function clearInvalidedCache()
    {
        if ($this->isElementPresent(self::$xpathCacheInvalidated)) {
            $this->clickAndWait(self::$xpathCacheInvalidated);
            $this->validatePage('cache_storage_management');

            $invalided = array('cache_disabled', 'cache_invalided');
            foreach ($invalided as $value) {
                $xpath = $this->_getControlXpath('pageelement', $value);
                $qty = $this->getXpathCount($xpath);
                for ($i = 1; $i < $qty + 1; $i++) {
                    $fillData = array('path'  => $xpath . '[' . $i . ']//input',
                                      'value' => 'Yes');
                    $this->_fillFormCheckbox($fillData);
                }
            }
            $this->fillForm(array('cache_action' => 'Refresh'));

            $selectedItems = $this->getText($this->_getControlXpath('pageelement', 'selected_items'));
            $this->addParameter('qtySelected', $selectedItems);

            $this->clickButton('submit', false);
            $alert = $this->isAlertPresent();
            if ($alert) {
                $text = $this->getAlert();
                $this->fail($text);
            }
            $this->waitForNewPage();
            $this->validatePage('cache_storage_management');
        }
    }

    /**
     * Reindex indexes that are marked as 'reindex required' or 'update required'.
     */
    public function reindexInvalidedData()
    {
        if ($this->isElementPresent(self::$xpathIndexesInvalidated)) {
            $this->clickAndWait(self::$xpathIndexesInvalidated);
            $this->validatePage('index_management');

            $invalided = array('reindex_required', 'update_required');
            foreach ($invalided as $value) {
                $xpath = $this->_getControlXpath('pageelement', $value);
                while ($this->isElementPresent($xpath)) {
                    $this->click($xpath . "//a[text()='Reindex Data']");
                    $this->waitForNewPage();
                    $this->validatePage('index_management');
                }
            }
        }
    }

    /**
     * @throws PHPUnit_Framework_Exception
     */
    public function waitForNewPage()
    {
        $notLoaded = true;
        $retries = 0;
        while ($notLoaded) {
            try {
                $retries++;
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                $notLoaded = false;
            } catch (PHPUnit_Framework_Exception $e) {
                if ($retries == 10) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Performs LogOut customer on front-end
     * @return Mage_Selenium_TestCase
     */
    public function logoutCustomer()
    {
        $this->frontend('home');
        $xpath = "//a[@title='Log Out']";
        if ($this->isElementPresent($xpath)) {
            $this->clickAndWait($xpath, $this->_browserTimeoutPeriod);
            $this->frontend('home');
        }

        return $this;
    }

    /**
     * Selects StoreView on Frontend
     *
     * @param string $storeViewName
     */
    public function selectFrontStoreView($storeViewName = 'Default Store View')
    {
        $xpath = "//select[@id='select-language']";
        $toSelect = $xpath . '//option[normalize-space(text())="' . $storeViewName . '"]';
        $isSelected = $toSelect . '[@selected]';
        if (!$this->isElementPresent($isSelected)) {
            $this->select($xpath, $storeViewName);
            $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        }
        $this->assertElementPresent($isSelected, '\'' . $storeViewName . '\' store view not selected');
    }

    ################################################################################
    #                                                                              #
    #       Should be removed if PHPUnit_Selenium version is 1.2.1 or more         #
    #                                                                              #
    ################################################################################
    /**
     * @static
     *
     * @param  string $className
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite($className)
    {
        $suite = new PHPUnit_Framework_TestSuite;
        $suite->setName($className);

        $class = new ReflectionClass($className);
        $classGroups = PHPUnit_Util_Test::getGroups($className);
        $staticProperties = $class->getStaticProperties();

        // Create tests from Selenese/HTML files.
        if (isset($staticProperties['seleneseDirectory']) &&
            is_dir($staticProperties['seleneseDirectory'])
        ) {
            $files = array_merge(
                self::getSeleneseFiles($staticProperties['seleneseDirectory'], '.htm'),
                self::getSeleneseFiles($staticProperties['seleneseDirectory'], '.html')
            );

            // Create tests from Selenese/HTML files for multiple browsers.
            if (!empty($staticProperties['browsers'])) {
                foreach ($staticProperties['browsers'] as $browser) {
                    $browserSuite = new PHPUnit_Framework_TestSuite;
                    $browserSuite->setName($className . ': ' . $browser['name']);

                    foreach ($files as $file) {
                        self::addConfiguredTestTo($browserSuite, new $className($file, array(), '', $browser),
                                                  $classGroups
                        );
                    }

                    $suite->addTest($browserSuite);
                }
            }

            // Create tests from Selenese/HTML files for single browser.
            else {
                foreach ($files as $file) {
                    self::addConfiguredTestTo($suite, new $className($file), $classGroups);
                }
            }
        }

        // Create tests from test methods for multiple browsers.
        if (!empty($staticProperties['browsers'])) {
            foreach ($staticProperties['browsers'] as $browser) {
                $browserSuite = new PHPUnit_Framework_TestSuite;
                $browserSuite->setName($className . ': ' . $browser['name']);

                foreach ($class->getMethods() as $method) {
                    if (PHPUnit_Framework_TestSuite::isPublicTestMethod($method)) {
                        $name = $method->getName();
                        $data = PHPUnit_Util_Test::getProvidedData($className, $name);
                        $groups = PHPUnit_Util_Test::getGroups($className, $name);

                        // Test method with @dataProvider.
                        if (is_array($data) || $data instanceof Iterator) {
                            $dataSuite = new PHPUnit_Framework_TestSuite_DataProvider(
                                $className . '::' . $name
                            );

                            foreach ($data as $_dataName => $_data) {
                                self::addConfiguredTestTo($dataSuite,
                                                          new $className($name, $_data, $_dataName, $browser), $groups);
                            }

                            $browserSuite->addTest($dataSuite);
                        }

                        // Test method with invalid @dataProvider.
                        else {
                            if ($data === false) {
                                $browserSuite->addTest(
                                    new PHPUnit_Framework_Warning(
                                        sprintf(
                                            'The data provider specified for %s::%s is invalid.',
                                            $className, $name
                                        )
                                    )
                                );
                            }

                            // Test method without @dataProvider.
                            else {
                                self::addConfiguredTestTo($browserSuite, new $className($name, array(), '', $browser),
                                                          $groups);
                            }
                        }
                    }
                }

                $suite->addTest($browserSuite);
            }
        }

        // Create tests from test methods for single browser.
        else {
            foreach ($class->getMethods() as $method) {
                if (PHPUnit_Framework_TestSuite::isPublicTestMethod($method)) {
                    $name = $method->getName();
                    $data = PHPUnit_Util_Test::getProvidedData($className, $name);
                    $groups = PHPUnit_Util_Test::getGroups($className, $name);

                    // Test method with @dataProvider.
                    if (is_array($data) || $data instanceof Iterator) {
                        $dataSuite = new PHPUnit_Framework_TestSuite_DataProvider(
                            $className . '::' . $name
                        );

                        foreach ($data as $_dataName => $_data) {
                            self::addConfiguredTestTo($dataSuite, new $className($name, $_data, $_dataName), $groups
                            );
                        }

                        $suite->addTest($dataSuite);
                    }

                    // Test method with invalid @dataProvider.
                    else {
                        if ($data === false) {
                            $suite->addTest(
                                new PHPUnit_Framework_Warning(
                                    sprintf(
                                        'The data provider specified for %s::%s is invalid.', $className,
                                        $name
                                    )
                                )
                            );
                        }

                        // Test method without @dataProvider.
                        else {
                            self::addConfiguredTestTo($suite, new $className($name), $groups);
                        }
                    }
                }
            }
        }

        return $suite;
    }

    /**
     * Returns a string representation of the messages.
     *
     * @static
     *
     * @param array|string $message
     *
     * @return string
     */
    private static function messagesToString($message)
    {
        if (is_array($message) && $message) {
            $message = implode("\n", call_user_func_array('array_merge', $message));
        }
        return $message;
    }

    /**
     * @static
     *
     * @param PHPUnit_Framework_TestSuite $suite
     * @param PHPUnit_Framework_TestCase $test
     * @param $classGroups
     */
    private static function addConfiguredTestTo(PHPUnit_Framework_TestSuite $suite, PHPUnit_Framework_TestCase $test, $classGroups)
    {
        list ($methodName,) = explode(' ', $test->getName());
        $test->setDependencies(
            PHPUnit_Util_Test::getDependencies(get_class($test), $methodName)
        );
        $suite->addTest($test, $classGroups);
    }

    /**
     * Sets the dependencies of a TestCase.
     *
     * @param  array $dependencies
     *
     * @since  Method available since Release 3.4.0
     */
    public function setDependencies(array $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * Runs the test case and collects the results in a TestResult object.
     * If no TestResult object is passed a new one will be created.
     *
     * @param  PHPUnit_Framework_TestResult $result
     *
     * @return PHPUnit_Framework_TestResult
     * @throws InvalidArgumentException
     */
    public function run(PHPUnit_Framework_TestResult $result = null)
    {
        if ($result === null) {
            $result = $this->createResult();
        }

        $this->result = $result;

        $this->collectCodeCoverageInformation = $result->getCollectCodeCoverageInformation();

        foreach ($this->drivers as $driver) {
            $driver->setCollectCodeCoverageInformation(
                $this->collectCodeCoverageInformation
            );
        }

        if (!$this->handleDependencies()) {
            return;
        }

        $result->run($this);

        if ($this->collectCodeCoverageInformation) {
            $result->getCodeCoverage()->append(
                $this->getCodeCoverage(), $this
            );
        }

        return $result;
    }

    /**
     * @since Method available since Release 3.5.4
     * @return bool
     */
    protected function handleDependencies()
    {
        if (!empty($this->dependencies) && !$this->inIsolation) {
            $className = get_class($this);
            $passed = $this->result->passed();
            $passedKeys = array_keys($passed);
            $numKeys = count($passedKeys);

            for ($i = 0; $i < $numKeys; $i++) {
                $pos = strpos($passedKeys[$i], ' with data set');

                if ($pos !== false) {
                    $passedKeys[$i] = substr($passedKeys[$i], 0, $pos);
                }
            }

            $passedKeys = array_flip(array_unique($passedKeys));

            foreach ($this->dependencies as $dependency) {
                if (strpos($dependency, '::') === false) {
                    $dependency = $className . '::' . $dependency;
                }

                if (!isset($passedKeys[$dependency])) {
                    $this->result->addError(
                        $this,
                        new PHPUnit_Framework_SkippedTestError(
                            sprintf(
                                'This test depends on "%s" to pass.', $dependency
                            )
                        ), 0
                    );

                    return false;
                }

                if (isset($passed[$dependency])) {
                    if (is_array($passed[$dependency]) && array_key_exists('size', $passed[$dependency])) {
                        if ($passed[$dependency]['size'] > $this->getSize()) {
                            $this->result->addError(
                                $this,
                                new PHPUnit_Framework_SkippedTestError(
                                    'This test depends on a test that is larger than itself.'
                                ), 0
                            );

                            return false;
                        }
                        $this->dependencyInput[] = $passed[$dependency]['result'];
                    } else {
                        $this->dependencyInput[] = $passed[$dependency];
                    }
                } else {
                    $this->dependencyInput[] = null;
                }
            }
        }

        return true;
    }

    /**
     * Override to run the test and assert its state.
     * @return mixed
     * @throws RuntimeException
     */
    protected function runTest()
    {
        if ($this->name === null) {
            throw new PHPUnit_Framework_Exception(
                'PHPUnit_Framework_TestCase::$name must not be null.'
            );
        }

        try {
            $class = new ReflectionClass($this);
            $method = $class->getMethod($this->name);
        } catch (ReflectionException $e) {
            $this->fail($e->getMessage());
        }

        try {
            $testResult = $method->invokeArgs(
                $this, array_merge($this->data, $this->dependencyInput)
            );
            $this->assertEmptyVerificationErrors();
        } catch (Exception $e) {
            if (!$e instanceof PHPUnit_Framework_IncompleteTest &&
                !$e instanceof PHPUnit_Framework_SkippedTest &&
                is_string($this->expectedException)
            ) {
                $this->assertThat(
                    $e,
                    new PHPUnit_Framework_Constraint_Exception(
                        $this->expectedException
                    )
                );

                if (is_string($this->expectedExceptionMessage) &&
                    !empty($this->expectedExceptionMessage)
                ) {
                    $this->assertThat(
                        $e,
                        new PHPUnit_Framework_Constraint_ExceptionMessage(
                            $this->expectedExceptionMessage
                        )
                    );
                }

                if ($this->expectedExceptionCode !== null) {
                    $this->assertThat(
                        $e,
                        new PHPUnit_Framework_Constraint_ExceptionCode(
                            $this->expectedExceptionCode
                        )
                    );
                }

                return;
            } else {
                throw $e;
            }
        }

        if ($this->expectedException !== null) {
            $this->assertThat(
                null,
                new PHPUnit_Framework_Constraint_Exception(
                    $this->expectedException
                )
            );
        }

        return $testResult;
    }

    /**
     * Works correctly with PHPUnit 3.5.15 only (right now)
     * Solving problem with coverage, send to phpunit_coverage.php cookieid(single) instead of testid(multiple)
     * @return array
     * @since  Method available since Release 3.2.0
     */
    protected function getCodeCoverage()
    {
        if (!empty($this->coverageScriptUrl)) {
            $url = sprintf(
                '%s?PHPUNIT_SELENIUM_TEST_ID=%s',
                $this->coverageScriptUrl,
                //$this->testId,
                $_COOKIE['PHPUNIT_SELENIUM_TEST_ID']
            );

            $buffer = @file_get_contents($url);

            if ($buffer !== FALSE) {
                return $this->matchLocalAndRemotePaths(unserialize($buffer));
            }
        }

        return array();
    }
}
