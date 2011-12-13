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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Application helper
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Helper_Application extends Mage_Selenium_Helper_Abstract
{

    /**
     * Current application area
     *
     * @var string
     */
    protected $_area = '';

    /**
     * Application information:
     * array(
     *      'frontendUrl'   => '',
     *      'adminUrl'      => '',
     *      'adminLogin'    => '',
     *      'adminPassword' => '',
     *      'basePath'      => ''
     * )
     *
     * @var array
     */
    protected $_appInfo = array();

    /**
     * Set current application area
     *
     * @param string $area Possible values are 'frontend' and 'admin'
     *
     * @return Mage_Selenium_Helper_Application
     */
    public function setArea($area)
    {
        if (!in_array($area, array('admin', 'frontend'))) {
            throw new OutOfRangeException();
        }
        $this->_area = $area;
        return $this;
    }

    /**
     * Get current application area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->_area;
    }

    /**
     * Get base url of current area
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $url = $this->isAdmin()
                ? $this->_appInfo['adminUrl']
                : $this->_appInfo['frontendUrl'];
        return $url;
    }

    /**
     * Get base path for application
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->_appInfo['basePath'];
    }

    /**
     * Change Application information
     *
     * @param string $configName
     */
    public function changeAppInfo($configName)
    {
        $applications = $this->_config->getConfigValue('applications');
        $this->_appInfo = $applications[$configName];
    }

    /**
     * Initializes Application information
     *
     * @return Mage_Selenium_Helper_Application
     */
    protected function _init()
    {
        $applications = $this->_config->getConfigValue('applications');
        $this->_appInfo = $applications['default'];
        return parent::_init();
    }

    /**
     * Checks if the current area is admin
     *
     * @return boolean
     */
    public function isAdmin()
    {
        if ('admin' == $this->_area) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve default admin user username
     *
     * @return string
     */
    public function getDefaultAdminUsername()
    {
        return $this->_appInfo['adminLogin'];
    }

    /**
     * Retrieve default admin user password
     *
     * @return string
     */
    public function getDefaultAdminPassword()
    {
        return $this->_appInfo['adminPassword'];
    }

}
