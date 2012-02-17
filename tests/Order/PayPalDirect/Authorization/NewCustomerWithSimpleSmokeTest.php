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
 * Cancel orders
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_PayPalDirect_Authorization_NewCustomerWithSimpleSmokeTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->addParameter('id', '0');
    }

    /**
     * <p>Create Pro Merchant Account on PayPal sandbox</p>
     *
     * @return array
     * @test
     */
    public function createPayPalProAccountAndActivate()
    {
        $this->goToArea('paypal-developer');
        $this->paypalHelper()->paypalDeveloperLogin('paypal_developer_login');
        $api = $this->paypalHelper()->createPayPalProAccount('paypal_sandbox_new_pro_account');
        $data = $this->loadData('paypaldirect_without_3Dsecure',
            array('email_associated_with_paypal_merchant_account' => $api['test_account'],
                  'api_username'                                  => $api['api_username'],
                  'api_password'                                  => $api['api_password'],
                  'api_signature'                                 => $api['signature']));
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($data);

        return $api;
    }

    /**
     * <p>Create Buyers Accounts on PayPal sandbox</p>
     *
     * @depends createPayPalProAccountAndActivate
     * @return array $accounts
     * @test
     */
    public function createPayPalBuyerAccounts()
    {
        $this->goToArea('paypal-developer');
        $this->paypalHelper()->paypalDeveloperLogin('paypal_developer_login');
        $accounts = $this->paypalHelper()->createBuyerAccounts(array('visa' , 'mastercard', 'discover', 'amex'));

        return $accounts;
    }

    /**
     * <p>Create Simple Product for tests</p>
     *
     * @depends createPayPalBuyerAccounts
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
     * <p>Smoke test for order without 3D secure</p>
     *
     * @depends createSimpleProduct
     * @depends createPayPalBuyerAccounts
     * @param string $simpleSku
     * @param array $accounts
     * @return array
     * @test
     */
    public function orderWithout3DSecureSmoke($simpleSku, $accounts)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_paypaldirect_flatrate',
            array('filter_sku' => $simpleSku, 'payment_info' => $accounts['mastercard']['credit_card']));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');

        return $orderData;
    }

    /**
     * <p>Create order with PayPal Direct using all types of credit card</p>
     *
     * @depends orderWithout3DSecureSmoke
     * @depends createPayPalBuyerAccounts
     * @dataProvider cardPayFlowProVerisignDataProvider
     * @param string $card
     * @param array $orderData
     * @param array $accounts
     *
     * @test
     */
    public function orderWithDifferentCreditCard($card, $orderData, $accounts)
    {
        //Data
        $orderData['payment_data']['payment_info'] = $accounts[$card]['credit_card'];
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
    }

    /**
     * <p>Data provider for orderWithDifferentCreditCard test</p>
     *
     * @return array
     */
    public function cardPayFlowProVerisignDataProvider()
    {
        return array(
            array('amex'),
            array('visa'),
        );
    }

    /**
     * <p>Website payments pro. Full Invoice With different types of Capture</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'paypal direct'</p>
     * <p>10.Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>13.Create invoice.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer. Invoice is created</p>
     *
     * @depends orderWithout3DSecureSmoke
     * @dataProvider fullInvoiceWithDifferentTypesOfCaptureDataProvider
     * @param string $captureType
     * @param array $orderData
     * @test
     */
    public function fullInvoiceWithDifferentTypesOfCapture($captureType, $orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty($captureType);
    }

    /**
     * <p>Data provider for fullInvoiceWithDifferentTypesOfCapture test</p>
     *
     * @return array
     */
    public function fullInvoiceWithDifferentTypesOfCaptureDataProvider()
    {
        return array(
            array('Capture Online'),
            array('Capture Offline'),
            array('Not Capture')
        );
    }

    /**
     * <p>Partial invoice with different types of capture</p>
     *
     * @depends orderWithout3DSecureSmoke
     * @dataProvider partialInvoiceWithDifferentTypesOfCaptureDataProvider
     * @param string $captureType
     * @param array $orderData
     * @test
     */
    public function partialInvoiceWithDifferentTypesOfCapture($captureType, $orderData)
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
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty($captureType, $invoice);
    }

    /**
     * <p>Data provider for partialInvoiceWithDifferentTypesOfCapture test</p>
     *
     * @return array
     */
    public function partialInvoiceWithDifferentTypesOfCaptureDataProvider()
    {
        return array(
            array('Capture Online'),
            array('Capture Offline')
        );
    }

    /**
     * <p>PayPal Direct. Full Refund</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'PayPal Direct - Visa'</p>
     * <p>10. Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>13.Invoice order.</p>
     * <p>14.Make refund offline.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer. Refund Offline is successful</p>
     *
     * @depends orderWithout3DSecureSmoke
     * @dataProvider creditMemoDataProvider
     * @param string $captureType
     * @param string $refundType
     * @param array $orderData
     * @test
     */
    public function fullCreditMemo($captureType, $refundType, $orderData)
    {
        //Steps and Verifying
        $this->addParameter('invoice_id', 1);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $orderId = $this->orderHelper()->defineOrderId();
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty($captureType);
        $this->navigate('manage_sales_invoices');
        $this->orderInvoiceHelper()->openInvoice(array('filter_order_id' => $orderId));
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty($refundType);
    }

    /**
     * <p>Partial Credit Memo</p>
     *
     * @depends orderWithout3DSecureSmoke
     * @depends createPayPalProAccountAndActivate
     * @dataProvider creditMemoDataProvider
     * @param string $captureType
     * @param string $refundType
     * @param array $orderData
     * @param array $api
     * @test
     */
    public function partialCreditMemo($captureType, $refundType, $orderData, $api)
    {
        //Data
        $orderData['products_to_add']['product_1']['product_qty'] = 10;
        $creditMemo = $this->loadData('products_to_refund',
                array('return_filter_sku' => $orderData['products_to_add']['product_1']['filter_sku']));
        $data = $this->loadData('paypaldirect_without_3Dsecure',
                array('email_associated_with_paypal_merchant_account' => $api['test_account'],
                      'api_username'                                  => $api['api_username'],
                      'api_password'                                  => $api['api_password'],
                      'api_signature'                                 => $api['signature']));
        //Steps and Verifying
        $this->addParameter('invoice_id', 1);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($data);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $orderId = $this->orderHelper()->defineOrderId();
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty($captureType);
        $this->navigate('manage_sales_invoices');
        $this->orderInvoiceHelper()->openInvoice(array('filter_order_id' => $orderId));
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty($refundType, $creditMemo);
    }

    /**
     * <p>Data provider for partialCreditMemo test</p>
     *
     * @return array
     */
    public function creditMemoDataProvider()
    {
        return array(
            array('Capture Online', 'refund'),
            array('Capture Online', 'refund_offline'),
            array('Capture Offline', 'refund_offline'),
        );
    }

    /**
     * <p>Shipment for order</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add products;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Paypal Direct';</p>
     * <p>10.Choose any from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>12. Invoice order;</p>
     * <p>13. Ship order;</p>
     * <p>Expected result:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     * <p>Order is invoiced and shipped successfully</p>
     *
     * @depends orderWithout3DSecureSmoke
     * @param array $orderData
     * @test
     */
    public function fullShipmentForOrderWithoutInvoice($orderData)
    {
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
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
     * <p>Order is unholded;</p>
     *
     * @depends orderWithout3DSecureSmoke
     * @param array $orderData
     * @test
     */
    public function holdAndUnholdPendingOrderViaOrderPage($orderData)
    {
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButton('hold');
        $this->assertMessagePresent('success', 'success_hold_order');
        $this->clickButton('unhold');
        $this->assertMessagePresent('success', 'success_unhold_order');
    }

    /**
     * <p>Cancel Pending Order From Order Page</p>
     *
     * @depends orderWithout3DSecureSmoke
     * @param array $orderData
     * @test
     */
    public function cancelPendingOrderFromOrderPage($orderData)
    {
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');
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
     * @depends orderWithout3DSecureSmoke
     * @param array $orderData
     * @test
     */
    public function reorderPendingOrder($orderData)
    {
        //Data
        $errors = array();
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->clickButton('reorder');
        $data = $orderData['payment_data']['payment_info'];
        $emptyFields = array('card_number', 'card_verification_number');
        foreach ($emptyFields as $field) {
            $xpath = $this->_getControlXpath('field', $field);
            $value = $this->getAttribute($xpath . '@value');
            if ($value) {
                $errors[] = "Value for field '$field' should be empty, but now is $value";
            }
        }
        $this->fillForm(array('card_number' => $data['card_number'],
            'card_verification_number' => $data['card_verification_number']));
        $this->saveForm('submit_order', false);
        $this->orderHelper()->defineOrderId();
        $this->validatePage();
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        if ($errors) {
            $this->fail(implode("\n", $errors));
        }
    }

    /**
     * <p>Void order.</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'PayPal Direct - Visa'</p>
     * <p>10. Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>13.Void Order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer. Void successful</p>
     *
     * @depends orderWithout3DSecureSmoke
     * @param array $orderData
     * @test
     */
    public function voidPendingOrderFromOrderPage($orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->clickButtonAndConfirm('void', 'confirmation_to_void');
        //Verifying
        $this->assertMessagePresent('success', 'success_voided_order');
    }

    /**
     * <p>Create Orders using different payment methods with 3DSecure</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Press 'Add Products' button.</p>
     * <p>6.Add simple product.</p>
     * <p>7.Fill all required fields in billing address form.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check shipping method</p>
     * <p>10.Check payment method</p>
     * <p>11.Validate card with 3D secure</p>
     * <p>12.Submit order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer.</p>
     *
     * @depends orderWithout3DSecureSmoke
     * @dataProvider createOrderWith3DSecureDataProvider
     * @param string $card
     * @param bool $needSetUp
     * @param array $orderData
     * @test
     */
    public function createOrderWith3DSecure($card, $needSetUp, $orderData)
    {
        //Data
        $orderData['payment_data']['payment_info'] = $this->loadData($card);
        //Steps
        if ($needSetUp) {
            $this->systemConfigurationHelper()->useHttps('admin', 'yes');
            $this->systemConfigurationHelper()->configure('paypaldirect_with_3Dsecure');
        }
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
    }

    /**
     * <p>Data provider for createOrderWith3DSecure test</p>
     *
     * @return array
     */
    public function createOrderWith3DSecureDataProvider()
    {
        return array(
            array('else_visa_direct', true),
            array('else_mastercard', false)
        );
    }

    /**
     * <p>Delete test accounts</p>
     *
     * @depends createPayPalProAccountAndActivate
     * @depends createPayPalBuyerAccounts
     * @param array $api
     * @param array $accounts
     * @test
     */
    public function deleteTestAccounts($api, $accounts)
    {
        $this->goToArea('paypal-developer');
        $this->paypalHelper()->paypalDeveloperLogin('paypal_developer_login');
        if (isset($api['test_account'])) {
            $this->paypalHelper()->deleteAccount($api['test_account']);
        }
        foreach ($accounts as $card) {
            if (isset($card['email'])) {
                $this->paypalHelper()->deleteAccount($card['email']);
            }
        }
    }
}
