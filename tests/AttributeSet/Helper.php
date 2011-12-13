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
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AttributeSet_Helper extends Mage_Selenium_TestCase
{

    /**
     * Create Attribute Set
     *
     * @param array $attrSet Array which contains DataSet for filling of the current form
     */
    public function createAttributeSet(array $attrSet)
    {
        $attrSet = $this->arrayEmptyClear($attrSet);
        $this->clickButton('add_new_set');
        $this->fillForm($attrSet);
        $this->addParameter('attributeName', $attrSet['set_name']);
        $this->saveForm('save_attribute_set');
        if (array_key_exists('new_groups', $attrSet)) {
            $this->addNewGroup($attrSet['new_groups']);
        }
        if (array_key_exists('associated_attributes', $attrSet)) {
            $this->addAttributeToSet($attrSet['associated_attributes']);
        }
        $this->saveForm('save_attribute_set');
    }

    /**
     * Add new group to attribute set
     *
     * @param mixed $attrGroup Array or String (data divided by comma)
     *                         which contains DataSet for creating folder of attributes
     */
    public function addNewGroup($attrGroup)
    {
        if (is_string($attrGroup)) {
            $attrGroup = explode(',', $attrGroup);
            $attrGroup = array_map('trim', $attrGroup);
        }
        foreach ($attrGroup as $value) {
            $this->addParameter('folderName', $value);
            $groupXpath = $this->_getControlXpath('link', 'group_folder');
            if (!$this->isElementPresent($groupXpath)) {
                $this->answerOnNextPrompt($value);
                $this->clickButton('add_group', FALSE);
                $this->getPrompt();
            }
        }
    }

    /**
     * Add attribute to attribute Set
     *
     * @param array $attributes Array which contains DataSet for filling folder of attribute set
     */
    public function addAttributeToSet(array $attributes)
    {
        foreach ($attributes as $groupName => $attributeCode) {
            if ($attributeCode == '%noValue%') {
                continue;
            }
            if (is_string($attributeCode)) {
                $attributeCode = explode(',', $attributeCode);
                $attributeCode = array_map('trim', $attributeCode);
            }
            $this->addParameter('folderName', $groupName);
            foreach ($attributeCode as $value) {
                $this->addParameter('attributeName', $value);
                $elFrom = $this->_getControlXpath('link', 'unassigned_attribute');
                $elTo = $this->_getControlXpath('link', 'group_folder');
                if (!$this->isElementPresent($elTo)) {
                    $this->addNewGroup($groupName);
                }
                if (!$this->isElementPresent($elFrom)) {
                    $this->fail("Attribute with title '$value' does not exist");
                }
                $this->moveElementOverTree('link', 'unassigned_attribute', 'fieldset', 'unassigned_attributes');
                $this->moveElementOverTree('link', 'group_folder', 'fieldset', 'groups');
                $this->clickAt($elFrom, '1,1');
                $this->clickAt($elTo, '1,1');
                $this->mouseDownAt($elFrom, '1,1');
                $this->mouseMoveAt($elTo, '1,1');
                $this->mouseUpAt($elTo, '10,10');
            }
        }
    }

    /**
     * Open Attribute Set
     *
     * @param string|array $setName
     */
    public function openAttributeSet($setName = 'Default')
    {
        if (is_array($setName) and isset($setName['set_name'])) {
            $setName = $setName['set_name'];
        }
        $this->addParameter('attributeName', $setName);
        $searchData = $this->loadData('search_attribute_set', array('set_name' => $setName));
        $this->assertTrue($this->searchAndOpen($searchData), "Attribute Set with name '$setName' is not found");
    }

}
