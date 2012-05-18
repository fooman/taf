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
 * @subpackage  Mage_Listener_Observers
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Implementation of the Teamcity Observer for getting tests statuses in real-time
 */
class Mage_Listener_Observers_TeamcityObserver extends PHPUnit_Util_Printer
{
    /**
     * @var Mage_Listener_EventListener
     */
    protected static $_listener;

    /**
     * @var bool
     */
    protected $_failed;

    /**
     * Counter of errors
     *
     * @var int
     */
    protected $_errorsCount = 0;

    /**
     * Counter of skipped
     *
     * @var int
     */
    protected $_skippedCount = 0;

    /**
     * Counter of failures
     *
     * @var int
     */
    protected $_failuresCount = 0;

    /**
     * Counter of incomplete tests
     *
     * @var int
     */
    protected $_incompleteCount = 0;

    /**
     * Constructs Teamcity Observer
     *
     * @param Mage_Listener_EventListener $listener
     */
    public function __construct(Mage_Listener_EventListener $listener)
    {
        $this->setListener($listener);
    }

    /**
     * Sets protected variable $listener
     *
     * @param Mage_Listener_EventListener $listener
     *
     * @return Mage_Listener_Observers_TeamcityObserver
     */
    protected function setListener($listener)
    {
        self::$_listener = $listener;
        return $this;
    }

    /**
     * Gets protected variable $listener
     *
     * @return Mage_Listener_EventListener|null
     */
    protected function getListener()
    {
        return $this->_listener;
    }

    /**
     * A test started.
     *
     */
    public function startTest()
    {
        $listener = self::$_listener;
        $test = ($listener) ? $listener->getCurrentTest(): null;
        if ($test != null) {
            //setting default status for test
            //$this->_failed will be used for setting appropriate status for test in endTest()
            $this->_failed = false;
        }
        $tcmessage = sprintf("##teamcity[testStarted name='%s' captureStandardOutput='true']" . PHP_EOL,
                             get_class($test) . "::" . $test->getName());
        $this->write($tcmessage);
    }

    /**
     * Generates message from last failures, errors, skip
     *
     * @return string
     */
    protected function getMessage()
    {
        $listener = self::$_listener;
        $test = ($listener) ? $listener->getCurrentTest() : null;
        $message = '';
        if ($test != null) {
            $message = get_class($test) . "::" . $test->getName() . "\n";
            $resultObject = $test->getTestResultObject();
            if (empty($resultObject)) {
                return $message;
            }
            //Getting actual errors count
            $errorsCount = $resultObject->errorCount();
            //Getting actual failures count
            $failuresCount = $resultObject->failureCount();
            //Getting actual skipped tests count
            $skippedCount = $resultObject->skippedCount();
            //Getting actual incomplete tests count
            $incompleteCount = $resultObject->notImplementedCount();
            //Comparing number of actual errors and previous errors
            if ($errorsCount > $this->_errorsCount) {
                //Getting last error message if actual number of errors is greater than previous
                $errors = $test->getTestResultObject()->errors();
                $error = end($errors);
                $message .= $error->exceptionMessage();
                $this->_errorsCount = $errorsCount;
            }
            //Comparing number of actual failures and previous failures
            if ($failuresCount > $this->_failuresCount) {
                //Getting last failure message if actual number of failures is greater than previous
                $fails = $test->getTestResultObject()->failures();
                $fail = end($fails);
                $message .= $fail->exceptionMessage();
                $this->_failuresCount = $failuresCount;
            }
            //Comparing number of actual skipped tests and previously skipped tests
            if ($skippedCount > $this->_skippedCount) {
                //Getting last skipped test message if actual number of skipped tests is greater than previous
                $skipped = $test->getTestResultObject()->skipped();
                $skip = end($skipped);
                $message .= $skip->exceptionMessage();
                $this->_skippedCount = $skippedCount;
            }
            //Comparing number of actual not implemented tests and previously not implemented tests
            if ($incompleteCount > $this->_incompleteCount) {
                //Getting last not implemented test message if actual number of not implemented tests is greater than previous
                $notImplemented = $test->getTestResultObject()->notImplemented();
                $notImpl = end($notImplemented);
                $message .= $notImpl->exceptionMessage();
                $this->_incompleteCount = $incompleteCount;
            }
        }
        return $message;
    }

    /**
     * Test Failed
     */
    public function testFailed()
    {
        $this->_failed = true;
        $listener = self::$_listener;
        $test = ($listener) ? $listener->getCurrentTest() : null;
        $message = $this->getMessage();
        $tcmessage = sprintf("##teamcity[testFailed name='%s' message='%s']" . PHP_EOL,
                             get_class($test) . "::" . $test->getName(), $message);
        $this->write($tcmessage);
    }

    /**
     * Handler for 'testSkipped' event
     */
    public function testSkipped()
    {
        $this->_failed = true;
        $listener = self::$_listener;
        $test = ($listener) ? $listener->getCurrentTest() : null;
        $message = $this->getMessage();
        $tcmessage = sprintf("##teamcity[testIgnored name='%s' message='%s']" . PHP_EOL,
                             get_class($test) . "::" . $test->getName(), $message);
        $this->write($tcmessage);
    }

    /**
     * Handler for 'testIncomplete' event
     */
    public function testIncomplete()
    {
        $this->_failed = true;
        $listener = self::$_listener;
        $test = ($listener) ? $listener->getCurrentTest() : null;
        $message = $this->getMessage();
        $tcmessage = sprintf("##teamcity[testIgnored name='%s' message='%s']" . PHP_EOL,
                             get_class($test) . "::" . $test->getName(), $message);
        $this->write($tcmessage);
    }

    /**
     * Handler for 'endTest' event
     */
    public function endTest()
    {
        if ($this->_failed == false) {
            $listener = self::$_listener;
            $test = ($listener) ? $listener->getCurrentTest() : null;
            $message = sprintf("##teamcity[testFinished name='%s']" . PHP_EOL,
                               get_class($test) . "::" . $test->getName());
            $this->write($message);
        }
    }
}