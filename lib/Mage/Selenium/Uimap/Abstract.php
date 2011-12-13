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
 * Abstract uimap class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Uimap_Abstract
{

    /**
     * XPath string
     * @var string
     */
    protected $xPath = '';

    /**
     * UIMap elements
     * @var array
     */
    protected $_elements = array();

    /**
     * UIMap elements cache for recursive operations
     * @var array
     */
    protected $_elements_cache = array();

    /**
     * Parameters helper instance
     *
     * @var Mage_Selenium_Helper_Params
     */
    protected $_params = null;

    /**
     * Retrieve XPath of the current element
     *
     * @param Mage_Selenium_Helper_Params $paramsDecorator Params decorator instance
     *
     * @return string|null
     */
    public function getXPath($paramsDecorator = null)
    {
        return $this->applyParamsToString($this->xPath, $paramsDecorator);
    }

    /**
     * Retrieve all elements on current level
     *
     * @return array
     */
    public function &getElements()
    {
        return $this->_elements;
    }

    /**
     * Parser from native UIMap array to UIMap class hierarchy
     *
     * @param array &$container Array with UIMap
     *
     * @return Mage_Selenium_Uimap_Abstract
     */
    protected function parseContainerArray(array &$container)
    {
        foreach ($container as $formElemKey => &$formElemValue) {
            if (!empty($formElemValue)) {
                $newElement = Mage_Selenium_Uimap_Factory::createUimapElement($formElemKey, $formElemValue);
                if (!empty($newElement)) {
                    if (!isset($this->_elements[$formElemKey])) {
                        $this->_elements[$formElemKey] = $newElement;
                    } else {
                        if ($this->_elements[$formElemKey] instanceof ArrayObject) {
                            $this->_elements[$formElemKey]->append($newElement);
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Asign parameters decorator to uimap tree from any level
     *
     * @param Mage_Selenium_Helper_Params $params Parameters decorator
     *
     * @return Mage_Selenium_Uimap_Abstract
     */
    public function assignParams($params)
    {
        $this->_params = $params;
        $this->_elements_cache = null;

        foreach ($this->_elements as $elem) {
            if ($elem instanceof Mage_Selenium_Uimap_Abstract
                    || $elem instanceof Mage_Selenium_Uimap_ElementsCollection
            ) {
                $elem->assignParams($params);
            } else if ($elem instanceof ArrayObject) {
                foreach ($elem as $arr_elem) {
                    if ($arr_elem instanceof Mage_Selenium_Uimap_Abstract) {
                        $arr_elem->assignParams($params);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Get parameters decorator
     *
     * @param Mage_Selenium_Helper_Params $paramsDecorator Parameters decorator instance (by default = NULL)
     *
     * @return Mage_Selenium_Helper_Params|null
     */
    protected function getParams($paramsDecorator = null)
    {
        if ($paramsDecorator) {
            return $paramsDecorator;
        }

        return $this->_params;
    }

    /**
     * Apply parameters decorator to string
     *
     * @param string $text String to change
     * @param Mage_Selenium_Helper_Params $paramsDecorator Parameters decorator instance or null
     *
     * @return Mage_Selenium_Helper_Params|null
     */
    protected function applyParamsToString($text, $paramsDecorator = null)
    {
        $paramsDecorator = $this->getParams($paramsDecorator);
        if ($paramsDecorator) {
            return $paramsDecorator->replaceParameters($text);
        }

        return $text;
    }

    /**
     * Internal recursive function
     *
     * @param string $elementsCollectionName UIMap elements collection name
     * @param Mage_Selenium_Uimap_ElementsCollection|Mage_Selenium_Uimap_Abstract $container UIMap container
     * @param array $cache Array with search results
     * @param Mage_Selenium_Helper_Params $paramsDecorator Parameters decorator instance or null
     *
     * @return array
     */
    protected function __getElementsRecursive($elementsCollectionName, &$container, &$cache, $paramsDecorator = null)
    {
        foreach ($container as $elKey => $elValue) {
            if ($elValue instanceof ArrayObject) {
                if (($elementsCollectionName == 'tabs'
                        && $elementsCollectionName == $elKey
                        && $elValue instanceof Mage_Selenium_Uimap_TabsCollection)
                        || ($elementsCollectionName == 'fieldsets'
                        && $elementsCollectionName == $elKey
                        && $elValue instanceof Mage_Selenium_Uimap_FieldsetsCollection)
                        || $elKey == $elementsCollectionName
                        && $elValue instanceof Mage_Selenium_Uimap_ElementsCollection
                ) {
                    $cache = array_merge($cache, $elValue->getArrayCopy());
                } else {
                    $this->__getElementsRecursive($elementsCollectionName, $elValue, $cache, $paramsDecorator);
                }
            } elseif ($elValue instanceof Mage_Selenium_Uimap_Abstract) {
                $this->__getElementsRecursive($elementsCollectionName,
                        $elValue->getElements(), $cache, $paramsDecorator);
            }
        }

        return $cache;
    }

    /**
     * Search UIMap element by name on any level from current and deeper
     * This method uses a cache to save search results
     *
     * @param string $elementsCollectionName UIMap Elements collection name
     * @param Mage_Selenium_Helper_Params $paramsDecorator Parameters decorator instance (by default = NULL)
     *
     * @return array
     */
    public function getAllElements($elementsCollectionName, $paramsDecorator = null)
    {
        if (empty($this->_elements_cache[$elementsCollectionName])) {
            $cache = array();
            $this->_elements_cache[$elementsCollectionName] = new Mage_Selenium_Uimap_ElementsCollection(
                            $elementsCollectionName,
                            $this->__getElementsRecursive($elementsCollectionName, $this->_elements, $cache,
                                    $paramsDecorator),
                            $paramsDecorator);
        }

        return $this->_elements_cache[$elementsCollectionName];
    }

    /**
     * Magic method to call an accessor methods<br>
     * Format:
     * <li>- call "get"+"UIMap properties collection name"() to get UIMap elements collection by name from current level
     * <li>- call "getAll"+"UIMap properties collection name"() to get UIMap elements collection by name on any level
     * from current and deeper
     * <li>- call "find"+"UIMap element type"(element name) to get UIMap element by name on any level from current
     * and deeper
     *
     * @param string $name Method's name to call 'get' | 'getAll' | 'find'
     * @param string $arguments Argument to calling method 'UIMap properties collection name' | 'UIMap element type'
     *
     * @return Mage_Selenium_Uimap_ElementsCollection|array|Null
     */
    public function __call($name, $arguments)
    {
        $returnValue = null;

        if (preg_match('|^getAll(\w+)$|', $name)) {
            $elementName = strtolower(substr($name, 6));
            if (!empty($elementName)) {
                $returnValue = $this->getAllElements($elementName,
                        $this->getParams(isset($arguments[1]) ? $arguments[1] : null));
            }
        } elseif (preg_match('|^get(\w+)$|', $name)) {
            $elementName = strtolower(substr($name, 3));
            if (!empty($elementName) && isset($this->_elements[$elementName])) {
                $returnValue = $this->_elements[$elementName];
            }
        } elseif (preg_match('|^find(\w+)$|', $name)) {
            $elementName = strtolower(substr($name, 4));
            if ($elementName === 'checkbox') {
                $elementName .= 'es';
            } else {
                $elementName .= 's';
            }
            if (!empty($elementName) && !empty($arguments)) {
                $elemetsColl = $this->getAllElements($elementName);
                $returnValue = $elemetsColl->get($arguments[0],
                        $this->getParams(isset($arguments[1]) ? $arguments[1] : null));
            }
        }

        if (!empty($elementName) && !$returnValue) {
            throw new Exception('Cant\' find element(s) "' . $elementName . '"');
        }

        return $returnValue;
    }

}
