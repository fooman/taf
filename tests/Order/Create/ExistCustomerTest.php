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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test with variations of address
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_Create_ExistCustomerTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Log in to Backend.</p>
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
     * <p>Create Simple Product for tests</p>
     *
     * @return string
     * @test
     */
    public function createSimpleProduct()
    {
        //Data
        $productData = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');

        return $productData['general_sku'];
    }

    /**
     * <p>Create customer for tests</p>
     *
     * @return string
     * @test
     */
    public function createCustomer()
    {
        //Data
        $userData = $this->loadData('generic_customer_account', null, 'email');
        $addressData = $this->loadData('all_fields_address');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData, $addressData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');

        return $userData['email'];
    }

    /**
     * <p>Creating order for existing customer with same billing and shipping addresses.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order and choose existing customer from the list;</p>
     * <p>3. Choose existing address for billing and shipping;</p>
     * <p>4. Fill in all required fields (add products, add payment method information, choose shipping method, etc);</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created, no error messages appear;</p>
     *
     * @depends createSimpleProduct
     * @depends createCustomer
     * @param string $simpleSku
     * @param string $customer
     * @test
     */
    public function existingCustomerWithAddress($simpleSku, $customer)
    {
        //Data
        $orderData = $this->loadData('order_physical', array('filter_sku' => $simpleSku, 'email' => $customer));
        unset($orderData['billing_addr_data']);
        unset($orderData['shipping_addr_data']);
        //Steps And Verifying
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
        $this->clickButton('reorder');
        $this->orderHelper()->submitOreder();
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');
    }
}