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
     * Path to testing applications config
     */
    const XPATH_APPLICATIONS = 'default/applications';

    /**
     * Default testing application
     */
    const DEFAULT_APPLICATION = 'default';

    /**
     * Default area id
     */
    const DEFAULT_AREA = 'frontend';

    /**
     * Configs for all applications
     * @var array
     */
    protected $_applicationsConfig = array();

    /**
     * List of all application names
     * @var array
     */
    protected $_applicationNames = array();

    /**
     * Config for current application
     * @var array
     */
    protected $_applicationConfig = array();

    /**
     * Name of current application
     * @var string
     */
    protected $_application;

    /**
     * Area information
     * @var array
     */
    protected $_areaConfig;

    /**
     * Current area
     * @var string
     */
    protected static $_area;

    /**
     * Initialize application
     * @return Mage_Selenium_Helper_Application
     */
    protected function _init()
    {
        $this->changeApplication(self::DEFAULT_APPLICATION);
        $this->setArea(self::DEFAULT_AREA);
        return parent::_init();
    }

    /**
     * Return config applications section
     * @return array
     */
    public function getApplicationsConfig()
    {
        if (!$this->_applicationsConfig) {
            $this->_applicationsConfig = $this->getConfig()->getConfigValue(self::XPATH_APPLICATIONS);
        }
        return $this->_applicationsConfig;
    }

    /**
     * Return list of application names
     * @return array
     */
    public function getApplicationNames()
    {
        if (!$this->_applicationNames) {
            $this->_applicationNames = array_keys($this->getApplicationsConfig());
        }
        return $this->_applicationNames;
    }

    /**
     * Change current application
     *
     * @param  string $name
     *
     * @return Mage_Selenium_Helper_Application
     * @throws OutOfRangeException
     */
    public function changeApplication($name)
    {
        $config = $this->getApplicationsConfig();
        if (!isset($config[$name])) {
            throw new OutOfRangeException('Application with the ' . $name . ' name is absent');
        }

        $this->_applicationConfig = $config[$name];
        $this->_application = $name;

        return $this;
    }

    /**
     * Return current application config
     * @return array
     */
    public function getApplicationConfig()
    {
        return $this->_applicationConfig;
    }

    /**
     * Return current application
     * @return string
     */
    public function getApplication()
    {
        return $this->_application;
    }

    /**
     * Return config areas section
     * @return array
     */
    public function getAreasConfig()
    {
        $config = $this->getApplicationConfig();
        return $config['areas'];
    }

    /**
     * Return list of areas
     * @return array
     */
    public function getAreas()
    {
        $config = $this->getAreasConfig();
        return array_keys($config);
    }

    /**
     * Return area config
     * @return array
     */
    public function getAreaConfig()
    {
        return $this->_areaConfig;
    }

    /**
     * Return current area
     * @return string
     */
    public function getArea()
    {
        return self::$_area;
    }

    /**
     * Set current area
     *
     * @param  string $name
     *
     * @return Mage_Selenium_Helper_Application
     * @throws OutOfRangeException
     */
    public function setArea($name)
    {
        $config = $this->getAreasConfig();
        if (!isset($config[$name])) {
            throw new OutOfRangeException('Area with the name ' . $name . ' is absent');
        }

        $this->_areaConfig = $config[$name];
        self::$_area = $name;

        return $this;
    }

    /**
     * Get base url of current area
     * @return string
     */
    public function getBaseUrl()
    {
        $config = $this->getAreaConfig();
        return isset($config['url']) ? $config['url'] : null;
    }

    /**
     * Get base path for application
     * @return string
     */
    public function getBasePath()
    {
        $config = $this->getApplicationConfig();
        return isset($config['basePath']) ? $config['basePath'] : null;
    }

    /**
     * Retrieve default admin user name
     * @return string
     */
    public function getDefaultAdminUsername()
    {
        $config = $this->getApplicationConfig();
        return isset($config['adminLogin']) ? $config['adminLogin'] : null;
    }

    /**
     * Retrieve default admin user password
     * @return string
     */
    public function getDefaultAdminPassword()
    {
        $config = $this->getApplicationConfig();
        return isset($config['adminPassword']) ? $config['adminPassword'] : null;
    }

    /**
     * Change application information
     * @deprecated @see $this->changeApplication()
     *
     * @param  string $name
     *
     * @return Mage_Selenium_Helper_Application
     */
    public function changeAppInfo($name)
    {
        return $this->changeApplication($name);
    }

    /**
     * Checks if the current area is admin
     * @return boolean
     */
    public function isAdmin()
    {
        if ('admin' == self::$_area) {
            return true;
        }
        return false;
    }
}
