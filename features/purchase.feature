Feature: Product Purchase
             #10 In order to buy products
             #10 As a customer
             #10 I need to be able to add products to the cart and complete the checkout process

        @smoke
        Scenario: Successful one-time purchase with normal card
            Given I am on the "aeons-total-harmony" product page
             When I select "One-Time Purchase"
              And I set the quantity to "3 Jars"
             When I add the product to the cart
             When I proceed to checkout
              And I fill in the shipping information with:
                  | Email     | alice.johnson@example.com |
                  | FirstName | Alice                     |
                  | LastName  | Johnson                   |
                  | Phone     | 1234567890                |
                  | Address   | 789 Oak St                |
                  | City      | Manchester                |
                  | Postcode  | M1 1AA                    |
                  | Country   | GB                        |
              And I use the same address for billing
             Then The shipping method "Domestic tracked" should be selected
              And I verify the shipping cost is "FREE"
             When I enter the payment details:
                  | Card number | 4242424242424242 |
                  | Expiration  | 12/26            |
                  | CVC         | 123              |
              And I complete the purchase
              And I wait for the order confirmation page to load
             Then I verify the order details are correct