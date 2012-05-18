<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;


/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param   array   $parameters     context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
    }

    /**
     * @Given /^I logged into magento backend$/
     */
    public function loginToBackend()
    {
        $testCase = new Mage_Selenium_TestCase();
        $testCase->prepareBrowserSession();
        $testCase->loginAdminUser();
    }

    /**
     * @When /^I execute from test suite "([^"]*)" test "([^"]*)"$/
     *
     * @param $testSuiteName
     * @param $testName
     */
    public function executeTest($testSuiteName, $testName = null)
    {
        $test = new $testSuiteName();
        $test->prepareBrowserSession();
//        $test->loginAdminUser();
        $test->$testName();
    }

    /**
     * @When /^I navigate to "([^"]*)" page$/
     *
     * @param $page
     */
    public function navigateToPage($page)
    {
        $testCase = new Mage_Selenium_TestCase();
        $testCase->prepareBrowserSession();
//        $testCase->loginAdminUser();
        $testCase->navigate($page);
    }

    /**
     * @When /^I create new user with role "([^"]*)" from profile "([^"]*)" I want to see "([^"]*)" type message "([^"]*)"$/
     *
     * @param $role
     * @param $profile
     * @param $type
     * @param $message
     */
    public function createUser($role, $profile, $type, $message)
    {
        $helper = new Core_Mage_AdminUser_Helper();
        $helper->prepareBrowserSession();
        $helper->addParameter('id', '0');
        //Data
        $userData = $helper->loadData($profile,
                array('this_acount_is' => 'Inactive', 'role_name' => $role), array('email', 'user_name'));
        //Steps
        $helper->createAdminUser($userData);
        //Verifying
        $helper->assertMessagePresent($type, $message);
    }

}
