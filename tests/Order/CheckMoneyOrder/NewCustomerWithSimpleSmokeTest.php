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
 * Test admin order workflow with Check Money Order payment method
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_CheckMoneyOrder_NewCustomerWithSimpleSmokeTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('checkmoney');
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
     * <p>Smoke tests for order creation</p>
     *
     * @depends createSimpleProduct
     * @param string $simpleSku
     * @return array
     * @test
     */
    public function orderSmoke($simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustomer_checkmoney_flatrate_usa', array('filter_sku' => $simpleSku));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');

        return $orderData;
    }

    /**
     * <p>TL-MAGE-312:Invoice for full order</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add products;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Check/Money order';</p>
     * <p>10.Choose any from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>12. Invoice order;</p>
     * <p>Expected result:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     * <p>Order is invoiced successfully</p>
     *
     * @depends orderSmoke
     * @param array $orderData
     * @test
     */
    public function fullInvoice($orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
    }

    /**
     * <p>TL-MAGE-313:Invoice for part of order</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add products;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Check/Money order';</p>
     * <p>10.Choose any from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>12. Partially invoice order;</p>
     * <p>Expected result:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     * <p>Order is invoiced successfully</p>
     *
     * @depends orderSmoke
     * @param array $orderData
     * @test
     */
    public function partialInvoice($orderData)
    {
        //Data
        $orderData['products_to_add']['product_1']['product_qty'] = 10;
        $invoice = $this->loadData('products_to_invoice',
                array('invoice_product_sku' => $orderData['products_to_add']['product_1']['filter_sku']));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty(null, $invoice);
    }

    /**
     * <p>TL-MAGE-319:Credit Memo for whole invoce<p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add products;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Check/Money order';</p>
     * <p>10.Choose any from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>12. Invoice order;</p>
     * <p>13. Refund order;</p>
     * <p>Expected result:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     * <p>Order is invoiced and refunded successfully</p>
     *
     * @depends orderSmoke
     * @param array $orderData
     * @test
     */
    public function fullCreditMemoWithCheck($orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
    }

    /**
     * <p>TL-MAGE-418:Credit Memo for part of invoce</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add two products;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Check/Money order';</p>
     * <p>10.Choose any from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>12. Invoice order;</p>
     * <p>13. Partially refund order (choose only one product);</p>
     * <p>Expected result:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     * <p>Order is invoiced and refunded successfully</p>
     *
     * @depends orderSmoke
     * @param array $orderData
     * @test
     */
    public function partialCreditMemoWithCheck($orderData)
    {
        //Data
        $orderData['products_to_add']['product_1']['product_qty'] = 10;
        $creditMemo = $this->loadData('products_to_refund',
                array('return_filter_sku' => $orderData['products_to_add']['product_1']['filter_sku']));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline', $creditMemo);
    }

    /**
     * <p>TL-MAGE-316:Shipment for order</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add products;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Check Money Order';</p>
     * <p>10.Choose any from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>13. Ship order;</p>
     * <p>Expected result:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     * <p>Order is shipped successfully</p>
     *
     * @depends orderSmoke
     * @param array $orderData
     * @test
     */
    public function fullShipmentForOrderWithoutInvoice($orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
    }

    /**
     * <p>TL-MAGE-317:Shipment for part of order</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add several products;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Check Money Order';</p>
     * <p>10.Choose any from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>13. Partially ship order;</p>
     * <p>Expected result:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     * <p>Order is shipped successfully</p>
     *
     * @depends orderSmoke
     * @param array $orderData
     * @test
     */
    public function partialShipmentForOrderWithoutInvoice($orderData)
    {
        //Data
        $orderData['products_to_add']['product_1']['product_qty'] = 10;
        $shipment = $this->loadData('products_to_ship',
                array('ship_product_sku' => $orderData['products_to_add']['product_1']['filter_sku']));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty($shipment);
    }

    /**
     * <p>Cancel Pending Order From Order Page</p>
     *
     * @depends orderSmoke
     * @param array $orderData
     * @test
     */
    public function cancelPendingOrderFromOrderPage($orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        //Verifying
        $this->assertMessagePresent('success', 'success_canceled_order');
    }

    /**
     * <p>Holding and unholding order after creation.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Hold order;</p>
     * <p>Expected result:</p>
     * <p>Order is holded;</p>
     * <p>4. Unhold order;</p>
     * <p>Expected result:</p>
     * <p>Order is unholded;</p>ss
     *
     * @depends orderSmoke
     * @param array $orderData
     * @test
     */
    public function holdAndUnholdPendingOrderViaOrderPage($orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->clickButton('hold');
        //Verifying
        $this->assertMessagePresent('success', 'success_hold_order');
        //Steps
        $this->clickButton('unhold');
        //Verifying
        $this->assertMessagePresent('success', 'success_unhold_order');
    }

    /**
     * <p>TL-MAGE-321:Reorder.</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add products;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Credit Card';</p>
     * <p>10.Choose any from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>12. Edit order (add products and change billing address);</p>
     * <p>13. Submit order;</p>
     * <p>Expected results:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     * <p>New order during reorder is created.</p>
     * <p>Message "The order has been created." is displayed.</p>
     *
     * @depends orderSmoke
     * @param array $orderData
     * @test
     */
    public function reorderPendingOrder($orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->clickButton('reorder');
        $this->saveForm('submit_order', false);
        $this->orderHelper()->defineOrderId();
        $this->validatePage();
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
    }
}