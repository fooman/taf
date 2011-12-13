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
 * Parameters helper class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Helper_Params
{

    /**
     * Parameters array
     *
     * @var array
     */
    protected $_paramsArray = array();

    /**
     * Class constructor
     *
     * @param array $params Parameters array
     */
    public function __construct(array $params = null)
    {
        if (!empty($params)) {
            foreach ($params as $paramName => $paramValue) {
                $this->setParameter($paramName, $paramValue);
            }
        }
    }

    /**
     * Set Xpath parameter
     *
     * @param string $name Parameter name
     * @param string $value Parameter value (null to unset)
     */
    public function setParameter($name, $value)
    {
        $key = '%' . $name . '%';
        if ($value === null) {
            unset($this->_paramsArray[$key]);
        } else {
            $this->_paramsArray[$key] = $value;
        }
    }

    /**
     * Get Xpath parameter
     *
     * @param string $name Parameter name
     *
     * @return string
     */
    public function getParameter($name)
    {
        $key = '%' . $name . '%';
        return isset($this->_paramsArray[$key]) ? $this->_paramsArray[$key] : false;
    }

    /**
     * Populate string with parameter values
     *
     * @param string $source Source string
     *
     * @return string
     */
    public function replaceParameters($source)
    {
        if (empty($this->_paramsArray) || !is_string($source) || empty($source)) {
            return $source;
        } else {
            return str_replace(array_keys($this->_paramsArray), array_values($this->_paramsArray), $source);
        }
    }

    /**
     * Populate string with Regexp for next matching
     *
     * @param string $source Source string
     * @param string $regexp Regular expression (by default = '(.*?)')
     *
     * @return string
     */
    public function replaceParametersWithRegexp($source, $regexp = '([^\/]+?)'/* '(.*?)' */)
    {
        if (empty($this->_paramsArray)) {
            return $source;
        } else {
            return str_replace(array_keys($this->_paramsArray), $regexp, $source);
        }
//       return preg_replace('/%([^\/]+?)%/', $regexp, $source);
    }

}
