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
 * Tests for payment methods. Frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CheckoutMultipleAddresses_WithRegistration_PaymentMethodsTest extends Mage_Selenium_TestCase
{
    protected static $useTearDown = false;

    protected function tearDown()
    {
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        if (!empty(self::$useTearDown)) {
            $this->loginAdminUser();
            $this->systemConfigurationHelper()->useHttps('frontend', 'no');
        }
    }
    /**
     * <p>Creating Simple product</p>
     *
     * @test
     * @return array $productData
     */
    public function preconditionsCreateProduct()
    {
        //Data
        $productData = $this->loadData('simple_product_for_order');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * <p>Payment methods without 3D secure.</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with Registering</p>
     * <p>5. Fill in Select Addresses page.</p>
     * <p>6. Click 'Continue to Shipping Information' button.</p>
     * <p>7. Fill in Shipping Information page</p>
     * <p>8. Click 'Continue to Billing Information' button.</p>
     * <p>9. Select Payment Method(by data provider).</p>
     * <p>10. Click 'Continue to Review Your Order' button.</p>
     * <p>11. Verify information into "Place Order" page</p>
     * <p>12. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.</p>
     *
     * @param $payment
     * @param $productData
     * @depends preconditionsCreateProduct
     * @dataProvider differentPaymentMethodsWithout3DDataProvider
     * @test
     */
    public function differentPaymentMethodsWithout3D($payment, $productData)
    {
        //Data
        $paymentData = $this->loadData('front_payment_' . $payment);
        $checkoutData = $this->loadData('multiple_payment_methods_register',
                                        array ('payment_data' => $paymentData,
                                              'products_to_add/product_1' => $productData));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        if ($payment != 'checkmoney') {
            $payment .= '_without_3Dsecure';
        }
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($payment);
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    public function differentPaymentMethodsWithout3DDataProvider()
    {
        return array(
            array('paypaldirect'),
            array('savedcc'),
            array('paypaldirectuk'),
            array('checkmoney'),
            array('payflowpro'),
            array('authorizenet')
        );
    }

    /**
     * <p>Payment methods with 3D secure.</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with Registering</p>
     * <p>5. Fill in Select Addresses page.</p>
     * <p>6. Click 'Continue to Shipping Information' button.</p>
     * <p>7. Fill in Shipping Information page</p>
     * <p>8. Click 'Continue to Billing Information' button.</p>
     * <p>9. Select Payment Method(by data provider).</p>
     * <p>10. Click 'Continue to Review Your Order' button.</p>
     * <p>11. Enter 3D security code.</p>
     * <p>12. Verify information into "Place Order" page</p>
     * <p>13. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.</p>
     *
     * @param $payment
     * @param $productData
     * @depends preconditionsCreateProduct
     * @dataProvider differentPaymentMethodsWith3DDataProvider
     * @test
     */
    public function differentPaymentMethodsWith3D($payment, $productData)
    {
        //For Clean Up
        if ($payment == 'authorizenet') {
            self::$useTearDown = TRUE;
        }
        //Data
        $paymentData = $this->loadData('front_payment_' . $payment);
        $checkoutData = $this->loadData('multiple_payment_methods_register',
                                        array ('payment_data' => $paymentData,
                                              'products_to_add/product_1' => $productData));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        //Steps
        $this->loginAdminUser();
        $this->systemConfigurationHelper()->useHttps('frontend', 'yes');
        $this->systemConfigurationHelper()->configure($payment . '_with_3Dsecure');
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    public function differentPaymentMethodsWith3DDataProvider()
    {
        return array(
            array('paypaldirect'),
            array('savedcc'),
            array('paypaldirectuk'),
            array('payflowpro'),
            array('authorizenet')
        );
    }
}