Feature: Google Search
              In order to learn more
              As a user
              I need to be able to search for information

        Scenario: Searching for BrowserStack
            Given I am on "https://www.google.com"
             When I search for "BrowserStack"
             Then I should see "BrowserStack" in the title
