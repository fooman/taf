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
 * Creating Order with specific shipment
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_Create_ShippingMethodsTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('store_information');
    }

    protected function assertPreConditions()
    {
        $this->addParameter('id', '0');
    }

    protected function tearDown()
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('shipping_disable');
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
     * <p>Creating order with different shipment methods</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select simple product and add it to the order;</p>
     * <p>4. Fill in all required information;</p>
     * <p>5. Choose shipping method;</p>
     * <p>6. Choose payment method;</p>
     * <p>6. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @param $shipment
     * @param $shippingOrigin
     * @param $shippingDestination
     * @param $simpleSku
     *
     * @depends createSimpleProduct
     * @dataProvider shipmentDataProvider
     * @param string $shipment
     * @param string $shippingOrigin
     * @param string $simpleSku
     * @test
     */
    public function differentShipmentMethods($shipment, $shippingOrigin, $shippingDestination, $simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustomer_checkmoney_flatrate_' . $shippingDestination,
                               array('filter_sku' => $simpleSku));
        $orderData['shipping_data'] = $this->loadData('shipping_' . $shipment);
        //Steps And Verifying
        $this->navigate('system_configuration');
        if($shippingOrigin) {
            $this->systemConfigurationHelper()->configure('shipping_settings_' . strtolower($shippingOrigin));
        }
        $this->systemConfigurationHelper()->configure($shipment . '_enable');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
        $this->clickButton('reorder');
        $this->orderHelper()->submitOreder();
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');
    }

    /**
     * <p>Data provider differentShipmentMethods test</p>
     *
     * @return array
     */
    public function shipmentDataProvider()
    {
        return array(
            array('flatrate', null, 'usa'),
            array('free', null, 'usa'),
            array('ups', 'usa', 'usa'),
            array('upsxml', 'usa', 'usa'),
            array('usps', 'usa', 'usa'),
            array('fedex', 'usa', 'usa'),
            array('dhl', 'usa', 'france'),
        );
    }
}