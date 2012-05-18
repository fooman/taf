Feature: Login

    Scenario: Check login and execute test from TAF
        Given I logged into magento backend
         When I navigate to "manage_admin_users" page
          And I execute from test suite "Core_Mage_AdminUser_CreateTest" test "withRequiredFieldsOnly"

    Scenario: Check login and execute test described outside TAF, but using Helpers from TAF (positive test)
        Given I logged into magento backend
         When I navigate to "manage_admin_users" page
          And I create new user with role "Administrators" from profile "generic_admin_user" I want to see "success" type message "success_saved_user"

    Scenario: Check login and execute test described outside TAF, but using Helpers from TAF (negative test)
        Given I logged into magento backend
         When I navigate to "manage_admin_users" page
          And I create new user with role "Administrators" from profile "generic_admin_user" I want to see "error" type message "exist_name_or_email"