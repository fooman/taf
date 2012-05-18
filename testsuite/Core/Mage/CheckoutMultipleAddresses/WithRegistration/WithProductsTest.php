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
 * Checkout Multiple Addresses tests with different product types
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CheckoutMultipleAddresses_WithRegistration_WithProductsTest extends Mage_Selenium_TestCase
{
    private static $productTypes = array('simple', 'virtual', 'downloadable',
                                         'bundle', 'configurable', 'grouped');

    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
    }

    /**
     * <p>Create all types of products</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        foreach (self::$productTypes as $type) {
            $method = 'create' . ucfirst($type) . 'Product';
            $products[$type] = $this->productHelper()->$method();
        }
        return $products;
    }

    /**
     * <p>Checkout with multiple addresses simple and virtual/downloadable products</p>
     * <p>Preconditions:</p>
     * <p>1.Products are created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with log in</p>
     * <p>5. Fill in Select Addresses page.</p>
     * <p>6. Click "Continue to Shipping Information" button;</p>
     * <p>7. Fill in Shipping Information tab and click "Continue to Billing Information";</p>
     * <p>8. Fill in Billing Information tab and click "Continue to Review Your Order";</p>
     * <p>9. Verify information into "Order Review" tab;</p>
     * <p>10. Place order;</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful;</p>
     *
     * @param string $productType
     * @param array $products
     *
     * @test
     * @dataProvider virtualProductsDataProvider
     * @depends preconditionsForTests
     */
    public function withVirtualTypeOfProducts($productType, $products)
    {
        //Data
        $simple = $products['simple']['simple']['product_name'];
        $virtual = $products['configurable'][$productType]['product_name'];
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register', null,
                                           array('product_1'=> $simple,
                                                'product_2' => $virtual));
        //Steps and Verify
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertMessagePresent('success', 'success_checkout');
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    /**
     * <p>Checkout with multiple addresses grouped products</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with log in</p>
     * <p>5. Fill in Select Addresses page.</p>
     * <p>6. Click "Continue to Shipping Information" button;</p>
     * <p>7. Fill in Shipping Information tab and click "Continue to Billing Information";</p>
     * <p>8. Fill in Billing Information tab and click "Continue to Review Your Order";</p>
     * <p>9. Verify information into "Order Review" tab;</p>
     * <p>10. Place order;</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful;</p>
     *
     * @param array $products
     * @param string $productType
     *
     * @test
     * @dataProvider productsDataProvider
     * @depends preconditionsForTests
     */
    public function withGroupedProduct($productType, $products)
    {
        //Data
        $simple = $products['simple']['simple']['product_name'];
        $grouped = $products['grouped']['grouped']['product_name'];
        $optionParams = $products['grouped'][$productType]['product_name'];
        $productOptions = $this->loadDataSet('Products', 'grouped_options_to_add_to_shopping_cart', null,
                                             array('subProduct_1' => $optionParams));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register', null,
                                           array('product_1'       => $simple,
                                                'product_2'        => $grouped,
                                                'option_product_2' => $productOptions));
        $checkoutData['shipping_data'] = $this->loadDataSet('MultipleAddressesCheckout',
                                                            'multiple_with_login/shipping_data', null,
                                                            array('product_1' => $simple,
                                                                 'product_2'  => $optionParams));
        //Steps and Verify
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertMessagePresent('success', 'success_checkout');
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    /**
     * <p>Checkout with multiple addresses bundle products</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with log in</p>
     * <p>5. Fill in Select Addresses page.</p>
     * <p>6. Click "Continue to Shipping Information" button;</p>
     * <p>7. Fill in Shipping Information tab and click "Continue to Billing Information";</p>
     * <p>8. Fill in Billing Information tab and click "Continue to Review Your Order";</p>
     * <p>9. Verify information into "Order Review" tab;</p>
     * <p>10. Place order;</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful;</p>
     *
     * @param string $productType
     * @param array $products
     *
     * @test
     * @dataProvider virtualProductsDataProvider
     * @depends preconditionsForTests
     */
    public function withBundleProduct($productType, $products)
    {
        //Data
        $simple = $products['simple']['simple']['product_name'];
        $bundle = $products['bundle']['bundle']['product_name'];
        $optionParams = $products['bundle']['bundleOption'];
        foreach ($optionParams as $key => $value) {
            $optionParams[$key] = $products['bundle'][$productType]['product_name'];
        }
        $productOptions = $this->loadDataSet('Products', 'bundle_options_to_add_to_shopping_cart', null, $optionParams);
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register', null,
                                           array('product_1'       => $simple,
                                                'product_2'        => $bundle,
                                                'option_product_2' => $productOptions));
        //Steps and Verify
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertMessagePresent('success', 'success_checkout');
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    /**
     * <p>Checkout with multiple addresses configurable product with associated products</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with log in</p>
     * <p>5. Fill in Select Addresses page.</p>
     * <p>6. Click "Continue to Shipping Information" button;</p>
     * <p>7. Fill in Shipping Information tab and click "Continue to Billing Information";</p>
     * <p>8. Fill in Billing Information tab and click "Continue to Review Your Order";</p>
     * <p>9. Verify information into "Order Review" tab;</p>
     * <p>10. Place order;</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful;</p>
     *
     * @param string $productType
     * @param array $products
     *
     * @test
     * @dataProvider productsDataProvider
     * @depends preconditionsForTests
     */
    public function withConfigurable($productType, $products)
    {
        //Data
        $simple = $products['simple']['simple']['product_name'];
        $configurable = $products['configurable']['configurable']['product_name'];
        $optionParams = $products['configurable']['configurableOption'];
        $optionParams['custom_option_dropdown'] = $products['configurable'][$productType . 'Option']['option_front'];
        $productOptions = $this->loadDataSet('Products', 'configurable_options_to_add_to_shopping_cart', $optionParams);
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register', null,
                                           array('product_1'       => $simple,
                                                'product_2'        => $configurable,
                                                'option_product_2' => $productOptions));
        //Steps and Verify
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertMessagePresent('success', 'success_checkout');
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    /**
     * <p>Checkout with multiple addresses Downloadable product with associated links</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with log in</p>
     * <p>5. Fill in Select Addresses page.</p>
     * <p>6. Click "Continue to Shipping Information" button;</p>
     * <p>7. Fill in Shipping Information tab and click "Continue to Billing Information";</p>
     * <p>8. Fill in Billing Information tab and click "Continue to Review Your Order";</p>
     * <p>9. Verify information into "Order Review" tab;</p>
     * <p>10. Place order;</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful;</p>
     *
     * @param array $products
     *
     * @test
     * @depends preconditionsForTests
     */
    public function withDownloadable($products)
    {
        //Data
        $simple = $products['simple']['simple']['product_name'];
        $downloadable = $products['downloadable']['downloadable']['product_name'];
        $optionParams = $products['downloadable']['downloadableOption'];
        $productOptions = $this->loadDataSet('Products', 'downloadable_options_to_add_to_shopping_cart', $optionParams);
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register', null,
                                           array('product_1'       => $simple,
                                                'product_2'        => $downloadable,
                                                'option_product_2' => $productOptions));
        //Steps and Verify
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertMessagePresent('success', 'success_checkout');
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    public function productsDataProvider()
    {
        return array(
            array('simple'),
            array('virtual'),
            array('downloadable')
        );
    }

    public function virtualProductsDataProvider()
    {
        return array(
            array('simple'),
            array('virtual')
        );
    }

    /**
     * <p>Checkout with multiple addresses products with custom options</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with log in</p>
     * <p>5. Fill in Select Addresses page.</p>
     * <p>6. Click "Continue to Shipping Information" button;</p>
     * <p>7. Fill in Shipping Information tab and click "Continue to Billing Information";</p>
     * <p>8. Fill in Billing Information tab and click "Continue to Review Your Order";</p>
     * <p>9. Verify information into "Order Review" tab;</p>
     * <p>10. Place order;</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful;</p>
     *
     * @param string $productType
     * @param array $products
     *
     * @test
     * @dataProvider withCustomOptionsDataProvider
     * @depends preconditionsForTests
     */
    public function withCustomOptions($productType, $products)
    {
        //Data
        $simple = $products['simple']['simple']['product_name'];
        $productData = $products[$productType];
        if ($productType == 'simple') {
            $productData = $products['bundle'];
        }
        $secondProduct = $productData[$productType]['product_name'];
        $optionParams = (isset($productData[$productType . 'Option'])) ? $productData[$productType . 'Option']
            : array();
        $productOptions = array();
        if (!empty($optionParams)) {
            $name = '_options_to_add_to_shopping_cart';
            if ($productType == 'configurable' || $productType == 'downloadable') {
                $productOptions = $this->loadDataSet('Products', $productType . $name, $optionParams);
            } else {
                $productOptions = $this->loadDataSet('Products', $productType . $name, null, $optionParams);
            }
        }
        $customOptions = $this->loadDataSet('Products', 'custom_options_to_add_to_shopping_cart');
        $productOptions = array_merge($productOptions, $customOptions);
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register', null,
                                           array('product_1'       => $simple,
                                                'product_2'        => $secondProduct,
                                                'option_product_2' => $productOptions));
        $search = $this->loadDataSet('Product', 'product_search', array('product_name'=> $secondProduct));
        $customOptionsData['custom_options_data'] = $this->loadDataSet('Product', 'custom_options_data');
        //Steps and Verify
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->fillProductTab($customOptionsData, 'custom_options');
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertMessagePresent('success', 'success_checkout');
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    public function withCustomOptionsDataProvider()
    {
        return array(
            array('virtual'),
            array('downloadable'),
            array('bundle'),
            array('configurable'),
            array('simple')
        );
    }
}
