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
 * @category   Varien
 * @package    Varien_Io
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Input/output client interface
 *
 * @category   Varien
 * @package    Varien_Io
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Varien_Io_Interface
{
    /**
     * Open a connection
     *
     * @param array $args
     *
     * @return
     */
    public function open(array $args = array());

    /**
     * Close a connection
     *
     */
    public function close();

    /**
     * Create a directory
     *
     * @param $dir
     * @param int $mode
     * @param bool $recursive
     *
     * @return
     */
    public function mkdir($dir, $mode = 0777, $recursive = true);

    /**
     * Delete a directory
     *
     * @param $dir
     * @param bool $recursive
     *
     * @return
     */
    public function rmdir($dir, $recursive = false);

    /**
     * Get current working directory
     *
     */
    public function pwd();

    /**
     * Change current working directory
     *
     * @param $dir
     *
     * @return
     */
    public function cd($dir);

    /**
     * Read a file
     *
     * @param $filename
     * @param null $dest
     *
     * @return
     */
    public function read($filename, $dest = null);

    /**
     * Write a file
     *
     * @param $filename
     * @param $src
     * @param null $mode
     *
     * @return
     */
    public function write($filename, $src, $mode = null);

    /**
     * Delete a file
     *
     * @param $filename
     *
     * @return
     */
    public function rm($filename);

    /**
     * Rename or move a directory or a file
     *
     * @param $src
     * @param $dest
     *
     * @return
     */
    public function mv($src, $dest);

    /**
     * Chamge mode of a directory or a file
     *
     * @param $filename
     * @param $mode
     *
     * @return
     */
    public function chmod($filename, $mode);

    /**
     * Get list of cwd subdirectories and files
     *
     * @param null $grep
     *
     * @return
     */
    public function ls($grep = null);

    /**
     * Retrieve directory separator in context of io resource
     *
     */
    public function dirsep();
}
