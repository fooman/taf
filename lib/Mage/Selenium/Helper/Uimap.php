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
 * UIMap helper class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Helper_Uimap extends Mage_Selenium_Helper_Abstract
{
    /**
     * Path to 'basePath' in config
     */
    const XPATH_UIMAP_BASEPATH = 'default/uimaps/basePath';

    /**
     * Id for uimapData in cash
     */
    const CACHE_ID_DATA = 'UIMAP_DATA';

    /**
     * Tag for UimapData in cash
     */
    const CACHE_TAG = 'UIMAP';

    /**
     * Uimap data
     * @var array
     */
    protected $_uimapData = array();

    /**
     * Initialize process
     * @return Mage_Selenium_Helper_Uimap
     */
    protected function _init()
    {
        parent::_init();

        $this->_loadUimapData();
        return $this;
    }

    /**
     * Load and merge data files
     * @return Mage_Selenium_TestConfiguration
     */
    protected function _loadUimapData()
    {
        $cache = $this->getConfig()->getCacheHelper()->getCache();

        // try to load from cache
        $this->_uimapData = $cache->load(self::CACHE_ID_DATA);
        if (!$this->_uimapData) {
            $areasConfig = $this->getConfig()->getApplicationHelper()->getAreasConfig();
            $uimapsBasePath = $this->getConfig()->getConfigValue(self::XPATH_UIMAP_BASEPATH);

            foreach ($areasConfig as $areaKey => $areaConfig) {
                $files = implode(DIRECTORY_SEPARATOR, array(
                                                           SELENIUM_TESTS_BASEDIR,
                                                           $uimapsBasePath,
                                                           $areaConfig['uimap_path'],
                                                           '*.yml'
                                                      ));
                $pages = $this->getConfig()->getFileHelper()->loadYamlFiles($files);
                foreach ($pages as $pageKey => $pageContent) {
                    if ($pageContent) {
                        $this->_uimapData[$areaKey][$pageKey] = new Mage_Selenium_Uimap_Page($pageKey, $pageContent);
                    }
                }
            }

            if ($this->_uimapData) {
                $cache->save($this->_uimapData, self::CACHE_ID_DATA, array(self::CACHE_TAG));
            }
        }
        return $this;
    }

    /**
     * Retrieve array with UIMap data
     *
     * @param string $area Application area ('frontend'|'admin')
     *
     * @throws OutOfRangeException
     *
     * @return array
     */
    public function getUimap($area)
    {
        if (!array_key_exists($area, $this->_uimapData)) {
            throw new OutOfRangeException();
        }

        return $this->_uimapData[$area];
    }

    /**
     * Retrieve Page from UIMap data configuration by path
     *
     * @param string $area Application area ('frontend'|'admin')
     * @param string $pageKey UIMap page key
     * @param Mage_Selenium_Helper_Params $paramsDecorator Params decorator instance
     *
     * @return Mage_Selenium_Uimap_Page|null
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
     * Retrieve Page from UIMap data configuration by MCA
     *
     * @param string $area Application area ('frontend'|'admin')
     * @param string $mca
     * @param Mage_Selenium_Helper_Params $paramsDecorator Params decorator instance
     *
     * @return Mage_Selenium_Uimap_Page|null
     */
    public function getUimapPageByMca($area, $mca, $paramsDecorator = null)
    {
        if (!isset($area)) {
            return null;
        }
        $mca = trim($mca, ' /\\');
        foreach ($this->_uimapData[$area] as &$page) {
            // get mca without any modifications
            $pageMca = trim($page->getMca(new Mage_Selenium_Helper_Params()), ' /\\');
            if ($pageMca !== false && $pageMca !== null) {
                if ($paramsDecorator) {
                    $pageMca = $paramsDecorator->replaceParametersWithRegexp($pageMca);
                }
                if ($area == 'admin' || $area == 'frontend') {
                    if (preg_match(';^' . $pageMca . '$;', $mca)) {
                        $page->assignParams($paramsDecorator);
                        return $page;
                    }
                } elseif ($this->_compareMcaAndPageMca($mca, $pageMca)) {
                    $page->assignParams($paramsDecorator);
                    return $page;
                }
            }
        }
        return null;
    }

    /**
     * Compares mca from current url and from area mca array
     *
     * @param $mca
     * @param $page_mca
     *
     * @return bool
     */
    protected function _compareMcaAndPageMca($mca, $page_mca)
    {
        if (parse_url($page_mca, PHP_URL_PATH) == parse_url($mca, PHP_URL_HOST) . parse_url($mca, PHP_URL_PATH)) {
            parse_str(parse_url($mca, PHP_URL_QUERY), $mca_params);
            parse_str(parse_url($page_mca, PHP_URL_QUERY), $page_mca_params);
            if (array_keys($mca_params) == array_keys($page_mca_params)) {
                foreach ($page_mca_params as $key => $value) {
                    if ($mca_params[$key] != $value && $value != '%anyValue%') {
                        return false;
                    }
                }
                return true;
            }
        }
        return false;
    }

}
