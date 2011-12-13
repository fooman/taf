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
 * Creating order for new customer with one required field empty
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_Create_CheckingValidationTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Preconditions:</p>
     *
     * <p>Log in to Backend.</p>
     *
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    protected function assertPreConditions()
    {
        $this->addParameter('id', '0');
    }

    /**
     * Create Simple Product for tests
     *
     * @test
     */
    public function createSimple()
    {
        //Data
        $productData = $this->loadData('simple_product_for_order', NULL, array('general_name', 'general_sku'));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');

        return $productData['general_sku'];
    }

    /**
     * <p>Create customer via 'Create order' form (required fields are not filled).</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all fields except one required;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add first two products;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Check / Money order';</p>
     * <p>10.Choose first from 'Get shipping methods and rates';</p>
     * <p>11.Submit order;</p>
     * <p>Expected result:</p>
     * <p>New customer is not created. Order is not created for the new customer.
     *    Message with "Empty required field" appears.</p>
     *
     * @depends createSimple
     * @dataProvider dataEmptyFieldsBilling
     *
     * @param string $emptyField
     * @param string $simpleSku
     * @test
     *
     */
    public function emptyRequiredFildsInBillingAddress($emptyField, $simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_physical', array('filter_sku' => $simpleSku));
        if ($emptyField != 'billing_country') {
            $orderData['billing_addr_data'] = $this->loadData('billing_address_req', array($emptyField => ''));
        } else {
            $orderData['billing_addr_data'] = $this->loadData('billing_address_req',
                    array($emptyField => '', 'billing_state' => '%noValue%'));
        }
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $page = $this->getUimapPage('admin', 'create_order_for_new_customer');
        $fieldSet = $page->findFieldset('order_billing_address');
        if ($emptyField != 'billing_country' and $emptyField != 'billing_state') {
            $fieldXpath = $fieldSet->findField($emptyField);
        } else {
            $fieldXpath = $fieldSet->findDropdown($emptyField);
        }
        if ($emptyField == 'billing_street_address_1') {
            $fieldXpath .= "/ancestor::div[@class='multi-input']";
        }
        $this->addParameter('fieldXpath', $fieldXpath);

        $this->assertMessagePresent('error', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function dataEmptyFieldsBilling()
    {
        return array(
            array('billing_first_name'),
            array('billing_last_name'),
            array('billing_street_address_1'),
            array('billing_city'),
            array('billing_country'),
            array('billing_state'),
            array('billing_zip_code'),
            array('billing_telephone')
        );
    }

    /**
     * <p>Create customer via 'Create order' form (required fields are not filled).</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all fields except one required;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Fill in billing address with required fields;</p>
     * <p>8.Check each shipping required fields (message with error should appear near the field);</p>
     * <p>9.Check payment method 'visa'. Fill its fields with correct information;</p>
     * <p>10.Choose first from 'Get shipping methods and rates';</p>
     * <p>11.Submit order;</p>
     * <p>Expected result:</p>
     * <p>New customer is not created. Order is not created for the new customer.
     *    Message with "Empty required field" appears.</p>
     *
     * @depends createSimple
     * @dataProvider dataEmptyFieldsShipping
     *
     * @param string $emptyField
     * @param string $simpleSku
     * @test
     */
    public function emptyRequiredFildsInShippingAddress($emptyField, $simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_physical', array('filter_sku' => $simpleSku));
        if ($emptyField != 'shipping_country') {
            $orderData['shipping_addr_data'] = $this->loadData('shipping_address_req', array($emptyField => ''));
        } else {
            $orderData['shipping_addr_data'] = $this->loadData('shipping_address_req',
                    array($emptyField => '', 'shipping_state' => '%noValue%'));
        }
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $page = $this->getUimapPage('admin', 'create_order_for_new_customer');
        $fieldSet = $page->findFieldset('order_shipping_address');
        if ($emptyField != 'shipping_country' and $emptyField != 'shipping_state') {
            $fieldXpath = $fieldSet->findField($emptyField);
        } else {
            $fieldXpath = $fieldSet->findDropdown($emptyField);
        }
        if ($emptyField == 'shipping_street_address_1') {
            $fieldXpath .= "/ancestor::div[@class='multi-input']";
        }
        $this->addParameter('fieldXpath', $fieldXpath);

        $this->assertMessagePresent('error', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function dataEmptyFieldsShipping()
    {
        return array(
            array('shipping_first_name'),
            array('shipping_last_name'),
            array('shipping_street_address_1'),
            array('shipping_city'),
            array('shipping_country'),
            array('shipping_state'),
            array('shipping_zip_code'),
            array('shipping_telephone')
        );
    }

    /**
     * <p>Create order without shipping method</p>
     * <p>Steps:</p>
     * <p>1. Create new order for new customer;</p>
     * <p>2. Add product to order;</p>
     * <p>3. Fill in billing and shipping address;</p>
     * <p>4. Choose payment method;</p>
     * <p>4. Do not choose any shipping method;</p>
     * <p>5. Submit order;</p>
     * <p>Expected result:</p>
     * <p>Order cannot be created by the reason of empty required fields in shipping method.</p>
     *
     * @depends createSimple
     * @test
     */
    public function withoutGotShippingMethod($simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_checkmoney_flatrate', array('filter_sku' => $simpleSku));
        unset($orderData['shipping_data']);
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $fieldXpath = $this->_getControlXpath('link', 'get_shipping_methods_and_rates');
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertMessagePresent('error', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Create order without shipping method</p>
     * <p>Steps:</p>
     * <p>1. Create new order for new customer;</p>
     * <p>2. Add product to order;</p>
     * <p>3. Fill in billing and shipping address;</p>
     * <p>4. Choose payment method;</p>
     * <p>4. Click 'Get shipping methods and rates' link;</p>
     * <p>6. Do not choose any shipping method;</p>
     * <p>7. Submit order;</p>
     * <p>Expected result:</p>
     * <p>Order cannot be created by the reason of empty required fields in shipping method.</p>
     *
     * @depends createSimple
     * @test
     */
    public function withGotShippingMethod($simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_checkmoney_flatrate', array('filter_sku' => $simpleSku));
        $orderData = $this->arrayEmptyClear($orderData);
        $billingAddr = $orderData['billing_addr_data'];
        $shippingAddr = $orderData['shipping_addr_data'];
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        $this->orderHelper()->addProductToOrder($orderData['products_to_add']['product_1']);
        $this->orderHelper()->fillOrderAddress($billingAddr, $billingAddr['address_choice'], 'billing');
        $this->orderHelper()->fillOrderAddress($shippingAddr, $shippingAddr['address_choice'], 'shipping');
        $this->orderHelper()->selectPaymentMethod($orderData['payment_data']);
        $this->clickControl('link', 'get_shipping_methods_and_rates', FALSE);
        $this->pleaseWait();
        $this->orderHelper()->submitOreder();
        //Verifying
        $this->assertMessagePresent('error', 'shipping_must_be_specified');
    }

    /**
     * <p>Create order without products.</p>
     * <p>Steps:</p>
     * <p>1. Create new order for new customer;</p>
     * <p>2. Fill in the required fields with billing and shipping address;</p>
     * <p>3. Add products to order;</p>
     * <p>4. Choose any shipping method;</p>
     * <p>5. Remove products from order;</p>
     * <p>6. Submit order;</p>
     * <p>Expected result:</p>
     * <p>Order cannot be created. Message 'You need to specify order items.' appears.</p>
     *
     * @depends createSimple
     * @test
     */
    public function noProductsChosen()
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_checkmoney_flatrate');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $this->assertMessagePresent('error', 'error_specify_order_items');
        $this->assertMessagePresent('error', 'shipping_must_be_specified');
    }

    /**
     * <p>Create order without payment method.</p>
     * <p>Steps:</p>
     * <p>1. Create new order for new customer;</p>
     * <p>2. Fill in the required fields with billing and shipping address;</p>
     * <p>3. Add products to order;</p>
     * <p>4. Choose any shipping method;</p>
     * <p>5. Do not choose payment method;</p>
     * <p>6. Submit order;</p>
     * <p>Expected result:</p>
     * <p>Order cannot be created. Message 'Please select one of the options.' appears.</p>
     *
     * @depends createSimple
     * @test
     */
    public function noPaymentMethodChosen($simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_checkmoney_flatrate', array('filter_sku' => $simpleSku));
        unset($orderData['payment_data']);
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $this->assertMessagePresent('error', 'empty_payment_method');
    }

    /**
     * With all empty credit card fields
     * @param type $simpleSku
     *
     * @depends createSimple
     * @test
     */
    public function emptyAllCardFieldsInSavedCCVisa($simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_savedcc_flatrate', array('filter_sku' => $simpleSku));
        $orderData['payment_data']['payment_info'] = $this->loadData('saved_empty_all');
        $emptyFields = array('field' => 'name_on_card', 'dropdown' => 'card_type',
            'dropdown' => 'expiration_year', 'field' => 'card_verification_number');
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('savedcc_without_3Dsecure');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        foreach ($emptyFields as $key => $value) {
            $xpath = $this->_getControlXpath($key, $value);
            $this->addParameter('fieldXpath', $xpath);
            $this->assertMessagePresent('validation', 'empty_required_field');
        }
        $this->assertTrue($this->verifyMessagesCount(4), $this->getParsedMessages());
    }

    /**
     * With empty 'Name On Card' field in credit card visa
     *
     * @param type $simpleSku
     * @depends createSimple
     * @test
     */
    public function emptyNameOnCardFieldInSavedCC($simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_savedcc_flatrate',
                array('filter_sku' => $simpleSku, 'name_on_card' => ''));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('savedcc_without_3Dsecure');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $xpath = $this->_getControlXpath('field', 'name_on_card');
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * With empty 'Card Type' field in credit card visa
     *
     * @param type $simpleSku
     * @depends createSimple
     * @test
     */
    public function emptyCardTypeFieldInSavedCC($simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_savedcc_flatrate',
                array('filter_sku' => $simpleSku, 'card_type' => ''));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('savedcc_without_3Dsecure');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $xpath = $this->_getControlXpath('dropdown', 'card_type');
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $xpath = $this->_getControlXpath('field', 'card_verification_number');
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('validation', 'invalid_cvv');
        $this->assertTrue($this->verifyMessagesCount(2), $this->getParsedMessages());
    }

    /**
     * With empty 'Card Number' field in credit card visa
     *
     * @param type $simpleSku
     * @depends createSimple
     * @test
     */
    public function emptyCardNumberFieldInSavedCC($simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_savedcc_flatrate',
                array('filter_sku' => $simpleSku, 'card_number' => ''));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('savedcc_without_3Dsecure');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $xpath = $this->_getControlXpath('dropdown', 'card_type');
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('validation', 'card_type_doesnt_match');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * With empty 'Expiration Year' field in credit card visa
     *
     * @param type $simpleSku
     * @depends createSimple
     * @test
     */
    public function emptyExpirationYearFieldInSavedCC($simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_savedcc_flatrate',
                array('filter_sku' => $simpleSku, 'expiration_year' => ''));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('savedcc_without_3Dsecure');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $xpath = $this->_getControlXpath('dropdown', 'expiration_year');
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * With empty 'Card Verification Number' field in credit card visa
     *
     * @param type $simpleSku
     * @depends createSimple
     * @test
     */
    public function emptCardVerificationNumberFieldInSavedCC($simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_savedcc_flatrate',
                array('filter_sku' => $simpleSku, 'card_verification_number' => ''));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('savedcc_without_3Dsecure');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $xpath = $this->_getControlXpath('field', 'card_verification_number');
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * With empty 'Expiration Month' field in credit card visa
     *
     * @param type $simpleSku
     * @depends createSimple
     * @test
     */
    public function emptyExpirationMonthFieldInSavedCC($simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_savedcc_flatrate',
                array('filter_sku' => $simpleSku, 'expiration_month' => ''));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('savedcc_without_3Dsecure');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $this->assertMessagePresent('error', 'invalid_exp_date');
    }

}
