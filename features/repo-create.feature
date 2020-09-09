Feature: I want to create a new repository
    Scenario: I need a repository
        Given I am an authenticated user
        When I create "new" repository
        And I request a list of my repositories
        Then The result should include a repository called "new"
