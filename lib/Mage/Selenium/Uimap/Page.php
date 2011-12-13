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
 * Page uimap class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Uimap_Page extends Mage_Selenium_Uimap_Abstract
{
    /**
     * Page ID
     *
     * @var string
     */
    protected $pageId = '';

    /**
     * Page MCA
     *
     * @var string
     */
    protected $mca = '';

    /**
     * @var string
     */
    protected $_clickXpath = '';

    /**
     * Page title
     *
     * @var string
     */
    protected $title = '';

    /**
     * Page class constructor
     *
     * @param string $pageId Page ID
     * @param array $pageContainer Array of data, which contains in specific page
     */
    public function  __construct($pageId, array &$pageContainer)
    {
        $this->pageId = $pageId;

        if (isset($pageContainer['mca'])) {
            $this->mca = $pageContainer['mca'];
        }
        if (isset($pageContainer['click_xpath'])) {
            $this->_clickXpath = $pageContainer['click_xpath'];
        }
        if (isset($pageContainer['title'])) {
            $this->title = $pageContainer['title'];
        }
        if (isset($pageContainer['uimap'])) {
            $this->parseContainerArray($pageContainer['uimap']);
        }
    }

    /**
     * Get page ID
     *
     * @return string ID of the page
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * Get page mca
     *
     * @param Mage_Selenium_Helper_Params $paramsDecorator Parameters decorator instance or NULL (by default = NULL)
     *
     * @return string
     */
    public function getMca($paramsDecorator = null)
    {
        return $this->applyParamsToString($this->mca, $paramsDecorator);
    }

    /**
     * Get page click xpath
     *
     * @param Mage_Selenium_Helper_Params $paramsDecorator Parameters decorator instance or NULL (by default = NULL)
     *
     * @return string
     */
    public function getClickXpath($paramsDecorator = null)
    {
        return $this->applyParamsToString($this->_clickXpath, $paramsDecorator);
    }

    /**
     * Get page title
     *
     * @param Mage_Selenium_Helper_Params $paramsDecorator Parameters decorator instance or null
     *
     * @return string Title of the page
     */
    public function getTitle($paramsDecorator = null)
    {
        return $this->applyParamsToString($this->title, $paramsDecorator);
    }

    /**
     * Get the main form defined on the current page
     *
     * @return Mage_Selenium_Uimap_Form
     */
    public function getMainForm()
    {
        return $this->_elements['form'];
    }
}
