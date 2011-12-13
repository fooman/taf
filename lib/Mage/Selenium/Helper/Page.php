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
 * Page helper object
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Helper_Page extends Mage_Selenium_Helper_Abstract
{

    /**
     * Current page
     *
     * @var string
     */
    protected $_currentPage = '';

    /**
     * Application helper instance
     *
     * @var Mage_Selenium_Helper_Application
     */
    protected $_applicationHelper = null;

    /**
     * Set Application helper instance to access application info
     *
     * @param Mage_Selenium_Helper_Application $applicationHelper Application helper instance
     *
     * @return Mage_Selenium_AbstractHelper
     */
    public function setApplicationHelper(Mage_Selenium_Helper_Application $applicationHelper)
    {
        $this->_applicationHelper = $applicationHelper;
        return $this;
    }

    /**
     * Return URL of a specified page
     *
     * @param string $page Page identifier
     *
     * @return string
     */
    public function getPageUrl($page)
    {
        if (!$this->_applicationHelper) {
            throw new Mage_Selenium_Exception("ApplicationHelper hasn't inited yet");
        }

        $pageData = $this->_config->getUimapHelper()->getUimapPage($this->_applicationHelper->getArea(), $page);

        if (empty($pageData)) {
            throw new Mage_Selenium_Exception('Page data is not defined');
        }
        $url = $this->_applicationHelper->getBaseUrl() . $pageData->getMca();
        return $url;
    }

    /**
     * Return xpath which we need to click to open page
     *
     * @param string $page Page identifier
     *
     * @return string
     */
    public function getPageClickXpath($page)
    {
        if (!$this->_applicationHelper) {
            throw new Mage_Selenium_Exception('ApplicationHelper hasn\'t inited yet');
        }

        $pageData = $this->_config
                            ->getUimapHelper()
                                ->getUimapPage($this->_applicationHelper->getArea(), $page);

        if (empty($pageData)) {
            throw new Mage_Selenium_Exception('Page data is not defined');
        }

        return $pageData->getClickXpath();
    }

    /**
     * Convert page MCA to page ID
     *
     * @param string Page's mca
     * @param Mage_Selenium_Helper_Params $paramsDecorator Params decorator instance
     *
     * @return string Page identifier
     */
    public function getPageByMca($mca, $paramsDecorator = null)
    {
        if (!$this->_applicationHelper) {
            throw new Mage_Selenium_Exception("ApplicationHelper hasn't inited yet");
        }

        return $this->_config
                        ->getUimapHelper()
                            ->getUimapPageByMca($this->_applicationHelper->getArea(), $mca, $paramsDecorator);
    }

    /**
     * Returns PageID of current page
     *
     * @return string
     */
    public function getCurrentPage()
    {
        return $this->_currentPage;
    }

    /**
     * Set PageID
     *
     * param string $page
     */
    public function setCurrentPage($page)
    {
        $this->_currentPage = $page;
    }

}
