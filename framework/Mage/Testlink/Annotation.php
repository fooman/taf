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
 * Implementation of the @TestlinkId doc comment directive
 */
class Mage_Testlink_Annotation
{

    /**
     * @var Mage_Testlink_Listener
     */
    protected $_listener;

    /**
     * @var Mage_Testlink_Connector
     */
    protected $_testlink;

    /**
     * Test plan properties (id, name, notes, active, is_public, testproject_id)
     *
     * @var array
     */
    protected $_testplan;

    /**
     * Build properties (id, testplan_id, name, notes, active, is_open, release_date, closed_on_date)
     *
     * @var array
     */
    protected $_build;

    /**
     * Variable for definition of status of the last test case (used for not changing status from fail to pass
     * when test is using dataprovider)
     *
     * @var bool
     */
    protected $_failed;

    /**
     * Name of the last test (used for tests with dataprovider)
     *
     * @var string
     */
    protected $_lastTest;

    /**
     * Counter of errors
     *
     * @var int
     */
    protected $_errorsCount = 0;

    /**
     * Counter of failures
     *
     * @var int
     */
    protected $_failuresCount = 0;

    /**
     * Constructs Annotation
     *
     * @param Mage_Testlink_Listener $listener
     */
    public function __construct(Mage_Testlink_Listener $listener)
    {
        $this->setListener($listener);
        $project = $listener->getProject();
        if (isset($project)) {
            $this->setTestlink(new Mage_Testlink_Connector());
            $projectId = $this->_testlink->getProject($project);
            $testPlan = $listener->getTestPlan();
            $this->setTestplan($this->_testlink->getTestPlan($projectId, $testPlan));
            $testPlanId = (isset($this->_testplan['id'])) ? $this->_testplan['id'] : null;
            $this->setBuild($this->_testlink->getBuild($testPlanId, $listener->getBuild()));
        } else {
            return;
        }
    }

    /**
     * Sets protected variable $listener
     *
     * @param Mage_Testlink_Listener $listener
     *
     * @return Mage_Testlink_Annotation
     */
    protected function setListener($listener)
    {
        $this->_listener = $listener;
        return $this;
    }

    /**
     * Gets protected variable $listener
     *
     * @return Mage_Testlink_Listener|null
     */
    protected function getListener()
    {
        return $this->_listener;
    }

    /**
     * Sets protected variable $testlink
     *
     * @param Mage_Testlink_Connector $testlink
     *
     * @return Mage_Testlink_Annotation
     */
    protected function setTestlink($testlink)
    {
        $this->_testlink = $testlink;
        return $this;
    }

    /**
     * Gets protected variable $testlink
     *
     * @return Mage_Testlink_Connector|null
     */
    protected function getTestlink()
    {
        return $this->_testlink;
    }

    /**
     * Sets test plan
     *
     * @param array $testplan
     * @return Mage_Testlink_Annotation
     */
    protected function setTestplan($testplan)
    {
        $this->_testplan = $testplan;
        return $this;
    }

    /**
     * Get test plan
     *
     * @return array
     */
    protected function getTestplan()
    {
        return $this->_testplan;
    }

    /**
     * Sets build
     *
     * @param array $build
     *
     * @return Mage_Testlink_Annotation
     */
    protected function setBuild($build)
    {
        $this->_build = $build;
        return $this;
    }

    /**
     * Gets build
     *
     * @return array
     */
    protected function getBuild()
    {
        return $this->_build;
    }

    /**
     * Retrieve fixtures from annotation
     *
     * @param string $scope 'class' or 'method'
     * @return array|null
     */
    protected function getFixtures($scope)
    {
        $listener = $this->getListener();
        $tests = ($listener) ? $listener->getCurrentTest() : null;
        $annotations = ($tests) ? $tests->getAnnotations() : null;
        if (!empty($annotations[$scope]['TestlinkId'])) {
            return $annotations[$scope]['TestlinkId'];
        }
        return null;
    }

    /**
     * Handler for 'endTestSuite' event
     */
    public function endTestSuite()
    {

    }

    /**
     * Handler for 'startTest' event
     */
    public function startTest()
    {
        $listener = $this->getListener();
        $test = ($listener) ? $listener->getCurrentTest(): null;
        if ($test != null) {
            if ($this->_lastTest != $test->getName(false)) {
                $this->_failed = false;
            }
            $this->_lastTest = $test->getName(false);
        }
    }

    /**
     * Generates message from last failures and errors
     *
     * @return string
     */
    protected function getMessage()
    {
        $listener = $this->getListener();
        $test = ($listener) ? $listener->getCurrentTest() : null;
        $message = '';
        if ($test != null) {
            $message = get_class($test) . ":" . $test->getName() . "\n";
            $errorsCount = $test->getTestResultObject()->errorCount();
            $failuresCount = $test->getTestResultObject()->failureCount();
            if ($errorsCount > $this->_errorsCount) {
                $errors = $test->getTestResultObject()->errors();
                $error = end($errors);
                $message .= $error->exceptionMessage();
                $this->_errorsCount = $errorsCount;
            }
            if ($failuresCount > $this->_failuresCount) {
                $fails = $test->getTestResultObject()->failures();
                $fail = end($fails);
                $message .= $fail->exceptionMessage();
                $this->_failuresCount = $failuresCount;
            }
        }
        return $message;
    }

    /**
     * Handler for 'testFailed' event
     */
    public function testFailed()
    {
        $this->_failed = true;
        $message = $this->getMessage();
        $tcids = $this->getFixtures('method');
        if (!$tcids) {
            return;
        }
        foreach ($tcids as $tcid) {
            if (strpos($tcid, ':') > 0) {
                $tcid = substr($tcid, 0, strpos($tcid, ':'));
            }
            $testlink = $this->getTestlink();
            if ($testlink) {
                $testlink->report($tcid, $this->_testplan['id'],
                                    Mage_Testlink_Connector::$tcaseStatusCode['failed'],
                                    $this->_build['id'],
                                    $message);
            }
        }
    }

    /**
     * Handler for 'testSkipped' event
     */
    public function testSkipped()
    {
        $message = $this->getMessage();
        $tcids = $this->getFixtures('method');
        if (!$tcids) {
            return;
        }
        foreach ($tcids as $tcid) {
            if (strpos($tcid, ':') > 0) {
                $tcid = substr($tcid, 0, strpos($tcid, ':'));
            }
            $testlink = $this->getTestlink();
            if ($testlink) {
                $testlink->report($tcid, $this->_testplan['id'],
                                    Mage_Testlink_Connector::$tcaseStatusCode['blocked'],
                                    $this->_build['id'],
                                    $message);
            }
        }
    }

    /**
     * Handler for 'endTest' event
     */
    public function endTest()
    {
        if ($this->_failed == false) {
            $tcids = $this->getFixtures('method');
            if (!$tcids) {
                return;
            }
            foreach ($tcids as $tcid) {
                if (strpos($tcid, ':') > 0) {
                    $tcid = substr($tcid, 0, strpos($tcid, ':'));
                }
                $testlink = $this->getTestlink();
                if ($testlink) {
                    $testlink->report($tcid, $this->_testplan['id'],
                                    Mage_Testlink_Connector::$tcaseStatusCode['passed'],
                                    $this->_build['id'],
                                    get_class($this->getListener()->getCurrentTest()) . ":" .
                                    $this->getListener()->getCurrentTest()->getName() . "\n");
                }
            }
        }
    }
}