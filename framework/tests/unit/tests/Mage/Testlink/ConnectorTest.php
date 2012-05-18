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
 * @package     testlink unit tests
 * @subpackage  Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Testlink_ConnectorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Testlink_Connector
     */
    protected $_connector;

    protected function setUp()
    {
        Mage_Testlink_Connector::$devKey = "12312341234sdfgsdfgsdf";
        $this->_connector = new Mage_Testlink_Connector;
    }

    protected function tearDown()
    {

    }

    /**
     * @covers Mage_Testlink_Connector::report
     */
    public function testReport()
    {

        $report = $this->_connector->report("71", "72", "f");
        $this->assertNull($report);
    }

    /**
     * @covers Mage_Testlink_Connector::getProject
     *
     * @dataProvider getProjectDataProvider
     */
    public function testGetProject($project)
    {
        $proj = $this->_connector->getProject($project);
        if (is_numeric($project)) {
            $this->assertEquals($proj, $project);
        } else {
            $this->assertNull($proj);
        }
    }

    public function getProjectDataProvider()
    {
        return array(
            array(null),
            array("null"),
            array(false),
            array(true),
            array("name"),
            array("72"),
            array("72:23423:1234"),
            array("72.1234123.1234"),
        );
    }

    /**
     * @covers Mage_Testlink_Connector::getTestPlan
     *
     * @dataProvider getTestPlanDataProvider
     */
    public function testGetTestPlan($testplan)
    {
        $tp = $this->_connector->getProject($testplan);
        if (is_numeric($testplan)) {
            $this->assertEquals($tp, $testplan);
        } else {
            $this->assertNull($tp);
        }
    }

    public function getTestPlanDataProvider()
    {
        return array(
            array(null),
            array(null, false),
            array("null"),
            array("null", false),
            array(false),
            array(false, true),
            array(true),
            array(true, false),
            array("name"),
            array("name", '77'),
            array("72"),
            array("72", '77'),
            array("72:23423:1234"),
            array("72:23423:1234", 'name'),
            array("72.1234123.1234"),
            array("72.1234123.1234", 'name'),
        );
    }

    /**
     * @covers Mage_Testlink_Connector::getBuild
     *
     * @dataProvider getBuildDataProvider
     */
    public function testGetBuild($build)
    {
        $b = $this->_connector->getProject($build);
        if (is_numeric($build)) {
            $this->assertEquals($b, $build);
        } else {
            $this->assertNull($b);
        }
    }

    public function getBuildDataProvider()
    {
        return array(
            array(null),
            array(null, false),
            array("null"),
            array("null", false),
            array(false),
            array(false, true),
            array(true),
            array(true, false),
            array("name"),
            array("name", '77'),
            array("72"),
            array("72", '77'),
            array("72:23423:1234"),
            array("72:23423:1234", 'name'),
            array("72.1234123.1234"),
            array("72.1234123.1234", 'name'),
        );
    }
}