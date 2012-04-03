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
 * Wishlist tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Wishlist_Wishlist extends Mage_Selenium_TestCase
{
    /**
     * <p>Login as a registered user</p>
     */
    public function setUpBeforeTests()
    {
        $this->logoutCustomer();
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->addParameter('id', '0');
    }

    /**
     * <p>Create a new customer for tests</p>
     * @return array Customer 'email' and 'password'
     * @test
     * @group preConditions
     */
    public function preconditionsCreateCustomer()
    {
        $userData = $this->loadData('generic_customer_account');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        return array('email'    => $userData['email'],
                     'password' => $userData['password']);
    }

    /**
     * <p>Creates Category to use during tests</p>
     * @return array Category 'name' and 'path'
     * @test
     */
    public function preconditionsCreateCategory()
    {
        //Data
        $category = $this->loadData('sub_category_required');
        //Steps and Verification
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');

        return array('name' => $category['name'],
                     'path' => $category['parent_category'] . '/' . $category['name']);
    }

    /**
     * <p>Creating configurable product</p>
     * @return array
     * @test
     * @group preConditions
     */
    public function preconditionsCreateConfigurableAttribute()
    {
        //Data
        $attrData = $this->loadData('product_attribute_dropdown_with_options',
                                    null, array('admin_title', 'attribute_code'));
        $associatedAttributes = $this->loadData('associated_attributes',
                                                array('General' => $attrData['attribute_code']));
        //Steps
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        //Verifying
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        return $attrData;
    }

    /**
     * <p>Create a new product of the specified type</p>
     *
     * @param array $productData Product data to fill in backend
     * @param null|string $productType E.g. 'simple'|'configurable' etc.
     *
     * @return array $productData

     */
    protected function _createProduct(array $productData, $productType)
    {
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, $productType);
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * <p>Create a simple product within a category</p>
     *
     * @depends preconditionsCreateCategory
     * @param array $categoryData
     *
     * @test
     */
    public function preconditionsCreateProductSimple($categoryData)
    {
        $productData = $this->loadData('simple_product_visible',
                                       array('categories' => $categoryData['path']),
                                       array('general_name', 'general_sku'));
        $productSimple = $this->_createProduct($productData, 'simple');
        return $productSimple['general_name'];
    }

    /**
     * <p>Create products of all types for the tests without custom options</p>
     *
     * @depends preconditionsCreateConfigurableAttribute
     * @param array $attrData
     *
     * @return array Array of product names
     * @test
     * @group preConditions
     */
    public function preconditionsCreateAllProductsWithoutCustomOptions($attrData)
    {
        // Create simple product, so that it can be used in Configurable product.
        $simpleData = $this->loadData('simple_product_visible', null, array('general_name', 'general_sku'));
        $productSimple = $this->_createProduct($simpleData, 'simple');
        // Create a configurable product
        $productData = $this->loadData('configurable_product_visible',
                                       array('associated_configurable_data' => '%noValue%',
                                            'configurable_attribute_title'  => $attrData['admin_title']),
                                       array('general_sku', 'general_name'));
        $productConfigurable = $this->_createProduct($productData, 'configurable');
        //Create a virtual product
        $productData = $this->loadData('virtual_product_visible', null, array('general_name', 'general_sku'));
        $productVirtual = $this->_createProduct($productData, 'virtual');
        //Create a downloadable product
        $productData = $this->loadData('downloadable_product_visible',
                                       array('downloadable_information_data' => '%noValue%'),
                                       array('general_name', 'general_sku'));
        $productDownloadable = $this->_createProduct($productData, 'downloadable');
        //Create a grouped product
        $productData = $this->loadData('grouped_product_visible',
                                       array('associated_grouped_data' => '%noValue%'),
                                       array('general_name', 'general_sku'));
        $productGrouped = $this->_createProduct($productData, 'grouped');
        //Create a bundle product
        $productData = $this->loadData('fixed_bundle_visible',
                                       array('bundle_items_data' => '%noValue%'), array('general_name', 'general_sku'));
        $productBundle = $this->_createProduct($productData, 'bundle');

        $allProducts = array('simple'       => $productSimple,
                             'virtual'      => $productVirtual,
                             'downloadable' => $productDownloadable,
                             'grouped'      => $productGrouped,
                             'configurable' => $productConfigurable,
                             'bundle'       => $productBundle);
        return $allProducts;
    }

    /**
     * <p>Create products of all types for the tests with custom options</p>
     *
     * @depends preconditionsCreateConfigurableAttribute
     * @param array $attrData
     *
     * @return array Array of product names
     * @test
     */
    public function preconditionsCreateAllProductsWithCustomOptions($attrData)
    {
        // Create simple product, so that it can be used in Configurable product.
        $simpleData = $this->loadData('simple_product_visible', null, array('general_name', 'general_sku'));
        $simpleData['general_user_attr']['dropdown'][$attrData['attribute_code']] =
            $attrData['option_1']['admin_option_name'];
        $productSimple = $this->_createProduct($simpleData, 'simple');
        // Create a configurable product
        $productData = $this->loadData('configurable_product_visible',
                                       array('configurable_attribute_title' => $attrData['admin_title']),
                                       array('general_sku', 'general_name'));
        $productData['associated_configurable_data'] = $this->loadData('associated_configurable_data',
                                                                       array('associated_search_sku' => $simpleData['general_sku']));
        $productConfigurable = $this->_createProduct($productData, 'configurable');
        //Create a virtual product
        $productData = $this->loadData('virtual_product_visible', null, array('general_name', 'general_sku'));
        $productVirtual = $this->_createProduct($productData, 'virtual');
        //Create a downloadable product
        $productData = $this->loadData('downloadable_product_visible', null, array('general_name', 'general_sku'));
        $productDownloadable = $this->_createProduct($productData, 'downloadable');
        //Create a grouped product
        $productData = $this->loadData('grouped_product_visible',
                                       array('associated_search_name'        => $simpleData['general_name'],
                                            'associated_product_default_qty' => '3'),
                                       array('general_name', 'general_sku'));
        $productGrouped = $this->_createProduct($productData, 'grouped');
        //Create a bundle product
        $productData = $this->loadData('fixed_bundle_visible', null, array('general_name', 'general_sku'));
        $productData['bundle_items_data']['item_1'] = $this->loadData('bundle_item_1',
                                                                      array('bundle_items_search_sku' => $simpleData['general_sku']));
        $productBundle = $this->_createProduct($productData, 'bundle');

        $allProducts = array('simple'       => $productSimple,
                             'virtual'      => $productVirtual,
                             'downloadable' => $productDownloadable,
                             'grouped'      => $productGrouped,
                             'configurable' => $productConfigurable,
                             'bundle'       => $productBundle);
        return $allProducts;
    }

    /**
     * @param array $productDataSet Array of product data
     *
     * @return array Array of product names
     */
    private function _getProductNames($productDataSet)
    {
        $productNamesSet = array();
        foreach ($productDataSet as $productData) {
            $productNamesSet[] = $productData['general_name'];
        }
        return $productNamesSet;
    }

    /**
     * <p>Removes all products from My Wishlist. For all product types</p>
     * <p>Steps:</p>
     * <p>1. Add products to the wishlist</p>
     * <p>2. Remove one product from the wishlist</p>
     * <p>Expected result:</p>
     * <p>The product is no longer in wishlist</p>
     * <p>3. Repeat for all products until the last one</p>
     * <p>4. Remove the last product from the wishlist</p>
     * <p>Expected result:</p>
     * <p>Message 'You have no items in your wishlist.' is displayed</p>
     *
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateAllProductsWithCustomOptions
     *
     * @param $customer
     * @param $productDataSet
     *
     * @test
     */
    public function removeProductsFromWishlist($customer, $productDataSet)
    {
        //Setup
        $productNameSet = $this->_getProductNames($productDataSet);
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontClearWishlist();
        foreach ($productNameSet as $productName) {
            $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($productName);
            $this->assertMessagePresent('success', 'successfully_added_product');
        }
        //Steps
        $lastProductName = end($productNameSet);
        array_pop($productNameSet);
        foreach ($productNameSet as $productName) {
            $this->wishlistHelper()->frontRemoveProductsFromWishlist($productName); // Remove all but last
            //Verify
            $this->assertTrue(is_array($this->wishlistHelper()->frontWishlistHasProducts($productName)),
                              'Product ' . $productName . ' is in the wishlist, but should be removed.');
        }
        //Steps
        $this->wishlistHelper()->frontRemoveProductsFromWishlist($lastProductName); //Remove the last one
        //Verify
        $this->assertTrue($this->controlIsPresent('pageelement', 'no_items'), $this->getParsedMessages());
        //Cleanup
    }

    /**
     * <p>Adds a simple product to Wishlist from Catalog page.</p>
     * <p>Steps:</p>
     * <p>1. Open category</p>
     * <p>2. Find product</p>
     * <p>3. Add product to wishlist</p>
     * <p>Expected result:</p>
     * <p>Success message is displayed</p>
     *
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateCategory
     * @depends preconditionsCreateProductSimple
     *
     * @param array $customer
     * @param array $categoryData
     * @param string $simpleProductName
     *
     * @test
     */
    public function addProductToWishlistFromCatalog($customer, $categoryData, $simpleProductName)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontClearWishlist();
        //Steps
        $this->wishlistHelper()->frontAddProductToWishlistFromCatalogPage($simpleProductName, $categoryData['name']);
        //Verify
        $this->navigate('my_wishlist');
        $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($simpleProductName),
                          'Product ' . $simpleProductName . ' is not in the wishlist.');
        //Cleanup
    }

    /**
     * <p>Adds a simple product to Wishlist from Shopping Cart.</p>
     * <p>Steps:</p>
     * <p>1. Add the product to the shopping cart</p>
     * <p>2. Move the product to wishlist</p>
     * <p>3. Open the wishlist</p>
     * <p>Expected result:</p>
     * <p>The product is in the wishlist</p>
     *
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProductSimple
     *
     * @param array $customer
     * @param string $simpleProductName
     *
     * @test
     */
    public function addProductToWishlistFromShoppingCart($customer, $simpleProductName)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontClearWishlist();
        //Steps
        $this->productHelper()->frontOpenProduct($simpleProductName);
        $this->productHelper()->frontAddProductToCart();
        $this->shoppingCartHelper()->frontMoveToWishlist($simpleProductName);
        //Verify
        $this->navigate('my_wishlist');
        $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($simpleProductName),
                          'Product ' . $simpleProductName . ' is not in the wishlist.');
        //Cleanup
    }

    /**
     * <p>Opens My Wishlist using the link in quick access bar</p>
     * <p>Steps:</p>
     * <p>1. Open home page</p>
     * <p>2. Click "My Wishlist" link</p>
     * <p>Expected result:</p>
     * <p>The wishlist is opened.</p>
     *
     * @depends preconditionsCreateCustomer
     *
     * @param array $customer
     *
     * @test
     */
    public function openMyWishlistViaQuickAccessLink($customer)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->navigate('home');
        //Steps
        $this->clickControl('link', 'my_wishlist');
        //Verify
        $this->assertTrue($this->checkCurrentPage('my_wishlist'), $this->getParsedMessages());
        //Cleanup
    }

    /**
     * <p>Shares My Wishlist</p>
     * <p>Steps:</p>
     * <p>1. Add a product to the wishlist</p>
     * <p>2. Open My Wishlist</p>
     * <p>3. Click "Share Wishlist" button</p>
     * <p>4. Enter a valid email and a message</p>
     * <p>5. Click "Share Wishlist" button
     * <p>Expected result:</p>
     * <p>The success message is displayed</p>
     *
     * @dataProvider shareWishlistDataProvider
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProductSimple
     *
     * @param array $shareData
     * @param array $customer
     * @param string $simpleProductName
     *
     * @test
     */
    public function shareWishlist($shareData, $customer, $simpleProductName)
    {
        //Setup
        $shareData = $this->loadData('share_data', $shareData);
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($simpleProductName);
        $this->assertMessagePresent('success', 'successfully_added_product');
        //Steps
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontShareWishlist($shareData);
        //Verify
        $this->assertMessagePresent('success', 'successfully_shared_wishlist');
        //Cleanup
    }

    public function shareWishlistDataProvider()
    {
        return array(
            array(array('emails'  => 'autotest@test.com',
                        'message' => 'autotest message')),
            array(array('message' => '')),
        );
    }

    /**
     * <p>Shares My Wishlist with invalid email(s) provided</p>
     * <p>Steps:</p>
     * <p>1. Add a product to the wishlist</p>
     * <p>2. Open My Wishlist</p>
     * <p>3. Click "Share Wishlist" button</p>
     * <p>4. Enter an invalid email and a message</p>
     * <p>5. Click "Share Wishlist" button
     * <p>Expected result:</p>
     * <p>An error message is displayed</p>
     *
     * @dataProvider withInvalidEmailDataProvider
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProductSimple
     *
     * @param string $emails
     * @param string $errorMessage
     * @param array $customer
     * @param string $simpleProductName
     *
     * @test
     */
    public function withInvalidEmail($emails, $errorMessage, $customer, $simpleProductName)
    {
        //Setup
        $shareData = $this->loadData('share_data', array('emails' => $emails));
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($simpleProductName);
        $this->assertMessagePresent('success', 'successfully_added_product');
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontShareWishlist($shareData);
        //Verify
        if ($errorMessage == 'invalid_emails') {
            $this->assertMessagePresent('validation', $errorMessage);
        } else {
            $this->assertMessagePresent('error', $errorMessage);
        }
        //Cleanup
    }

    public function withInvalidEmailDataProvider()
    {
        return array(
            array('email@@example.com', 'invalid_emails_js'),
            array('.email@example.com', 'invalid_emails'),
        );
    }

    /**
     * <p>Shares My Wishlist with empty email provided</p>
     * <p>Steps:</p>
     * <p>1. Add a product to the wishlist</p>
     * <p>2. Open My Wishlist</p>
     * <p>3. Click "Share Wishlist" button</p>
     * <p>4. Enter an invalid email and a message</p>
     * <p>5. Click "Share Wishlist" button
     * <p>Expected result:</p>
     * <p>An error message is displayed</p>
     *
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProductSimple
     *
     * @param array $customer
     * @param string $simpleProductName
     *
     * @test
     */
    public function shareWishlistWithEmptyEmail($customer, $simpleProductName)
    {
        //Setup
        $shareData = $this->loadData('share_data', array('emails' => ''));
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($simpleProductName);
        $this->assertMessagePresent('success', 'successfully_added_product');
        //Steps
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontShareWishlist($shareData);
        //Verify
        $this->assertMessagePresent('validation', 'required_emails');
        //Cleanup
    }

    /**
     * <p>Verifies that a guest cannot open My Wishlist.</p>
     * <p>Steps:</p>
     * <p>1. Logout customer</p>
     * <p>2. Navigate to My Wishlist</p>
     * <p>Expected result:</p>
     * <p>Guest is redirected to login/register page.</p>
     *
     * @test
     */
    public function guestCannotOpenWishlist()
    {
        //Setup
        $this->logoutCustomer();
        //Steps
        $this->clickControl('link', 'my_wishlist');
        //Verify
        $this->assertTrue($this->checkCurrentPage('customer_login'), $this->getParsedMessages());
        //Cleanup
        $this->navigate('home'); // So that user is not redirected in further tests.
    }

    /**
     * <p>Verifies that a guest cannot add a product to a wishlist.</p>
     * <p>Steps:</p>
     * <p>1. Logout customer</p>
     * <p>2. Open a product</p>
     * <p>3. Add products to the wishlist</p>
     * <p>Expected result:</p>
     * <p>Guest is redirected to login/register page.</p>
     *
     * @depends preconditionsCreateProductSimple
     *
     * @param string $simpleProductName
     *
     * @test
     */
    public function guestCannotAddProductToWishlist($simpleProductName)
    {
        //Setup
        $this->logoutCustomer();
        //Steps
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($simpleProductName);
        //Verify
        $this->assertTrue($this->checkCurrentPage('customer_login'), $this->getParsedMessages());
        //Cleanup
        $this->navigate('home'); // So that user is not redirected in further tests.
    }
}
