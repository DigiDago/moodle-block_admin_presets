@block @block_admin_presets
Feature: I can export and import site settings
  In order to save time
  As an admin
  I need to export and import settings presets

  @javascript
  Scenario: Load changes and revert them
    Given I log in as "admin"
    And I am on site homepage
    And I follow "Turn editing on"
    And I add the "Admin presets" block
    And I follow "Export settings"
    And I set the following fields to these values:
      | Name | My preset |
    And I press "Save changes"
    And I follow "Advanced features"
    And I set the field "Enable portfolios" to "1"
    And I set the field "Enable badges" to "0"
    And I press "Save changes"
    And I navigate to "Assignment settings" node in "Site administration > Plugins > Activity modules > Assignment"
    And I set the field "Feedback plugin" to "File feedback"
    And I press "Save changes"
    And I navigate to "Course overview" node in "Site administration > Plugins > Blocks"
    And I set the field "Default maximum courses" to "5"
    And I press "Save changes"
    And I am on site homepage
    And I follow "Presets"
    And I click on "load" "link" in the "My preset" "table_row"
    And I press "Load selected settings"
    And I am on site homepage
    When I follow "Presets"
    And I click on "revert" "link" in the "My preset" "table_row"
    And I follow "revert"
    Then I should see "Settings successfully restored"
    And I should see "Enable portfolios" in the ".admin_presets_applied" "css_element"
    And I should see "Enable badges" in the ".admin_presets_applied" "css_element"
    And I should see "Feedback plugin" in the ".admin_presets_applied" "css_element"
    And I should see "File feedback" in the ".admin_presets_applied" "css_element"
    And I follow "Advanced features"
    And the field "Enable portfolios" matches value "1"
    And the field "Enable badges" matches value "0"
    And I navigate to "Assignment settings" node in "Site administration > Plugins > Activity modules > Assignment"
    And the field "Feedback plugin" matches value "File feedback"
    And I navigate to "Course overview" node in "Site administration > Plugins > Blocks"
    And the field "Default maximum courses" matches value "5"

