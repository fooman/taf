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
 * Centralized entry point for handling PHPUnit built-in and custom events for Testlink integration
 */
class Mage_Testlink_Listener implements PHPUnit_Framework_TestListener
{

    /**
     * Registered event observers classes
     *
     * @var array
     */
    protected static $_observerClasses = array();

    /**
     * Registered event observers
     *
     * @var array
     */
    protected $_observers = array();

    /**
     * @var PHPUnit_Framework_TestCase
     */
    protected $_currentTest;

    /**
     * @var string|null
     */
    protected $_project;

    /**
     * @var string|null
     */
    protected $_testPlan;

    /**
     * @var string|null
     */
    protected $_build;

    /**
     * Gets the project
     *
     * @return string|null
     */
    public function getProject()
    {
        return $this->_project;
    }

    /**
     * Gets test plan
     *
     * @return string|null
     */
    public function getTestPlan()
    {
        return $this->_testPlan;
    }

    /**
     * Gets build
     *
     * @return string|null
     */
    public function getBuild()
    {
        return $this->_build;
    }

    /**
     * Retrieve currently running test
     *
     * @return PHPUnit_Framework_TestCase
     */
    public function getCurrentTest()
    {
        return $this->_currentTest;
    }

    /**
     * Puts observer class to array
     *
     * @static
     * @param $observerClass
     */
    public static function registerObserver($observerClass)
    {
        self::$_observerClasses[] = $observerClass;
    }

    /**
     * Initializes connection settings
     */
    public function __construct($project=null, $url=null, $devkey=null, $testPlan=null, $build=null)
    {
        //Initialize Testlink credentials
        if (isset($url)) {
            Mage_Testlink_Connector::$SERVER_URL = $url;
        }
        Mage_Testlink_Connector::$devKey = $devkey;
        $this->_project = $project;
        $this->_testPlan = $testPlan;
        $this->_build = $build;
        if (isset($devkey) && isset($project)
                && ($devkey != "null") && ($project != "null")
                && ($devkey != "false") && ($project != "false")) {
            $this->instantiateObservers();
        }
    }

    /**
     * Constructor instantiates observers from registered classes and passes itself to constructor
     */
    protected function instantiateObservers()
    {
        foreach (self::$_observerClasses as $observerClass) {
            $this->_observers[] = new $observerClass($this);
        }
    }

    /**
     * Notify registered observers that are interested in the event
     *
     * @param string $eventName
     * @param bool $reverseOrder
     */
    protected function notifyObservers($eventName, $reverseOrder = false)
    {
        $observers = ($reverseOrder ? array_reverse($this->_observers) : $this->_observers);
        foreach ($observers as $observerInstance) {
            $callback = array($observerInstance, $eventName);
            if (is_callable($callback)) {
                call_user_func($callback);
            }
        }
    }

    /**
     * An error occurred.
     * Method is required by implemented interface, but is not needed by the class.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->_currentTest = $test;
        $this->notifyObservers('testFailed');
    }

    /**
     * A failure occurred.
     * Method is required by implemented interface, but is not needed by the class.
     *
     * @param  PHPUnit_Framework_Test                 $test
     * @param  PHPUnit_Framework_AssertionFailedError $e
     * @param  float                                  $time
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->_currentTest = $test;
        $this->notifyObservers('testFailed');
    }

    /**
     * Incomplete test.
     * Method is required by implemented interface, but is not needed by the class.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->_currentTest = $test;
        $this->notifyObservers('testIncomplete');
    }

    /**
     * Skipped test.
     * Method is required by implemented interface, but is not needed by the class.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     * @since  Method available since Release 3.0.0
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->_currentTest = $test;
        $this->notifyObservers('testSkipped');
    }

    /**
     * A test suite started.
     *
     * @param  PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        /* PHPUnit runs tests with data provider in own test suite for each test, so just skip such test suites */
        if ($suite instanceof PHPUnit_Framework_TestSuite_DataProvider) {
            return;
        }
        $this->notifyObservers('startTestSuite');
    }

    /**
     * A test suite ended.
     *
     * @param  PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        if ($suite instanceof PHPUnit_Framework_TestSuite_DataProvider) {
            return;
        }
        $this->notifyObservers('endTestSuite', true);
    }

    /**
     * A test started.
     *
     * @param  PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        if (!($test instanceof PHPUnit_Framework_TestCase) || ($test instanceof PHPUnit_Framework_Warning)) {
            return;
        }
        $this->_currentTest = $test;
        $this->notifyObservers('startTest');
    }

    /**
     * A test ended.
     * Method signature is implied by implemented interface, not all parameters are needed.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  float                  $time
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        if (!($test instanceof PHPUnit_Framework_TestCase) || ($test instanceof PHPUnit_Framework_Warning)) {
            return;
        }
        $this->notifyObservers('endTest', true);
    }
}
