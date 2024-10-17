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
             When I fill in the shipping information with:
                  | Name     | Alice Johnson  |
                  | Address  | 789 Oak St     |
                  | City     | Manchester     |
                  | Postcode | M1 1AA         |
                  | Country  | UNITED KINGDOM |
              And I use the same address for billing
              And I select "Domestic tracked" as the shipping method
              And I verify the shipping cost is "£X.XX"
              And I enter the payment details:
                  | Card number | 4242424242424242 |
                  | Expiration  | 12/26            |
                  | CVC         | 123              |
              And I complete the purchase
             Then I should see the order processing page
              And I wait for the order confirmation page to load
              And I verify the order details are correct