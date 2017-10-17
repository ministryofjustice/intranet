@skip
Feature: Add document through the documents plugin

  Scenario: An agency editor is able to add a document through the documents plugin
    Given I log into the intranet as "agency_editor"

    # Upload a document through the documents plugin
    When I open the add document editor
    And I fill in the field with the selector "#title" with "Smoke test document"
    And I click the "Upload New Version" link
    And I switch to the uploader modal window
    Then I should see "Add media files from your computer"

    # Not using the drop area as it is not working as expected
    When I switch to the browser built-in uploader
    And I attach the file "smoketest.jpg"
    And I click the "Upload" button
    And I switch to the main window
    # Publish the document
    And I click the "Publish" button
    Then I should see "Document published."

    # Let's go check to see if the document can be downloaded
    When I click the "Download document" link
    Then A file with name "smoke-test-document.jpg" should download

    # Now get rid of the document
    When I click the "Move to Trash" link
    Then I should see "1 post moved to the Trash."
