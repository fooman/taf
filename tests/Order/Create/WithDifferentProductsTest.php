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
 * Order creation with different type of products
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_Create_WithDifferentProductsTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Preconditions:</p>
     *
     * <p>Log in to Backend.</p>
     * <p>Navigate to 'Manage Products' page</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    protected function assertPreConditions()
    {
        $this->navigate('manage_products');
        $this->addParameter('id', '0');
    }
    /**
     * <p>Creating order with virtual product with custom options</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select virtual product and add it to the order.
     *       Fill any required information to configure product;</p>
     * <p>4. Fill in all required information.
     *       Shipping address fill and shipping methods should be disabled;</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @test
     */
    public function witCustomOptions()
    {
        //Data
        $virtual = $this->loadData('virtual_product_for_order',
                array('custom_options_data' => $this->loadData('custom_options_data')),
                array('general_name', 'general_sku'));
        $orderData = $this->loadData('order_virtual', array('filter_sku' => $virtual['general_sku'],
            'configurable_options' => $this->loadData('config_option_custom_options')));
        //Steps and Verifying
        $this->productHelper()->createProduct($virtual, 'virtual');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
        $this->clickButton('reorder');
        $this->orderHelper()->submitOreder();
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');

        return $virtual;
    }

    /**
     * Test Realizing precondition for creating configurable product.
     *
     * @test
     */
    public function createConfigurableAttribute()
    {
        //Data
        $attrData = $this->loadData('product_attribute_dropdown_with_options', null,
                array('admin_title', 'attribute_code'));
        $associatedAttributes = $this->loadData('associated_attributes',
                array('General' => $attrData['attribute_code']));
        //Steps and Verifying
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return $attrData;
    }

    /**
     * <p>Creating order with simple products</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select simple product and add it to the order;</p>
     * <p>4. Fill in all required information</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @test
     * @depends createConfigurableAttribute
     */
    public function withSimpleProduct($attrData)
    {
        //Data
        $simple = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
        $attrCode = $attrData['attribute_code'];
        $simple['general_user_attr']['dropdown'][$attrCode] = $attrData['option_1']['admin_option_name'];
        $orderData = $this->loadData('order_newcustmoer_checkmoney_flatrate',
                array('filter_sku' => $simple['general_sku']));
        //Steps and Verifying
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
        $this->clickButton('reorder');
        $this->orderHelper()->submitOreder();
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');

        return $simple;
    }

    /**
     * <p>Creating order with virtual products</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select virtual product and add it to the order.
     *       Fill any required information to configure product;</p>
     * <p>4. Fill in all required information.
     *       Shipping address fill and shipping methods should be disabled;</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @depends createConfigurableAttribute
     * @test
     */
    public function withVirtualProduct($attrData)
    {
        //Data
        $virtual = $this->loadData('virtual_product_for_order', null, array('general_name', 'general_sku'));
        $attrCode = $attrData['attribute_code'];
        $virtual['general_user_attr']['dropdown'][$attrCode] = $attrData['option_2']['admin_option_name'];
        $orderData = $this->loadData('order_virtual', array('filter_sku' => $virtual['general_sku']));
        //Steps and Verifying
        $this->productHelper()->createProduct($virtual, 'virtual');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
        $this->clickButton('reorder');
        $this->orderHelper()->submitOreder();
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');

        return $virtual;
    }

    /**
     * <p>Creating order with downloadable products</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select downloadable product and add it to the order.
     *       Fill any required information to configure product;</p>
     * <p>4. Fill in all required information; Shipping methods and address should be disabled;</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @test
     */
    public function withDownloadableConfigProduct()
    {
        //Data
        $downloadable = $this->loadData('downloadable_product_for_order', null, array('general_name', 'general_sku'));
        $orderData = $this->loadData('order_virtual',
                array('filter_sku' => $downloadable['general_sku'],
            'configurable_options' => $this->loadData('config_option_download')));
        //Steps and Verifying
        $this->productHelper()->createProduct($downloadable, 'downloadable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
        $this->clickButton('reorder');
        $this->orderHelper()->submitOreder();
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');
    }

    /**
     * <p>Creating order with downloadable products</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select downloadable product and add it to the order.
     *       Fill any required information to configure product;</p>
     * <p>4. Fill in all required information; Shipping methods and address should be disabled;</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @depends createConfigurableAttribute
     * @test
     */
    public function withDownloadableNotConfigProduct($attrData)
    {
        //Data
        $downloadable = $this->loadData('downloadable_product_for_order',
                array('downloadable_links_purchased_separately' => 'No'), array('general_name', 'general_sku'));
        $attrCode = $attrData['attribute_code'];
        $downloadable['general_user_attr']['dropdown'][$attrCode] = $attrData['option_3']['admin_option_name'];
        $orderData = $this->loadData('order_virtual',
                array('filter_sku' => $downloadable['general_sku'],
            'customer_email' => $this->generate('email', 32, 'valid')));
        //Steps and Verifying
        $this->productHelper()->createProduct($downloadable, 'downloadable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
        $this->clickButton('reorder');
        $this->orderHelper()->submitOreder();
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');

        return $downloadable;
    }

    /**
     * <p>Creating order with bundled products</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select bundled product and add it to the order.
     *       Fill any required information to configure product;</p>
     * <p>4. Fill in all required information</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @depends withSimpleProduct
     * @depends withVirtualProduct
     * @test
     */
    public function bundleWithSimple($simple, $virtual)
    {
        //Data
        //Bundle Product Data
        $product1 = $this->loadData('product_to_bundle', array('bundle_items_search_sku' => $simple['general_sku']));
        $product2 = $this->loadData('product_to_bundle', array('bundle_items_search_sku' => $virtual['general_sku']));
        $bundle = $this->loadData('fixed_bundle_for_order',
                array('add_product_1' => $product1,'add_product_2' => $product2), array('general_name','general_sku'));
        //Order Data
        $multiSelect = $this->loadData('configure_field_multiselect', array('fieldsValue' => $simple['general_name']));
        $dropDown = $this->loadData('configure_field_dropdown', array('fieldsValue' => $simple['general_name']));
        $checkBox = $this->loadData('configure_field_checkbox', array('fieldParameter' => $simple['general_name']));
        $radio = $this->loadData('configure_field_radiobutton', array('fieldParameter' => $simple['general_name']));
        $configurable = $this->loadData('config_option_bundle',
                array('field_checkbox'    => $checkBox,    'field_dropdow' => $dropDown,
                      'field_multiselect' => $multiSelect, 'field_radio'   => $radio));
        $orderData = $this->loadData('order_newcustmoer_checkmoney_flatrate',
                array('filter_sku' => $bundle['general_sku'], 'configurable_options' => $configurable));
        //Steps and Verifying
        $this->productHelper()->createProduct($bundle, 'bundle');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
        $this->clickButton('reorder');
        $this->orderHelper()->submitOreder();
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');

        return array('bundle_sku' => $bundle['general_sku'], 'virtual_name' => $virtual['general_name']);
    }

    /**
     * <p>Creating order with bundle product</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select bundled product and add it to the order.
     *       Fill any required information to configure product;</p>
     * <p>4. Fill in all required information</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @depends bundleWithSimple
     * @test
     */
    public function bundleWithVirtual($data)
    {
        //Data
        $multiSelect = $this->loadData('configure_field_multiselect', array('fieldsValue' => $data['virtual_name']));
        $dropDown = $this->loadData('configure_field_dropdown', array('fieldsValue' => $data['virtual_name']));
        $checkBox = $this->loadData('configure_field_checkbox', array('fieldParameter' => $data['virtual_name']));
        $radio = $this->loadData('configure_field_radiobutton', array('fieldParameter' => $data['virtual_name']));
        $configurable = $this->loadData('config_option_bundle',
                array('field_dropdow' => $dropDown, 'field_multiselect' => $multiSelect,
                      'field_radio'   => $radio,    'field_checkbox'    => $checkBox));
        $orderData = $this->loadData('order_virtual',
                array('filter_sku' => $data['bundle_sku'], 'configurable_options' => $configurable));
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
        $this->clickButton('reorder');
        $this->orderHelper()->submitOreder();
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');
    }

    /**
     * <p>Creating order with configurable product with simple</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select configurable product and add it to the order.
     *       Fill any required information to configure product;</p>
     * <p>4. Fill in all required information</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @depends createConfigurableAttribute
     * @depends withSimpleProduct
     * @depends withVirtualProduct
     * @depends withDownloadableNotConfigProduct
     * @test
     */
    public function configurableProductWithSimple($attrData, $simple, $virtual, $download)
    {
        //Data
        $pr_1 = $this->loadData('associated', array('associated_search_sku' => $simple['general_sku']));
        $pr_2 = $this->loadData('associated', array('associated_search_sku' => $virtual['general_sku']));
        $pr_3 = $this->loadData('associated', array('associated_search_sku' => $download['general_sku']));
        $configurable = $this->loadData('configurable_product_for_order',
                array(
                    'configurable_attribute_title' => $attrData['admin_title'],
                    'associated_configurable_1'    => $pr_1,
                    'associated_configurable_2'    => $pr_2,
                    'associated_configurable_3'    => $pr_3,
                ), array('general_name', 'general_sku'));
        $orderData = $this->loadData('order_newcustmoer_checkmoney_flatrate',
                array('filter_sku'           => $configurable['general_sku'],
                      'configurable_options' => $this->loadData('config_option_configurable',
                                                    array('title' => $attrData['admin_title'],
                                                        'fieldsValue' => $attrData['option_1']['admin_option_name']))));
        //Steps and Verifying
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
        $this->clickButton('reorder');
        $this->orderHelper()->submitOreder();
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');

        return $configurable;
    }

    /**
     * <p>Creating order with configurable product with virtual</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select configurable product and add it to the order.
     *       Fill any required information to configure product;</p>
     * <p>4. Fill in all required information</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @depends createConfigurableAttribute
     * @depends configurableProductWithSimple
     * @test
     */
    public function configurableProductWithVirtual($attrData, $configurable)
    {
        //Data
        $orderData = $this->loadData('order_virtual',
                array('filter_sku' => $configurable['general_sku'],
                      'configurable_options' => $this->loadData('config_option_configurable',
                                                    array('title' => $attrData['admin_title'],
                                                        'fieldsValue' => $attrData['option_2']['admin_option_name']))));
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
        $this->clickButton('reorder');
        $this->orderHelper()->submitOreder();
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');
    }

    /**
     * <p>Creating order with configurable product with downloadable</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select configurable product and add it to the order.
     *       Fill any required information to configure product;</p>
     * <p>4. Fill in all required information</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @depends createConfigurableAttribute
     * @depends configurableProductWithSimple
     * @test
     */
    public function configurableProductWithDownloadable($attrData, $configurable)
    {
        //Data
        $configOpt = $this->loadData('config_option_configurable',
                array('title' => $attrData['admin_title'],'fieldsValue' => $attrData['option_3']['admin_option_name']));
        $orderData = $this->loadData('order_virtual',
                array('filter_sku' => $configurable['general_sku'], 'configurable_options' => $configOpt));
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
        $this->clickButton('reorder');
        $this->orderHelper()->submitOreder();
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');
    }

    /**
     * <p>Creating order with grouped products</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select group product and add it to the order. Fill any required information to configure product;</p>
     * <p>4. Fill in all required information</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @depends withSimpleProduct
     * @depends withVirtualProduct
     * @depends withDownloadableNotConfigProduct
     * @test
     */
    public function groupedWithSimple($simple, $virtual, $download)
    {
        //Data
        $prod1 = $this->loadData('associated_grouped', array('associated_search_sku' => $simple['general_sku']));
        $prod2 = $this->loadData('associated_grouped', array('associated_search_sku' => $virtual['general_sku']));
        $prod3 = $this->loadData('associated_grouped', array('associated_search_sku' => $download['general_sku']));
        $grouped = $this->loadData('grouped_product_for_order',
                array('associated_grouped_1' => $prod1, 'associated_grouped_2' => $prod2,
                      'associated_grouped_3' => $prod3), array('general_name', 'general_sku'));
        $orderData = $this->loadData('order_newcustmoer_checkmoney_flatrate',
                array(
                    'filter_sku'           => $grouped['general_sku'],
                    'configurable_options' => $this->loadData('config_option_grouped',
                                                        array('fieldParameter' => $simple['general_sku'])),
                                                            'customer_email' => $this->generate('email', 32, 'valid')));
        //Steps and Verifying
        $this->productHelper()->createProduct($grouped, 'grouped');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
        $this->clickButton('reorder');
        $this->orderHelper()->submitOreder();
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');

        return $grouped['general_sku'];
    }

    /**
     * <p>Creating order with grouped products using virtual products</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select group product and add it to the order.
     *       Fill any required information to configure product;</p>
     * <p>4. Fill in all required information</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @depends withVirtualProduct
     * @depends withDownloadableNotConfigProduct
     * @depends groupedWithSimple
     * @test
     */
    public function groupedWithVirtualTypesOfProducts($virtual, $download, $grouped)
    {
        //Data
        $option2 = $this->loadData('config_option_grouped/option_1',
                array('fieldParameter' => $download['general_sku']));
        $orderData = $this->loadData('order_virtual',
                array('filter_sku'           => $grouped,
                      'configurable_options' => $this->loadData('config_option_grouped',
                                                       array('fieldParameter' => $virtual['general_sku'],
                                                             'option_2'       => $option2))));
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
        $this->clickButton('reorder');
        $this->orderHelper()->submitOreder();
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');
    }

}
