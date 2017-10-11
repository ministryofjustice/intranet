Feature: Test My Work component (HMCTS)

Scenario: Test My Work [HMCTS] Acceptance Criteria
  Given I visit "/"
  # Switch to HMTCS intranet
  When I click the "Switch to other intranet" link
  Then I click the "HM Courts & Tribunals Service" link
  # Make sure I am on HMCTS
  Then I should see "HM Courts & Tribunals Service"
  # Check that 'My Work' appears
  Then I should see "My Work"
  # Check the links are as expected
  When I am looking at the ".my-work-links-container" component, I should see a link which says "My work" and goes to "/about-hmcts/my-work/"
  When I am looking at the ".my-work-links-container" component, I should see a link which says "Crown Court" and goes to "/about-hmcts/my-work/crown-court/"
  When I am looking at the ".my-work-links-container" component, I should see a link which says "Civil" and goes to "http://hmcts.intranet.service.justice.gov.uk/hmcts/my-work/civil/index.htm"
  When I am looking at the ".my-work-links-container" component, I should see a link which says "Family" and goes to "/about-hmcts/my-work/family/"
  When I am looking at the ".my-work-links-container" component, I should see a link which says "Legal Services" and goes to "/about-hmcts/my-work/legal-services/"
  When I am looking at the ".my-work-links-container" component, I should see a link which says "Magistrates" and goes to "http://hmcts.intranet.service.justice.gov.uk/hmcts/my-work/magistrates/"
  When I am looking at the ".my-work-links-container" component, I should see a link which says "Tribunals" and goes to "/about-hmcts/my-work/tribunals/"
  When I am looking at the ".my-work-links-container" component, I should see a link which says "Enforcement" and goes to "/about-hmcts/my-work/enforcement/"
