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
 * @package     testlink unit tests
 * @subpackage  Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Testlink_AnnotationMock extends Mage_Testlink_Annotation
{

    /**
     * Just overrides the parent constructor
     */
    public function __construct()
    {
    }

    /**
     * Calls all parent methods, even protected
     *
     * @param type $name
     * @param type $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $class = new ReflectionClass('Mage_Testlink_Annotation');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($this, $arguments);
    }
}