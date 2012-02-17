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
 * Customer Tax Class creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tax_CustomerTaxClass_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Login to backend</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Sales-Tax-Customer Tax Classes</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_customer_tax_class');
    }

    /**
     * <p>Creating Customer Tax Class with required field</p>
     * <p>Steps</p>
     * <p>1. Click "Add New" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Click "Save Class" button</p>
     * <p>Expected Result:</p>
     * <p>Customer Tax Class created, success message appears</p>
     *
     * @return array $customerTaxClassData
     * @test
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $customerTaxClassData = $this->loadData('new_customer_tax_class');
        //Steps
        $this->taxHelper()->createTaxItem($customerTaxClassData, 'customer_class');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_class');
        //Steps
        $this->taxHelper()->openTaxItem($customerTaxClassData, 'customer_class');
        //Verifying
        $this->assertTrue($this->verifyForm($customerTaxClassData), $this->getParsedMessages());

        return $customerTaxClassData;
    }

    /**
     * <p>Creating Customer Tax Class with name that exists</p>
     * <p>Steps</p>
     * <p>1. Click "Add New" button </p>
     * <p>2. Fill in Class Name with name that exists</p>
     * <p>3. Click "Save Class" button</p>
     * <p>Expected Result:</p>
     * <p>Customer Tax Class should not be created, error message appears</p>
     *
     * @depends withRequiredFieldsOnly
     * @param array $customerTaxClassData
     * @test
     */
    public function withNameThatAlreadyExists($customerTaxClassData)
    {
        //Steps
        $this->taxHelper()->createTaxItem($customerTaxClassData, 'customer_class');
        //Verifying
        $this->assertMessagePresent('error', 'tax_class_exists');
    }

    /**
     * <p>Creating Customer Tax Class with empty name</p>
     * <p>Steps</p>
     * <p>1. Click "Add New" button </p>
     * <p>2. Leave Class Name empty</p>
     * <p>3. Click "Save Class" button</p>
     * <p>Expected Result:</p>
     * <p>Customer Tax Class should not be created, error message appears</p>
     *
     * @depends withRequiredFieldsOnly
     * @test
     */
    public function withEmptyName()
    {
        //Data
        $customerTaxClassData = $this->loadData('new_customer_tax_class', array('customer_class_name' => ''));
        //Steps
        $this->taxHelper()->createTaxItem($customerTaxClassData, 'customer_class');
        //Verifying
        $this->assertMessagePresent('error', 'empty_class_name');
    }

}
