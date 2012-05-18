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
require_once 'class-IXR.php';

class Mage_Testlink_Connector
{

    /**
     * Default server url. Should be overriden in phpunit.xml
     * @var string
     */
    public static $SERVER_URL = "http://localhost//testlink/lib/api/xmlrpc.php";

    /**
     * @var IXR_Client
     */
    private $_client;

    /**
     * Key used for getting connection to xml-rpc of testlink (all test cases will be signed from the user whoes id is used)
     *
     * @var string|null
     */
    public static $devKey = null;

    /**
     * Map of status codes for sending to testlink
     *
     * @var array
     */
    public static $tcaseStatusCode = array(
        'passed' => 'p',
        'blocked' => 'b',
        'failed' => 'f',
        'wrong' => 'w',
        'departed' => 'd'
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_client = new IXR_Client(Mage_Testlink_Connector::$SERVER_URL);
    }

    /**
     * Generates the report for sending to testlink. Sends it to testlink and returns array with the result
     *
     * @param string $tcaseexternalid
     * @param string $tplanid
     * @param string $status
     * @param string $buildid
     * @param string $notes
     *
     * @return array
     */
    public function report($tcaseexternalid, $tplanid, $status, $buildid=null, $notes=null)
    {
        return $this->reportResult($tcaseexternalid, $tplanid, $buildid, null, $status,
                                   $notes, null, null, null, false, false);
    }

    /**
     * Send the result of test execution of the test case
     *
     * @param string $tcaseexternalid
     * @param string $tplanid
     * @param string $buildid
     * @param string $buildname
     * @param string $status
     * @param string $notes
     * @param string $bugid
     * @param string $customfields
     * @param string $platformname
     * @param bool $overwrite
     * @param bool $debug
     *
     * @return array
     */
    protected function reportResult($tcaseexternalid=null, $tplanid, $buildid=null, $buildname=null,
                          $status, $notes=null, $bugid=null, $customfields=null, $platformname=null,
                          $overwrite=false, $debug=false)
    {
        $this->_client->debug = $debug;
        $data = array();
        $data["devKey"] = Mage_Testlink_Connector::$devKey;
        $data["testplanid"] = $tplanid;

        if (!is_null($bugid)) {
            $data["bugid"] = $bugid;
        }

        if (!is_null($tcaseexternalid)) {
            $data["testcaseexternalid"] = $tcaseexternalid;
        }

        if (!is_null($buildid)) {
            $data["buildid"] = $buildid;
        } elseif (!is_null($buildname)) {
            $data["buildname"] = $buildname;
        }

        if (!is_null($notes)) {
            $data["notes"] = $notes;
        }
        $data["status"] = $status;

        if (!is_null($customfields)) {
            $data["customfields"] = $customfields;
        }

        if (!is_null($platformname)) {
            $data["platformname"] = $platformname;
        }

        if (!is_null($overwrite)) {
            $data["overwrite"] = $overwrite;
        }

        if ($this->_client->query('tl.reportTCResult', $data)) {
            $response = $this->_client->getResponse();
        } else {
            $response = null;
        }
        return $response;
    }

    /**
     * Performs and action for the executed test case (i.e. sets the status of test case)
     *
     * @param string    $method
     * @param array     $args
     *
     * @return array
     */
    protected function action($method, $args)
    {
        $args["devKey"] = Mage_Testlink_Connector::$devKey;

        if (!$this->_client->query("tl.{$method}", $args)) {
            $response = null;
        } else {
            $response = $this->_client->getResponse();
        }
        return $response;
    }

    /**
     * Gets project's id
     *
     * @param string $name
     *
     * @return string
     */
    public function getProject($name)
    {
        if (!is_numeric($name)) {
            $method = 'getProjects';
            $args = array();
            $projects = $this->action($method, $args);
            if (!empty($projects)) {
                foreach ($projects as $project) {
                    if (isset($project["name"]) && ($project["name"] == $name)) {
                        return $id = isset($project["id"]) ? $project["id"] : null;
                    }
                }
            } else {
                return null;
            }
        } else {
            return $name;
        }
    }

    /**
     * Gets array of all tests plans in project
     *
     * @param string    $project_id
     *
     * @return array
     */
    protected function getTestPlans($project_id)
    {
        $plans = array();
        if (is_numeric($project_id)) {
            $method = 'getProjectTestPlans';
            $args = array();
            $args["testprojectid"] = $project_id;
            $plans = $this->action($method, $args);
        }
        return $plans;
    }

    /**
     * Gets the last test plan from project or searches for test plan by name or id
     *
     * @param string        $project_id
     * @param string|null   $testPlan
     *
     * @return array
     */
    public function getTestPlan($project_id, $testPlan=null)
    {
        $plans = $this->getTestPlans($project_id);
        if (isset($testPlan) && !empty($plans)) {
            if (is_numeric($testPlan)) {
                foreach ($plans as $plan) {
                    if (isset($plan['id']) && $plan['id'] == $testPlan) {
                        return $plan;
                    }
                }
            } else {
                foreach ($plans as $plan) {
                    if (isset($plan['name']) && $plan['name'] == $testPlan) {
                        return $plan;
                    }
                }
            }
        } else {
            return $plan = (!empty($plans)) ? $plans[count($plans) - 1] : array();
        }
    }

    /**
     * Gets array of all builds from the test plan
     *
     * @param string $testplan_id
     *
     * @return array
     */
    protected function getBuilds($testplan_id)
    {
        $method = 'getBuildsForTestPlan';
        $args = array();
        $args["testplanid"] = $testplan_id;
        return $this->action($method, $args);
    }

    /**
     * Gets current build
     *
     * @param string      $testplan_id
     * @param string|null $buildId
     *
     * @return array
     */
    public function getBuild($testplan_id, $buildId=null)
    {
        $builds = isset($testplan_id) ? $this->getBuilds($testplan_id) : array();
        if (!empty($builds)) {
            if (isset($buildId)) {
                foreach ($builds as $build) {
                    if (is_numeric($buildId)) {
                        if (isset($build['id']) && $build['id'] == $buildId) {
                            return $build;
                        } elseif (isset($build['name']) && $build['name'] == $buildId) {
                            return $build;
                        }
                    } else {
                        if (isset($build['name']) && $build['name'] == $buildId) {
                            return $build;
                        }
                    }
                }
            } else {
                return $builds[count($builds) -1];
            }
        }
        return array();
    }

    /**
     * Gets available tests from the test plan in testlink
     *
     * @param string $testplan_id
     *
     * @return array
     */
    protected function getTests($testplan_id)
    {
        $method = 'getTestCasesForTestPlan';
        $args = array();
        $args["testplanid"] = $testplan_id;
        return $this->action($method, $args);
    }
}