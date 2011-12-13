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
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Product_Buffer extends Mage_Selenium_TestCase
{

    protected static $productsInStock = array();

    protected function assertPreConditions()
    {
         $this->loginAdminUser();
    }
//  Uncomment it for products creation
//    /**
//     * <p>Preconditions</p>
//     * <p>Create attribute</p>
//     *
//     * @test
//     */
//    public function createAttribute()
//    {
//        $attrData = $this->loadData('product_attribute_dropdown_with_options', NULL,
//                array('admin_title', 'attribute_code'));
//        $associatedAttributes = $this->loadData('associated_attributes',
//                array('General' => $attrData['attribute_code']));
//        $this->navigate('manage_attributes');
//        $this->productAttributeHelper()->createAttribute($attrData);
//        $this->assertTrue($this->successMessage('success_saved_attribute'), $this->messages);
//        $this->navigate('manage_attribute_sets');
//        $this->attributeSetHelper()->openAttributeSet();
//        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
//        $this->addParameter('attributeName', 'Default');
//        $this->saveForm('save_attribute_set');
//        $this->assertTrue($this->successMessage('success_attribute_set_saved'), $this->messages);
//
//        return $attrData;
//    }
//
//    /**
//     * <p>Preconditions</p>
//     * <p>Create simple product for adding it to bundle and associated product</p>
//     *
//     * @depends createAttribute
//     * @test
//     * @return string
//     */
//    public function createSimpleProductForBundle($attrData)
//    {
//        $productData = $this->loadData('simple_product_visible', NULL, array('general_name','general_sku'));
//        $productData['general_user_attr']['dropdown'][$attrData['attribute_code']] =
//                $attrData['option_1']['admin_option_name'];
//        $this->navigate('manage_products');
//        $this->productHelper()->createProduct($productData);
//        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
//
//        return $productData['general_sku'];
//    }
//    /**
//     * <p>Preconditions</p>
//     * <p>Create products for linking in stock</p>
//     *
//     * @dataProvider productTypes
//     * @depends createAttribute
//     * @depends createSimpleProductForBundle
//     *
//     * @test
//     */
//    public function createProductsForLinkingInStock($productType, $attrData, $simple)
//    {
//        $productData = $this->loadData($productType. '_product_related',
//                                           array('bundle_items_search_sku' => $simple,
//                                                 'configurable_attribute_title' => $attrData['admin_title'],
//                                                 'associated_search_sku' => $simple));
//        if ($productType != 'grouped') {
//            $productData['custom_options_data'] = $this->loadData('custom_options_data');
//        }
//        $this->navigate('manage_products');
//        $this->productHelper()->createProduct($productData, $productType);
//        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
//
//        self::$productsInStock[$productType]['general_name'] = $productData['general_name'];
//        self::$productsInStock[$productType]['general_sku'] = $productData['general_sku'];
//    }
//
//    public function productTypes()
//    {
//        return array(
//            array('simple'),
//            array('virtual'),
//            array('downloadable'),
//            array('bundle'),
//            array('configurable'),
//            array('grouped')
//        );
//    }

    /**
     * @test
     */
    public function some()
    {
        $this->logoutCustomer();
        $products = $this->loadData('product_to_add_to_shop');
        foreach ($products as $key => $value) {
            $this->productHelper()->frontOpenProduct($value);
            if ($key != 'grouped') {
                $dataToBuy = $this->loadData('custom_options_to_add_to_shopping_cart');
                $this->productHelper()->frontFillBuyInfo($dataToBuy);
            }
            if ($key != 'simple' && $key != 'virtual') {
                $addData = $this->loadData($key . '_options_to_add_to_shopping_cart');
                $this->productHelper()->frontFillBuyInfo($addData);
            }
            $this->productHelper()->frontAddProductToCart();
        }
    }

}