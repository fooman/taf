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
 * Uimap helper class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Helper_Uimap extends Mage_Selenium_Helper_Abstract
{

    /**
     * File helper instance
     *
     * @var Mage_Selenium_Helper_File
     */
    protected $_fileHelper = null;

    /**
     * Uimap data
     *
     * @var array
     */
    protected $_uimapData = array();

    /**
     * Class constructor
     *
     * @param Mage_Selenium_TestConfiguration $config Test configuration
     */
    public function __construct(Mage_Selenium_TestConfiguration $config)
    {
        parent::__construct($config);

        $this->_fileHelper = new Mage_Selenium_Helper_File($this->_config);
        $this->_loadUimapData('admin');
        $this->_loadUimapData('frontend');
    }

    /**
     * Load and merge data files
     *
     * @param string $area Application area ('frontend'|'admin')
     *
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _loadUimapData($area)
    {
        $files = SELENIUM_TESTS_BASEDIR
                . DIRECTORY_SEPARATOR
                . 'uimaps'
                . DIRECTORY_SEPARATOR
                . $area
                . DIRECTORY_SEPARATOR
                . '*.yml';

        $pages = $this->_fileHelper->loadYamlFiles($files);
        foreach ($pages as $pageKey => $pageContent) {
            if (!empty($pageContent)) {
                $this->_uimapData[$area][$pageKey] = new Mage_Selenium_Uimap_Page($pageKey, $pageContent);
            }
        }

        return $this;
    }

    /**
     * Retrieve array with uimap data
     *
     * @param string $area Application area ('frontend'|'admin')
     *
     * @return array
     */
    public function &getUimap($area)
    {
        if (!array_key_exists($area, $this->_uimapData)) {
            throw new OutOfRangeException();
        }

        return $this->_uimapData[$area];
    }

    /**
     * Retrieve Page from uimap data configuration by path
     *
     * @param string $area Application area ('frontend'|'admin')
     * @param string $pageKey UIMap page key
     * @param Mage_Selenium_Helper_Params $paramsDecorator Params decorator instance
     *
     * @return Mage_Selenium_Uimap_Page
     */
    public function getUimapPage($area, $pageKey, $paramsDecorator = null)
    {
        $page = isset($this->_uimapData[$area][$pageKey]) ? $this->_uimapData[$area][$pageKey] : null;
        if ($page && $paramsDecorator) {
            $page->assignParams($paramsDecorator);
        }
        return $page;
    }

    /**
     * Retrieve Page from uimap data configuration by MCA
     *
     * @param string $area Application area ('frontend'|'admin')
     * @param string $pageKey UIMap page key
     * @param Mage_Selenium_Helper_Params $paramsDecorator Params decorator instance
     *
     * @return Mage_Selenium_Uimap_Page|Null
     */
    public function getUimapPageByMca($area, $mca, $paramsDecorator = null)
    {
        $mca = trim($mca, ' /\\');
        if (isset($this->_uimapData[$area])) {
            foreach ($this->_uimapData[$area] as &$page) {
                // get mca without any modifications
                $page_mca = trim($page->getMca(new Mage_Selenium_Helper_Params()), ' /\\');
                if ($page_mca !== false && $page_mca !== null) {
                    if ($paramsDecorator) {
                        $page_mca = $paramsDecorator->replaceParametersWithRegexp($page_mca);
                    }
                    if (preg_match(';^' . $page_mca . '$;', $mca)) {
                        $page->assignParams($paramsDecorator);
                        return $page;
                    }
                }
            }
        }
    }

}
