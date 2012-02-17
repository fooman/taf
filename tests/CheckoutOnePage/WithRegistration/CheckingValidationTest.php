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
 * One page Checkout  - checking validation tests
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CheckoutOnePage_WithRegistration_CheckingValidationTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->addParameter('id', '');
    }

    /**
     * <p>Creating Simple product</p>
     * @test
     * @return string
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadData('simple_product_for_order');
        //Steps and Verification
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');

        return $simple['general_name'];
    }

    /**
     * <p>Product not defined to shopping cart</p>
     * <p>Steps</p>
     * <p>1. Do not add product to shopping Cart</p>
     * <p>2. Click "Checkout" button</p>
     * <p>Expected Result</p>
     * <p>Shopping Cart is Empty page appears</p>
     * @test
     */
    public function emptyShoppingCart()
    {
        //Steps
        $this->logoutCustomer();
        $this->clickControl('link', 'checkout');
        $this->validatePage('shopping_cart');
        //Verifying
        $this->assertElementPresent($this->_getControlXpath('message', 'shopping_cart_is_empty'),
                                    'Shopping cart is not empty');
    }

    /**
     * <p>Checkout method is not defined</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Leave Checkout Method options empty</p>
     * <p>2. Click "Continue" button</p>
     * <p>Expected Result</p>
     * <p>Information window appears with message "Please choose to register or to checkout as a guest"</p>
     *
     * @param string $simpleSku
     *
     * @depends preconditionsForTests
     * @test
     */
    public function checkoutMethodNotDefined($simpleSku)
    {
        //Data
        $checkoutData = $this->loadData('with_register_flatrate_checkmoney_different_address',
                                        array('general_name'        => $simpleSku,
                                             'checkout_as_customer' => '%noValue%',));
        //Steps
        $this->logoutCustomer();
        try {
            //Steps
            $expected = 'Please choose to register or to checkout as a guest';
            $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
            $this->fail('Expected message is not displayed: [' . $expected . ']');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            //Verification
            $this->assertSame($expected, $e->toString());
            $this->clearMessages('verification');
        }
    }

    /**
     * <p>Empty required fields in billing address tab</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Leave billing information fields empty</p>
     * <p>4. Click "Continue" button</p>
     * <p>5. Verify error message;</p>
     * <p>6. Repeat scenario for all required fields in current tab;</p>
     * <p>Expected result:</p>
     * <p>Error message appears</p>
     *
     * @param string $field
     * @param string $errorMessage
     * @param string $simpleSku
     *
     * @dataProvider emptyRequiredFieldsInBillingAddressDataProvider
     * @depends preconditionsForTests
     * @test
     */
    public function emptyRequiredFieldsInBillingAddress($field, $errorMessage, $simpleSku)
    {
        //Data
        $checkoutData = $this->loadData('with_register_flatrate_checkmoney_different_address',
                                        array('general_name' => $simpleSku,
                                             $field          => ''));
        //Steps
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        try {
            //Steps
            $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
            $this->fail('Expected message is not displayed: [' . $errorMessage . ']');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            //Verification
            $this->assertSame($errorMessage, $e->toString());
            $this->clearMessages('verification');
        }
    }

    public function emptyRequiredFieldsInBillingAddressDataProvider()
    {
        return array(
            array('billing_first_name', '"First Name": This is a required field.'),
            array('billing_last_name', '"Last Name": This is a required field.'),
            array('billing_email', '"Email Address": This is a required field.'),
            array('billing_street_address_1', '"Address": This is a required field.'),
            array('billing_city', '"City": This is a required field.'),
            array('billing_state', '"State/Province": Please select an option.'),
            array('billing_zip_code', '"Zip/Postal Code": This is a required field.'),
            array('billing_country', '"Country": Please select an option.'),
            array('billing_telephone', '"Telephone": This is a required field.'),
            array('billing_password', '"Password": This is a required field.' . "\n"
                . '"Confirm Password": Please make sure your passwords match.'),
            array('billing_confirm_password', '"Confirm Password": This is a required field.')
        );
    }

    /**
     * <p>Incorrect password length</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in required fields by regular data. </p>
     * <p>4. Fill in 'Password' field by values with incorrect length.</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Error message appears</p>
     *
     * @param string $simpleSku
     *
     * @depends preconditionsForTests
     * @test
     */
    public function incorrectPasswordLength($simpleSku)
    {
        //Data
        $billingPassword = $this->generate('string', 5, ':punct:');
        $checkoutData = $this->loadData('with_register_flatrate_checkmoney_different_address',
                                        array('general_name'            => $simpleSku,
                                             'billing_password'         => $billingPassword,
                                             'billing_confirm_password' => $billingPassword));
        //Steps
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        try {
            //Steps
            $expected = '"Password": Please enter 6 or more characters. Leading or trailing spaces will be ignored.';
            $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
            $this->fail('Expected message is not displayed: [' . $expected . ']');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            //Verification
            $this->assertSame($expected, $e->toString());
            $this->clearMessages('verification');
        }
    }

    /**
     * <p>Incorrect Email</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in required fields by regular data. </p>
     * <p>4. Fill in 'Email' field by incorrect values.</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Error message appears</p>
     *
     * @param string $wrongValue
     * @param string $simpleSku
     *
     * @depends preconditionsForTests
     * @dataProvider incorrectEmailDataProvider
     * @test
     */
    public function incorrectEmail($wrongValue, $simpleSku)
    {
        //Data
        $checkoutData = $this->loadData('with_register_flatrate_checkmoney_different_address',
                                        array('general_name' => $simpleSku,
                                             'billing_email' => $wrongValue));
        //Steps
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        try {
            //Steps
            $expected = '"Email Address": Please enter a valid email address. For example johndoe@domain.com.';
            $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
            $this->fail('Expected message is not displayed: [' . $expected . ']');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            //Verification
            $this->assertSame($expected, $e->toString());
            $this->clearMessages('verification');
        }
    }

    public function incorrectEmailDataProvider()
    {
        return array(
            array('invalid'),
            array('test@invalidDomain'),
            array('te@st@unknown-domain.com')
        );
    }

    /**
     * <p>Exist Email</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in required fields by regular data. </p>
     * <p>4. Fill in 'Email' field by incorrect values.</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Error message appears</p>
     *
     * @param string $simpleSku
     *
     * @depends preconditionsForTests
     * @test
     */
    public function existEmail($simpleSku)
    {
        //Data
        $userData = $this->loadData('customer_account_register');
        $checkoutData = $this->loadData('with_register_flatrate_checkmoney_different_address',
                                        array('general_name' => $simpleSku,
                                             'billing_email' => $userData['email']));
        //Steps
        $this->logoutCustomer();
        $this->navigate('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->logoutCustomer();
        $expected = $this->getUimapPage('frontend', 'onepage_checkout')->findMessage('exist_email_alert');
        try {
            //Steps
            $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
            $this->fail('Expected message is not displayed: [' . $expected . ']');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            //Verification
            $this->assertSame($expected, $e->toString());
            $this->clearMessages('verification');
        }
    }

    /**
     * @param string $dataName
     * @param string $simpleSku
     *
     * @depends preconditionsForTests
     * @dataProvider specialValuesForAddressFieldsDataProvider
     * @test
     */
    public function specialValuesForAddressFields($dataName, $simpleSku)
    {
        //Data
        $checkoutData = $this->loadData($dataName, array('general_name' => $simpleSku));
        //Steps
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    public function specialValuesForAddressFieldsDataProvider()
    {
        return array(
            array('with_register_flatrate_checkmoney_long_address'),
            array('with_register_flatrate_checkmoney_special_address')
        );
    }

    /**
     * <p>Empty required fields in shipping address tab</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill billing information fields by regular data</p>
     * <p>4. Click 'Continue' button.</p>
     * <p>5. Leave shipping information fields empty</p>
     * <p>6. Click "Continue" button</p>
     * <p>7. Verify error message;</p>
     * <p>8. Repeat scenario for all required fields in current tab;</p>
     * <p>Expected result:</p>
     * <p>Error message appears</p>
     *
     * @param string $field
     * @param string $message
     * @param string $simpleSku
     *
     * @dataProvider emptyRequiredFieldsInShippingAddressDataProvider
     * @depends preconditionsForTests
     * @test
     */
    public function emptyRequiredFieldsInShippingAddress($field, $message, $simpleSku)
    {
        //Data
        $checkoutData = $this->loadData('with_register_flatrate_checkmoney_different_address',
                                        array('general_name' => $simpleSku,
                                             $field          => ''));
        //Steps
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        try {
            //Steps
            $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
            $this->fail('Expected message is not displayed: [' . $message . ']');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            //Verification
            $this->assertSame($message, $e->toString());
            $this->clearMessages('verification');
        }
    }

    public function emptyRequiredFieldsInShippingAddressDataProvider()
    {
        return array(
            array('shipping_first_name', '"First Name": This is a required field.'),
            array('shipping_last_name', '"Last Name": This is a required field.'),
            array('shipping_street_address_1', '"Address": This is a required field.'),
            array('shipping_city', '"City": This is a required field.'),
            array('shipping_state', '"State/Province": Please select an option.'),
            array('shipping_zip_code', '"Zip/Postal Code": This is a required field.'),
            array('shipping_country', '"Country": Please select an option.'),
            array('shipping_telephone', '"Telephone": This is a required field.')
        );
    }

    /**
     * <p>Using long values for fill billing information form</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in Billing Information tab</p>
     * <p>4. Select "Ship to different address" option</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>6. Fill in Shipping Information tab.</p>
     * <p>7. Fill in one field by long value</p>
     * <p>8. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Error massages appears</p>
     *
     * @param string $field
     * @param string $message
     * @param string $simpleSku
     *
     * @depends preconditionsForTests
     * @dataProvider addressLongValuesDataProvider
     * @test
     */
    public function billingAddressLongValues($field, $message, $simpleSku)
    {
        //Data
        $checkoutData = $this->loadData('with_register_flatrate_checkmoney_different_address',
                                        array('general_name'     => $simpleSku,
                                             'billing_' . $field => $this->generate('string', 256, ':alpha:')));
        //Steps and Verification
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        try {
            //Steps
            $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
            $this->fail('Expected message is not displayed: [' . $message . ']');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            //Verification
            $this->assertSame($message, $e->toString());
            $this->clearMessages('verification');
        }
    }

    /**
     * <p>Using long values for fill billing information form</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in Billing Information tab</p>
     * <p>4. Fill in one field by long value</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>Error massages appears</p>
     *
     * @param string $field
     * @param string $message
     * @param string $simpleSku
     *
     * @depends preconditionsForTests
     * @dataProvider addressLongValuesDataProvider
     * @test
     */
    public function shippingAddressLongValues($field, $message, $simpleSku)
    {
        //Data
        $checkoutData = $this->loadData('with_register_flatrate_checkmoney_different_address',
                                        array('general_name'      => $simpleSku,
                                             'shipping_' . $field => $this->generate('string', 256, ':alpha:')));
        //Steps and Verification
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        try {
            //Steps
            $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            //Verification
            $this->assertSame($message, $e->toString());
            $this->clearMessages('verification');
        }
    }

    public function addressLongValuesDataProvider()
    {
        return array(
            array('first_name', '"First Name" length must be equal or less than 255 characters.'),
            array('last_name', '"Last Name" length must be equal or less than 255 characters.'),
            array('company', '"Company" length must be equal or less than 255 characters.'),
            array('street_address_1', '"Street Address" length must be equal or less than 255 characters.'),
            array('street_address_2', '"Street Address" length must be equal or less than 255 characters.'),
            array('city', '"City" length must be equal or less than 255 characters.'),
            array('telephone', '"Telephone" length must be equal or less than 255 characters.'),
            array('fax', '"Fax" length must be equal or less than 255 characters.')
        );
    }
}
