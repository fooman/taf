#!/bin/sh
#
# Magento
#
# NOTICE OF LICENSE
#
# This source file is subject to the Open Software License (OSL 3.0)
# that is bundled with this package in the file LICENSE.txt.
# It is also available through the world-wide-web at this URL:
# http://opensource.org/licenses/osl-3.0.php
# If you did not receive a copy of the license and are unable to
# obtain it through the world-wide-web, please send an email
# to license@magentocommerce.com so we can send you a copy immediately.
#
# DISCLAIMER
#
# Do not edit or add to this file if you wish to upgrade Magento to newer
# versions in the future. If you wish to customize Magento for your
# needs please refer to http://www.magentocommerce.com for more information.
#
# @category    tests
# @package     selenium
# @subpackage  runner
# @author      Magento Core Team <core@magentocommerce.com>
# @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
# @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
#

OS=`uname -s`
if [ "$OS" = "Darwin" ]
then
    # We don't have `readlink -f` on OS X, so we roll our own, courtesy of:
    #   http://stackoverflow.com/questions/1055671/how-can-i-get-the-behavior-of-gnus-readlink-f-on-a-mac
    cd `dirname $0`
    TARGET_FILE=`basename $0`

    # Iterate down a (possible) chain of symlinks

    while [ -L "$TARGET_FILE" ]
    do
        TARGET_FILE=`readlink $TARGET_FILE`
        cd `dirname $TARGET_FILE`
        TARGET_FILE=`basename $TARGET_FILE`
    done

    # Compute the canonicalized name by finding the physical path
    # for the directory we're in and appending the target file.

    ABSPATH=`pwd -P`
else
    ABSPATH=`dirname $(readlink -f $0)`
fi
CONFIG=$ABSPATH/phpunit.xml

if [ -e $CONFIG ]
then
    phpunit --configuration $CONFIG
else
    echo "Error: The file $CONFIG doesn't exist."
    exit 1
fi
