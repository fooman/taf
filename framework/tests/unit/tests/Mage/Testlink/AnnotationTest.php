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
class Mage_Testlink_AnnotationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Testlink_AnnotationMock
     */
    protected $_annotationMock;

    /**
     * @var Mage_Testlink_Listener
     */
    protected $_listenerMock;

    /**
     * @var Mage_Testlink_Connector
     */
    protected $_connectorMock;

    /**
     * @var PHPUnit_Framework_TestCase
     */
    protected $_testCaseMock;

    /**
     * @var PHPUnit_Framework_TestResult
     */
    protected $_testResultMock;

    protected function setUp()
    {
        $this->_connectorMock = $this->getMock('Mage_Testlink_Connector', array(), array(), '', false);
        $this->_testCaseMock = $this->getMock('PHPUnit_Framework_TestCase', array(), array(), '', false);
        $this->_listenerMock = $this->getMock('Mage_Testlink_Listener', array(), array(), '', false);
        $this->_listenerMock->expects($this->any())->method('getCurrentTest')
                ->will($this->returnValue($this->_testCaseMock));
        $this->assertTrue(class_exists('Mage_Testlink_AnnotationMock'));
        $this->_annotationMock = new Mage_Testlink_AnnotationMock();
        $this->_testResultMock = $this->getMock('PHPUnit_Framework_TestResult',
                                               array('__construct', 'getTestResultObject'), array(), '', false);
        $this->_testCaseMock->expects($this->any())->method('getTestResultObject')
                ->will($this->returnValue($this->_testResultMock));
    }

    /**
     * @covers Mage_Testlink_Annotation::startTest
     */
    public function testStartTest()
    {
        $this->_annotationMock->__call('setListener', array($this->_listenerMock));
        $this->_listenerMock->expects($this->any())->method('getCurrentTest')
                        ->will($this->returnValue($this->_testCaseMock));
        $this->assertNull($this->_annotationMock->__call('startTest', array()));

    }
    /**
     * @covers Mage_Testlink_Annotation::testFailed
     */
    public function testTestFailed()
    {
        $this->_testCaseMock->expects($this->never())->method('getAnnotations');
        $this->_connectorMock->expects($this->never())->method('report');
        $this->assertNull($this->_annotationMock->testFailed());

    }

    /**
     * @covers Mage_Testlink_Annotation::testSkipped
     */
    public function testTestSkipped()
    {
        $this->_testCaseMock->expects($this->never())->method('getAnnotations');
        $this->_connectorMock->expects($this->never())->method('report');
        $this->assertNull($this->_annotationMock->testSkipped());
    }

    /**
     * @covers Mage_Testlink_Annotation::endTest
     */
    public function testEndTest()
    {
        $this->_testCaseMock->expects($this->never())->method('getAnnotations');
        $this->_connectorMock->expects($this->never())->method('report');
        $this->assertNull($this->_annotationMock->endTest());
    }
}
