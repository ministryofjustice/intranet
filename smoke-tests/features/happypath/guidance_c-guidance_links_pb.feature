Feature: Test PB Guidance Page component
  Scenario Outline: Test PB Guidance Page component Acceptance Criteria
    # Navigate to the Wales regions page for HMCTS
    Given I visit "/guidance?agency=pb"
    # Make sure I am on the Guidance and Forms page
    Then I should see "Guidance and forms"
    # Check all the links display and all the links have the right target
    When I am looking at the ".guidance-categories-list" component, I should see a link which says "<text>" and goes to "<target>"


    Examples:
      | text                                      | target                                                |
      | Case management – working together        |  /guidance/case-management-working-together/          |
      | Complaints guidance                       |  /guidance/complaints-guidance/                       |
      | Guidance for the Public and Stakeholders  |  /guidance/guidance-for-the-public-and-stakeholders/  |
      | Information assurance                     |  /guidance/information-assurance/                     |
      | Operations one sheets                     |  /guidance/operations/                                |
      | Recruitment policies etc                  |  /guidance/recruitment-policies-etc/                  |
      | Buildings and facilities                  |  /guidance/buildings-and-facilities/                  |
      | Change management                         |  /guidance/change-management/                         |
      | Charities and volunteering                |  /guidance/charities-and-volunteering/                |
      | Communications                            |  /guidance/communications/                            |
      | Diversity & Inclusion                     |  /guidance/equality-and-diversity/                    |
      | Financial management                      |  /guidance/financial-management/                      |
      | Fire safety                               |  /guidance/fire-safety/                               |
      | Health and safety                         |  /guidance/fire-health-safety/                        |
      | HR                                        |  /guidance/hr/                                        |
      | Internal audit                            |  /guidance/internal-audit/                            |
      | IT and Digital                            |  /guidance/it-services/                               |
      | Knowledge and information                 |  /guidance/knowledge-information/                     |
      | Legal services                            |  /guidance/legal-services/                            |
      | Policy making                             |  /guidance/policy-making/                             |
      | Research and analysis                     |  /guidance/research-and-analysis/                     |
      | Risk management                           |  /guidance/risk-management/                           |
      | Security                                  |  /guidance/security/                                  |
      | Social media                              |  /guidance/social-media/                              |
      | Staff directories                         |  /guidance/staff-directories/                         |
      | Sustainable development                   |  /guidance/sustainable-development-2/                 |
      | Working with arm’s length bodies (ALBs)   |  /guidance/working-with-arms-length-bodies/           |
      | Statistics and surveys                    |  /guidance/statistics-and-survey-results/             |


