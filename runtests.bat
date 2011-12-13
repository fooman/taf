@echo off
REM
REM Magento
REM
REM NOTICE OF LICENSE
REM
REM This source file is subject to the Open Software License (OSL 3.0)
REM that is bundled with this package in the file LICENSE.txt.
REM It is also available through the world-wide-web at this URL:
REM http://opensource.org/licenses/osl-3.0.php
REM If you did not receive a copy of the license and are unable to
REM obtain it through the world-wide-web, please send an email
REM to license@magentocommerce.com so we can send you a copy immediately.
REM
REM DISCLAIMER
REM
REM Do not edit or add to this file if you wish to upgrade Magento to newer
REM versions in the future. If you wish to customize Magento for your
REM needs please refer to http://www.magentocommerce.com for more information.
REM
REM @category    tests
REM @package     selenium
REM @subpackage  runner
REM @author      Magento Core Team <core@magentocommerce.com>
REM @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
REM @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
REM

if "%PHPBIN%" == "" set PHPBIN=php.exe
if not exist "%PHPBIN%" if "%PHP_PEAR_PHP_BIN%" neq "" set PHPBIN=%PHP_PEAR_PHP_BIN%
set BASEDIR=%~dp0
"%PHPBIN%" "%PHP_PEAR_BIN_DIR%\phpunit" --configuration "%BASEDIR%phpunit.xml" %*
