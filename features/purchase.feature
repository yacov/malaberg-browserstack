Feature: Product Purchase
              In order to buy products
              As a customer
              I need to be able to add products to the cart and complete the checkout process

        Scenario: Successful purchase flow
            Given I have selected the product "aeons-ancient-roots-olive-oil"
             When I set the quantity to 3
              And I select "One-Time Purchase"
              And I click "Add To Bag"
             Then the product should be added to the cart
             When I navigate to the shopping cart page
             Then the shopping cart page should display the correct items
              And the cart details should show the product, quantity, unit price, total, items total, shipping, and order total
             When I proceed to checkout
             Then the checkout page should be displayed
             When I fill in the shipping information with "Alice Johnson, 789 Oak St, Manchester, M1 1AA, UNITED KINGDOM"
             Then the shipping information should be accepted
             When I use the same address for billing
             Then the billing address should be set to the same as the shipping address
             When I select "Domestic tracked" as the shipping method
             Then the shipping method and its cost should be displayed
             When I enter valid card details with "4242424242424242, Exp: 12/26, CVC: 123"
             Then the card details should be accepted
             When I complete the purchase
             Then the order confirmation page should be displayed

        @purchase
        Scenario: Add product to cart with subscription
            Given I am on the product page
             When I subscribe to the product
              And I add the product to the cart
             Then I should be on the cart page
              And I should see the message "Item has been added to cart"
              And the purchase type should be "Subscribe & Save"

        @faq
        Scenario: Verify FAQ accordion functionality
            Given I am on the FAQ section
             When I click on accordion button 1
             Then accordion section 1 should be expanded
              And only one accordion section should be expanded
             When I click on accordion button 2
             Then accordion section 2 should be expanded
              And accordion section 1 should be collapsed
              And only one accordion section should be expanded
             When I click on accordion button 2
             Then accordion section 2 should be collapsed

        @cart
        Scenario: Verify Cart Functionality with Coupon
            Given I am on the cart page
             Then the cart should be empty
             When I open the "product" page
              And I add the product to the cart
             Then I should be on the cart page
             When I apply a valid coupon code "AEONS15"
             Then the discount should be applied