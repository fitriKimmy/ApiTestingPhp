Feature: This is an importan repository
    Scenario: I want to  know when something happens with this repository
    Given I am an authenticated user
    And I have a repository called "new"
    When I watch the "new" repository
    Then The "new" repository will  list me as a watcher
    And I delete the repository called "new"