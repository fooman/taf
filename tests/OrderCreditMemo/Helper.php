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
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class OrderCreditMemo_Helper extends Mage_Selenium_TestCase
{

    /**
     * Provides partial or full refund
     *
     * @param array $refundData
     */
    public function createCreditMemoAndVerifyProductQty($refundButton, $creditMemoData = array())
    {
        $creditMemoData = $this->arrayEmptyClear($creditMemoData);
        $verify = array();
        $this->clickButton('credit_memo');
        foreach ($creditMemoData as $product => $options) {
            if (is_array($options)) {
                $sku = (isset($options['return_filter_sku'])) ? $options['return_filter_sku'] : NULL;
                $productQty = (isset($options['qty_to_refund'])) ? $options['qty_to_refund'] : '%noValue%';
                if ($sku) {
                    $verify[$sku] = $productQty;
                    $this->addParameter('sku', $sku);
                    $this->fillForm($options);
                }
            }
        }
        if (!$verify) {
            $setXpath = $this->_getControlXpath('fieldset', 'product_line_to_refund');
            $skuXpath = $this->_getControlXpath('field', 'product_sku');
            $qtyXpath = $this->_getControlXpath('field', 'product_qty');
            $productCount = $this->getXpathCount($setXpath);
            for ($i = 1; $i <= $productCount; $i++) {
                $prod_sku = $this->getText($setXpath . "[$i]" . $skuXpath);
                $prod_sku = trim(preg_replace('/SKU:|\\n/', '', $prod_sku));
                if ($this->isElementPresent($qtyXpath . "/input")) {
                    $prod_qty = $this->getAttribute($setXpath . "[$i]" . $qtyXpath . '/input/@value');
                } else {
                    $prod_qty = $this->getText($setXpath . "[$i]" . $qtyXpath);
                }
                $verify[$prod_sku] = $prod_qty;
            }
        }
        $buttonXpath = $this->_getControlXpath('button', 'update_qty');
        if ($this->isElementPresent($buttonXpath . "[not(@disabled)]")) {
            $this->click($buttonXpath);
            $this->pleaseWait();
        }
        $this->clickButton($refundButton);
        $this->assertMessagePresent('success', 'success_creating_creditmemo');
        foreach ($verify as $productSku => $qty) {
            if ($qty == '%noValue%') {
                continue;
            }
            $this->addParameter('sku', $productSku);
            $this->addParameter('refundedQty', $qty);
            $xpathShiped = $this->_getControlXpath('field', 'qty_refunded');
            $this->assertTrue($this->isElementPresent($xpathShiped),
                    'Qty of refunded products is incorrect at the orders form');
        }
    }

}
