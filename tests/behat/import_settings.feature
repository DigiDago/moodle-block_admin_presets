@block @block_admin_presets
Feature: I can export and import site settings
  In order to save time
  As an admin
  I need to export and import settings presets

  Background:
    Given I log in as "admin"
    And I am on site homepage
    And I follow "Turn editing on"
    And I add the "Admin presets" block
    And I follow "Export settings"
    And I set the following fields to these values:
      | Name | My preset |
    And I press "Save changes"

  @javascript
  Scenario: Preset settings are applied
    Given I follow "Advanced features"
    And I set the field "Enable portfolios" to "1"
    And I set the field "Enable badges" to "0"
    And I press "Save changes"
    And I navigate to "Assignment settings" node in "Site administration > Plugins > Activity modules > Assignment"
    And I set the field "Feedback plugin" to "File feedback"
    And I press "Save changes"
    When I am on site homepage
    And I follow "Presets"
    And I click on "load" "link" in the "My preset" "table_row"
    And I press "Load selected settings"
    Then I should not see "All preset settings skipped, they are already loaded"
    And I should see "Settings applied"
    And I should see "Enable portfolios" in the ".admin_presets_applied" "css_element"
    And I should see "Enable badges" in the ".admin_presets_applied" "css_element"
    And I should see "Feedback plugin" in the ".admin_presets_applied" "css_element"
    And I should see "File feedback" in the ".admin_presets_applied" "css_element"
    And I should see "Enable outcomes" in the ".admin_presets_skipped" "css_element"
    And I should see "Show recent submissions" in the ".admin_presets_skipped" "css_element"
    And I follow "Advanced features"
    And the field "Enable portfolios" matches value "0"
    And the field "Enable badges" matches value "1"
    And I navigate to "Assignment settings" node in "Site administration > Plugins > Activity modules > Assignment"
    And the field "Feedback plugin" matches value "Feedback comments"

  @javascript
  Scenario: Settings don't change if you import what you just exported
    When I click on "load" "link" in the "My preset" "table_row"
    And I press "Load selected settings"
    Then I should see "All preset settings skipped, they are already loaded"
    And I should not see "Settings applied"
