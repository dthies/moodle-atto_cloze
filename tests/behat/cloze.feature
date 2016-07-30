@editor @editor_atto @atto @atto_cloze @_bug_phantomjs
Feature: Atto bold button
  To format text in Atto, I need to use the bold button.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname |
      | teacher  | Teacher   |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name                 | questiontext |
      | Test questions   | shortanswer | shortanswer question | Rabbit       |
    And I log in as "admin"
    And I navigate to "Atto toolbar settings" node in "Site administration > Plugins > Text editors > Atto HTML editor"
    And I set the field "Toolbar config" to "other = html, cloze"
    And I press "Save changes"
    And I follow "Site home"
    And I follow "Course 1"
    And I navigate to "Questions" node in "Course administration > Question bank"

@javascript
  Scenario: Insert the button into question text of existing question
    When I click on "Edit" "link" in the "shortanswer question" "table_row"
    And I expand all fieldsets
    And I set the field "Question text" to "Bunny"
    And I select the text in the "Question text" Atto editor
    And I click on "Cloze editor" "button"
    And I click on "SHORTANSWER" "radio" in the "Cloze editor" "dialogue"
    And I click on "Add" "button" in the "Cloze editor" "dialogue"
    And I set the field "Feedback" to "Funny"
    And I press "Insert" "button" in the "Cloze editor" "dialogue"
    Then I should see "{1:SHORTANSWER:~%100%Bunny}"

