Feature: Search repository
    Scenario: I want to get a list of repository that reference Behat
        Given I am an anonymous user
        When I search for "behat"
        Then I expect 200 response code
        And I expect at least 1 result
