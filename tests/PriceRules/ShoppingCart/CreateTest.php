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
 * Creating rules with correct and incorrect data without applying them
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PriceRules_ShoppingCart_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Login Admin user to backend</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->addParameter('id', '0');
    }

    /**
     * <p>Create Shopping cart price rule with required fields only filled.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form (only required fields) for SCPR;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>Expected results:</p>
     * <p>Rule is created;</p>
     *
     * @return string   Returns coupon code
     *
     * @test
     */
    public function createWithRequiredFields()
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadData('scpr_required_fields', null, array('rule_name', 'coupon_code'));
        $this->priceRulesHelper()->createRule($ruleData);
        $this->assertMessagePresent('success', 'success_saved_rule');

        return $ruleData['info']['coupon_code'];
    }

    /**
     * <p>Create Shopping cart price rule with all fields filled (except conditions).</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form (all fields) for SCPR;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>Expected results:</p>
     * <p>Rule is created;</p>
     *
     * @test
     */
    public function createWithAllFields()
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadData('scpr_all_fields', null, array('rule_name', 'coupon_code'));
        $this->priceRulesHelper()->createRule($ruleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
    }

    /**
     * <p>Create Shopping cart price rule with all fields filled (except conditions).</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form (all fields) for SCPR;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>Expected results:</p>
     * <p>Rule is created;</p>
     *
     * @test
     */
    public function createWithoutCoupon()
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadData('scpr_all_fields',
                                    array('coupon'          => 'No Coupon',
                                          'coupon_code'     => '%noValue%',
                                          'uses_per_coupon' => '%noValue%'),
                                    array('rule_name'));
        $this->priceRulesHelper()->createRule($ruleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
    }

    /**
     * <p>Create Shopping cart price rule with existing coupon.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form (all fields) for SCPR;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>Expected results:</p>
     * <p>Rule is created;</p>
     *
     * <p>4. Create rule with the same coupon;</p>
     * <p>Expected Results:</p>
     * <p>Rule is not created; Messsage "Coupon with the same code already exists." appears.</p>
     *
     * @param string    Coupon Code
     * @depends createWithRequiredFields
     *
     * @test
     */
    public function createWithExistingCoupon($coupon)
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadData('scpr_all_fields', array('coupon_code' => $coupon), array('rule_name'));
        $this->priceRulesHelper()->createRule($ruleData);
        $this->assertMessagePresent('error', 'error_coupon_code_exists');
    }
}
