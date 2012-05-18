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
class Mage_Testlink_ListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Testlink_Listener
     */
    protected $_listenerMock;
    protected $_test;

    protected function setUp()
    {
        $this->_listenerMock = $this->getMock('Mage_Testlink_Listener',
                array("instantiateObservers", "notifyObservers"),
                array("Test Project", "url", "key", "testplan", "build"), "", true);
        $this->_listenerMock->expects($this->any())
                           ->method('instantiateObservers')
                ->will($this->returnValue('null'));
    }

    protected function tearDown()
    {

    }

    /**
     * @covers Mage_Testlink_Listener::getProject
     */
    public function testGetProjectName()
    {
        $this->assertEquals($this->_listenerMock->getProject(), "Test Project");
    }

    /**
     * @covers Mage_Testlink_Listener::getTestPlan
     */
    public function testGetTestPlan()
    {
        $this->assertEquals($this->_listenerMock->getTestPlan(), "testplan");
    }

    /**
     * @covers Mage_Testlink_Listener::getBuild
     */
    public function testGetBuild()
    {
        $this->assertEquals($this->_listenerMock->getBuild(), "build");
    }

    /**
     * @covers Mage_Testlink_Listener::getCurrentTest
     */
    public function testGetCurrentTest()
    {
        $test = new Mage_Selenium_TestCase();
        $this->_listenerMock->addError($test, new Exception(), time());
        $this->assertEquals($this->_listenerMock->getCurrentTest(), $test);
    }

    /**
     * @covers Mage_Testlink_Listener::addError
     */
    public function testAddError()
    {
        $test = new Mage_Selenium_TestCase();
        $this->_listenerMock->expects($this->once())->method('notifyObservers');
        $this->_listenerMock->addError($test, new Exception("TestException"), time());
    }

    /**
     * @covers Mage_Testlink_Listener::addFailure
     */
    public function testAddFailure()
    {
        $test = new Mage_Selenium_TestCase();
        $this->_listenerMock->expects($this->once())->method('notifyObservers');
        $this->_listenerMock->addFailure($test, new PHPUnit_Framework_AssertionFailedError("TestException"), time());
    }

    /**
     * @covers Mage_Testlink_Listener::addIncompleteTest
     */
    public function testAddIncompleteTest()
    {
        $test = new Mage_Selenium_TestCase();
        $this->_listenerMock->expects($this->once())->method('notifyObservers');
        $this->_listenerMock->addIncompleteTest($test, new Exception("TestException"), time());
    }

    /**
     * @covers Mage_Testlink_Listener::addSkippedTest
     */
    public function testAddSkippedTest()
    {
        $test = new Mage_Selenium_TestCase();
        $this->_listenerMock->expects($this->once())->method('notifyObservers');
        $this->_listenerMock->addSkippedTest($test, new Exception("TestException"), time());
    }

    /**
     * @covers Mage_Testlink_Listener::startTestSuite
     */
    public function testStartTestSuite()
    {
        $this->_listenerMock->expects($this->once())->method('notifyObservers');
        $this->_listenerMock->startTestSuite(new PHPUnit_Framework_TestSuite());
    }

    /**
     * @covers Mage_Testlink_Listener::endTestSuite
     */
    public function testEndTestSuite()
    {
        $this->_listenerMock->expects($this->once())->method('notifyObservers');
        $this->_listenerMock->endTestSuite(new PHPUnit_Framework_TestSuite());
    }

    /**
     * @covers Mage_Testlink_Listener::startTest
     */
    public function testStartTest()
    {
        $test = new Mage_Selenium_TestCase();
        $this->_listenerMock->expects($this->once())->method('notifyObservers');
        $this->_listenerMock->startTest($test);
    }

    /**
     * @covers Mage_Testlink_Listener::endTest
     */
    public function testEndTest()
    {
        $test = new Mage_Selenium_TestCase();
        $this->_listenerMock->expects($this->once())->method('notifyObservers');
        $this->_listenerMock->endTest($test, time());
    }
}