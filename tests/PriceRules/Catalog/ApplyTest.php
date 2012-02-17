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
 * Catalog Price Rules applying in frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PriceRules_Catalog_ApplyTest extends Mage_Selenium_TestCase
{
    protected $_ruleToBeDeleted = array();

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('default_tax_config');
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Promotions -> Catalog Price Rules</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDown()
    {
        if ($this->_ruleToBeDeleted) {
            $this->loginAdminUser();
            $this->navigate('manage_catalog_price_rules');
            $this->priceRulesHelper()->deleteRule($this->_ruleToBeDeleted);
            $this->_ruleToBeDeleted = array();
        }
    }

    /**
     * <p>Preconditions</p>
     * <p>Create Customer for tests</p>
     * <p>Creates Category to use during tests</p>
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadData('generic_customer_account');
        $categoryData = $this->loadData('sub_category_required');
        $simple = $this->loadData('simple_product_for_price_rules_validation_front',
                                  array('categories'       => $categoryData['parent_category'] . '/' . $categoryData['name'],
                                       'prices_tax_class'  => 'Taxable Goods'));
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($categoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        return array(
            'customer'     => array('email'    => $userData['email'],
                                    'password' => $userData['password']),
            'categoryPath' => $categoryData['parent_category'] . '/' . $categoryData['name'],
            'categoryName' => $categoryData['name'],
            'simpleName'   => $simple['general_name']
        );
    }
}
