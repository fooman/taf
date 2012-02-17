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
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Implementation of the Selenium RC client/server protocol.
 * Extension: logging of all client/server protocol transactions to the 'selenium-rc-DATE.log' file.
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Driver extends PHPUnit_Extensions_SeleniumTestCase_Driver
{
    /**
     * If the flag is set to True, browser connection is not restarted after each test
     * @var boolean
     */
    protected $_contiguousSession = false;

    /**
     * Handle to log file
     * @var Resource
     */
    protected $_logHandle = null;

    /**
     * Current browser name
     * @var string
     */
    protected $_browser = null;

    /**
     * Basic constructor of Selenium RC driver
     * Extension: initialization of log file handle.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_logHandle = fopen('tmp' . DIRECTORY_SEPARATOR . 'selenium-rc-' . date('d-m-Y-H-i-s') . '.log', 'a+');
    }

    /**
     * Sets the flag to restart browser connection or not after each test
     *
     * @param boolean $flag Flag to restart browser after each test or not (TRUE - do not restart, FALSE - restart)
     * @return Mage_Selenium_Driver
     */
    public function setContiguousSession($flag)
    {
        $this->_contiguousSession = $flag;
        return $this;
    }

    /**
     * Starts browser connection
     * @return string
     */
    public function start()
    {
        return parent::start();
    }

    /**
     * Stops browser connection if the session is not marked as contiguous
     */
    public function stop()
    {
        if ($this->_contiguousSession) {
            return;
        }
        parent::stop();
    }

    /**
     * Sends a command to the Selenium RC server.
     * Extension: transaction logging to opened file stream in view: TIME,REQUEST,RESPONSE or TIME,EXCEPTION
     *
     * @param  string $command Command for send to Selenium RC server
     * @param  array $arguments Array of arguments to command
     *
     * @throws PHPUnit_Framework_Exception
     *
     * @return string
     */
    protected function doCommand($command, array $arguments = array())
    {
        // Add command logging
        try {
            $response = parent::doCommand($command, $arguments);
            //Fixed bug for new PHPUnit_Selenium 1.2
            if (!preg_match('/^OK/', $response)) {
                $this->stop();
                throw new PHPUnit_Framework_Exception(
                    sprintf("Response from Selenium RC server for %s.\n%s.\n", $command, $response));
            }
            if (!empty($this->_logHandle)) {
                fputs($this->_logHandle, self::udate('H:i:s.u') . "\n");
                fputs($this->_logHandle, "\tRequest: " . $command . "\n");
                fputs($this->_logHandle, "\tResponse: " . $response . "\n\n");
                fflush($this->_logHandle);
            }
        } catch (PHPUnit_Framework_Exception $e) {
            if (!empty($this->_logHandle)) {
                fputs($this->_logHandle, self::udate('H:i:s.u') . "\n");
                fputs($this->_logHandle, "\tException: " . $e->getMessage() . "\n");
                fflush($this->_logHandle);
            }
            throw $e;
        }

        return $response;
    }

    /**
     * Saves browser name to be accessible from driver
     *
     * @param string
     */
    public function setBrowser($browser)
    {
        $this->_browser = $browser;
        parent::setBrowser($browser);
    }

    /**
     * Return browser name
     * @return string
     */
    public function getBrowser()
    {
        return $this->_browser;
    }

    /**
     * Performs to return time to logging (e.g. 15:18:43.244768)
     *
     * @param  string $format A composite format string
     * @param  mixed  $uTimeStamp Timestamp (by default = null)
     * @return string A formatted date string.
     */
    public static function udate($format, $uTimeStamp = null)
    {
        if (is_null($uTimeStamp)) {
            $uTimeStamp = microtime(true);
        }

        $timestamp = floor($uTimeStamp);
        $milliseconds = round(($uTimeStamp - $timestamp) * 1000000);

        return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
    }
}
