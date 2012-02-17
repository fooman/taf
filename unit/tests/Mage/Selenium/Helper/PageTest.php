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
class Mage_Selenium_Helper_PageTest extends Mage_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_Helper_Page::setApplicationHelper
     */
    public function testSetApplicationHelper()
    {
        $pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $appHelper = new Mage_Selenium_Helper_Application($this->_config);
        $this->assertInstanceOf('Mage_Selenium_Helper_Page', $pageHelper->setApplicationHelper($appHelper));
    }

    /**
     * @covers Mage_Selenium_Helper_Page::getPageUrl
     */
    public function testGetPageUrl()
    {
        $pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $appHelper = new Mage_Selenium_Helper_Application($this->_config);
       // $appHelper->setArea('frontend');
        $pageHelper->setApplicationHelper($appHelper);
        $this->assertStringEndsWith('home', $pageHelper->getPageUrl('frontend','home'));
    }

    /**
     * @covers Mage_Selenium_Helper_Page::getPageUrl
     */
    public function testGetPageUrlUninitializedException()
    {
        $pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $this->setExpectedException('Mage_Selenium_Exception', "ApplicationHelper hasn't been initialized yet");
        $this->assertStringEndsWith('/control/permissions_user/', $pageHelper->getPageUrl('admin','manage_admin_users'));
    }

    /**
     * @covers Mage_Selenium_Helper_Page::getPageUrl
     */
    public function testGetPageUrlEmptyPageException()
    {
        $pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $appHelper = new Mage_Selenium_Helper_Application($this->_config);
        //$appHelper->setArea('admin');
        $pageHelper->setApplicationHelper($appHelper);

        $this->setExpectedException('Mage_Selenium_Exception', 'Page data is not defined');
        $pageHelper->getPageUrl('admin','');
    }

    /**
     * @covers Mage_Selenium_Helper_Page::getPageUrl
     * @expectedException OutOfRangeException
     */
    public function testGetPageUrlWrongAreaException()
    {
        $pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $appHelper = new Mage_Selenium_Helper_Application($this->_config);
        //$appHelper->setArea('admin-bla-bla-bla');
        $pageHelper->setApplicationHelper($appHelper);
        $this->setExpectedException('Mage_Selenium_Exception', 'Page data is not defined');
        $this->assertFalse($pageHelper->getPageUrl('admin-bla-bla-bla','some_page'));
    }

    /**
     * @covers Mage_Selenium_Helper_Page::getPageUrl
     * @expectedException Mage_Selenium_Exception
     */
    public function testGetPageUrlWrongUrlException()
    {
        $pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $appHelper = new Mage_Selenium_Helper_Application($this->_config);
        $appHelper->setArea('admin');
        $pageHelper->setApplicationHelper($appHelper);

        $this->assertFalse($pageHelper->getPageUrl('admin','some_page'));
    }
}