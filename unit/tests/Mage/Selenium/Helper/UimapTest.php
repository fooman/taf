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
class Mage_Selenium_Helper_UimapTest extends Mage_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_Helper_Uimap::__construct
     */
    public function test__construct()
    {
        $uimapHelper = new Mage_Selenium_Helper_Uimap($this->_config);
        $this->assertInstanceOf('Mage_Selenium_Helper_Uimap', $uimapHelper);
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimap
     */
    public function testGetUimap()
    {
        $uimapHelper = new Mage_Selenium_Helper_Uimap($this->_config);

        $uimap = $uimapHelper->getUimap('admin');
        $this->assertInternalType('array', $uimap);
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimap
     *
     * @expectedException OutOfRangeException
     */
    public function testGetUimapException()
    {
        $uimapHelper = new Mage_Selenium_Helper_Uimap($this->_config);
        $uimap = $uimapHelper->getUimap('invalid_area');
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimapPage
     */
    public function testGetUimapPage()
    {
        $uimapHelper = new Mage_Selenium_Helper_Uimap($this->_config);

        $uipage = $uimapHelper->getUimapPage('admin', 'create_customer');
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);

        $uipage = $uimapHelper->getUimapPage('admin', 'wrong_name');
        $this->assertNull($uipage);
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getUimapPageByMca
     */
    public function testGetUimapPageByMca()
    {
        $uimapHelper = new Mage_Selenium_Helper_Uimap($this->_config);

        $uipage = $uimapHelper->getUimapPageByMca('admin', 'customer/new/');
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);

        $uipage = $uimapHelper->getUimapPageByMca('admin', '');
        $this->assertInstanceOf('Mage_Selenium_Uimap_Page', $uipage);

        $uipage = $uimapHelper->getUimapPageByMca('admin', 'wrong-path');
        $this->assertNull($uipage);
    }

    /**
     * @covers Mage_Selenium_Helper_Uimap::getMainForm
     */
    public function testGetMainForm()
    {
        $uipage = $this->getUimapPage('admin', 'create_customer');
        $mainForm = $uipage->getMainForm();
        $this->assertInstanceOf('Mage_Selenium_Uimap_Form', $mainForm);
    }
}