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
 * Prices Validation on the Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tax_TaxAndPricesValidationBackendTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('shipping_settings_default');
        $this->systemConfigurationHelper()->configure('flat_rate_for_price_verification');
        $this->navigate('manage_tax_rule');
        $this->taxHelper()-> deleteRulesExceptSpecified(array('Retail Customer-Taxable Goods-Rate 1'));
    }

    protected function assertPreConditions()
    {
        $this->addParameter('id', '0');
    }

    /**
     * Create Customer for tests
     *
     * @return string $userData
     * @test
     * @group preConditions
     */
    public function createCustomer()
    {
        //Data
        $userData = $this->loadData('customer_account_for_prices_validation', null, 'email');
        $addressData = $this->loadData('customer_account_address_for_prices_validation');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData, $addressData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');

        return $userData['email'];
    }

    /**
     * Create Simple Products for tests
     *
     * @return array $products
     * @test
     * @group preConditions
     */
    public function createProducts()
    {
        $products = array();
        $this->navigate('manage_products');
        for ($i = 1; $i <= 3; $i++) {
            $simpleProductData = $this->loadData("simple_product_for_prices_validation_$i",
                null, array('general_name', 'general_sku'));
            $products['sku'][$i] = $simpleProductData['general_sku'];
            $products['name'][$i] = $simpleProductData['general_name'];
            $this->productHelper()->createProduct($simpleProductData);
            $this->assertMessagePresent('success', 'success_saved_product');
        }
        return $products;
    }

}
