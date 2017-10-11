Feature: Adding documents through media library

  Scenario: An agency editor can't add an unsupported file to the media library
    Given I log into the intranet as "agency_editor"

    # Upload a document through the media library
    When I open the new page editor
    And I click the "#insert-media-button" element
    And I click the "Insert Media" link
    And I click the "Upload Files" link
    Then I should see "Drop files anywhere to upload"

    # Check it does not allow to upload unsupported files
    And I attach the file "smoketest.pdf" into the media library
    Then I should see "Sorry, this file type is not permitted for security reasons."
