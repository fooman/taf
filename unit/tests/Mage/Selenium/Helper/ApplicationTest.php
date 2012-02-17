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
 * @package     selenium unit tests
 * @subpackage  Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Helper_ApplicationTest extends Mage_PHPUnit_TestCase
{
    public function test__construct()
    {
        $applicationHelper = new Mage_Selenium_Helper_Application($this->_config);
        $this->assertInstanceOf('Mage_Selenium_Helper_Application', $applicationHelper);
    }

    /**
     * @covers Mage_Selenium_Helper_Application::setArea
     * @depends test__construct
     */
    public function testSetArea()
    {
        $applicationHelper = new Mage_Selenium_Helper_Application($this->_config);
        $this->assertInstanceOf('Mage_Selenium_Helper_Application', $applicationHelper->setArea('frontend'));
        $this->assertInstanceOf('Mage_Selenium_Helper_Application', $applicationHelper->setArea('admin'));
    }

    /**
     * @covers Mage_Selenium_Helper_Application::setArea
     * @depends test__construct
     */
    public function testSetAreaOutOfRangeException()
    {
        $applicationHelper = new Mage_Selenium_Helper_Application($this->_config);
        $this->setExpectedException('OutOfRangeException');
        $applicationHelper->setArea('invalid-area');
    }

    /**
     * @covers Mage_Selenium_Helper_Application::getArea
     * @depends test__construct
     */
    public function testGetArea()
    {
        $applicationHelper = new Mage_Selenium_Helper_Application($this->_config);
        $applicationHelper->setArea('frontend');
        $this->assertInternalType('string', $applicationHelper->getArea());
        $this->assertNotEmpty($applicationHelper->getArea());
        $this->assertEquals('frontend', $applicationHelper->getArea());
    }

    /**
     * @covers Mage_Selenium_Helper_Application::getBaseUrl
     * @depends test__construct
     */
    public function testGetBaseUrl()
    {
        $applicationHelper = new Mage_Selenium_Helper_Application($this->_config);
        $this->assertInternalType('string', $applicationHelper->getBaseUrl());
        $this->assertNotEmpty($applicationHelper->getBaseUrl());

        $applicationHelper->setArea('admin');
        $this->assertRegExp('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \?=.-]*)*\/?$/', $applicationHelper->getBaseUrl());

        $applicationHelper->setArea('frontend');
        $this->assertRegExp('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \?=.-]*)*\/?$/', $applicationHelper->getBaseUrl());
    }

    /**
     * @covers Mage_Selenium_Helper_Application::isAdmin
     * @depends test__construct
     */
    public function testIsAdmin()
    {
        $applicationHelper = new Mage_Selenium_Helper_Application($this->_config);

        $applicationHelper->setArea('frontend');
        $this->assertFalse($applicationHelper->isAdmin());

        $applicationHelper->setArea('admin');
        $this->assertTrue($applicationHelper->isAdmin());
    }

    /**
     * @covers Mage_Selenium_Helper_Application::getDefaultAdminUsername
     * @depends test__construct
     */
    public function testGetDefaultAdminUsername()
    {
        $applicationHelper = new Mage_Selenium_Helper_Application($this->_config);
        $this->assertInternalType('string', $applicationHelper->getDefaultAdminUsername());
        $this->assertNotEmpty($applicationHelper->getDefaultAdminUsername());
    }

    /**
     * @covers Mage_Selenium_Helper_Application::getDefaultAdminPassword
     * @depends test__construct
     */
    public function testGetDefaultAdminPassword()
    {
        $applicationHelper = new Mage_Selenium_Helper_Application($this->_config);
        $this->assertInternalType('string', $applicationHelper->getDefaultAdminPassword());
        $this->assertNotEmpty($applicationHelper->getDefaultAdminPassword());
    }
}
?>
