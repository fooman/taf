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
 * Extension: logging of all client/server protocol tranactions to the 'selenium-rc-DATE.log' file.
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Driver extends PHPUnit_Extensions_SeleniumTestCase_Driver
{

    /**
     * If the flag is set true browser connection is not restarted after each test
     * @var boolean
     */
    protected $_contiguousSession = false;

    /**
     * Handle to log file
     * @var Resource
     */
    protected $_logHandle = null;

    /**
     * Basic constructor of Selenium RC driver
     * Extension: initialization of log file handle.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_logHandle = fopen('tmp'.DIRECTORY_SEPARATOR.'selenium-rc-'.date('d-m-Y-H-i-s').'.log', 'at');
    }

    /**
     * Sets the flag to RESTART/DON'T RESTART of a browser connection after each
     * test (TRUE - do not restart, FALSE - do restart)
     *
     * @param boolean $flag Flag of restarting browser after each test (TRUE - do not restart, FALSE - do restart)
     *
     * @return Mage_Selenium_Driver
     */
    public function setContiguousSession($flag)
    {
        $this->_contiguousSession = $flag;
        return $this;
    }

    /**
     * Starts browser connection
     *
     * @return string
     */
    public function start()
    {
        return parent::start();
    }

    /**
     * Stops browser connection
     * Extension: checking of $_contiguousSession flag (RESTART/NOT RESTART of browser)
     */
    public function stop()
    {
        if (!isset($this->sessionId)) {
            return;
        }
        if ($this->_contiguousSession) {
            return;
        }
        $this->doCommand('testComplete');
        $this->sessionId = NULL;
    }

    /**
     * Sends a command to the Selenium RC server.
     * Extension: transaction logging to opened file stream in view: TIME,REQUEST,RESPONSE or TIME,EXCEPTION
     *
     * @param  string $command Command for send to Selenium RC server
     * @param  array $arguments Array of arguments to command
     * @return string
     */
    protected function doCommand($command, array $arguments = array())
    {
        // Add command logging
        try {
            $response = parent::doCommand($command, $arguments);
            if (!empty($this->_logHandle)) {
                fputs($this->_logHandle, self::udate('H:i:s.u') . "\n");
                fputs($this->_logHandle, "\tRequest: " . end($this->commands) . "\n");
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
     * Performs to return time to logging (e.g. 15:18:43.244768)
     *
     * @param  string $format A composite format string
     * @param  mixed  $utimestamp Timestamp (by default = null)
     * @return string String a formatted date string.
     */
    public static function udate($format, $utimestamp = null)
    {
        if (is_null($utimestamp))
            $utimestamp = microtime(true);

        $timestamp = floor($utimestamp);
        $milliseconds = round(($utimestamp - $timestamp) * 1000000);

        return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
    }

}
