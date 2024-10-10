<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Mink\Exception\ExpectationException;
use Page\ProductPage;
use Page\CartPage;
use Page\CheckoutPage;
use Page\HomePage;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;


class FeatureContext extends RawMinkContext implements Context
{
    private $productPage;
    private $cartPage;
    private $checkoutPage;
    private $homePage;

    public function __construct()
    {

    }

    /**
     * Initializes page objects before each scenario.
     *
     * @BeforeScenario
     */
    public function initializePageObjects(BeforeScenarioScope $scope): void
    {
        $session = $this->getSession('browserstack');
        $this->productPage = new ProductPage($session);
        $this->cartPage = new CartPage($session);
        $this->checkoutPage = new CheckoutPage($session);
        $this->homePage = new HomePage($session);
    }

    /**
     * @Given I have selected the product :productName
     */
    public function iHaveSelectedTheProduct($productName): void
    {
        $this->visitPath("/products/$productName");
        $this->productPage->waitForPageLoad();
    }

    /**
     * @When I set the quantity to :quantity
     */
    public function iSetTheQuantityTo($quantity): void
    {
        $this->productPage->setQuantity($quantity);
    }

    /**
     * @When I select :option
     */
    public function iSelect($option): void
    {
        $this->productPage->selectPurchaseOption($option);
    }

    /**
     * @When I click :button
     */
    public function iClick($button): void
    {
        $this->productPage->clickButton($button);
    }

    /**
     * @Then the product should be added to the cart
     * @throws ExpectationException
     */
    public function theProductShouldBeAddedToTheCart(): void
    {
        $cartItems = $this->cartPage->getCartItems();
        if (empty($cartItems)) {
            throw new ExpectationException("Cart is empty. Product was not added.", $this->getSession());
        }
    }

    /**
     * @When I navigate to the shopping cart page
     */
    public function iNavigateToTheShoppingCartPage(): void
    {
        $this->visitPath("/cart");
        $this->cartPage->waitForPageLoad();
    }

    /**
     * @Then the shopping cart page should display the correct items
     * @throws ExpectationException
     */
    public function theShoppingCartPageShouldDisplayTheCorrectItems()
    {
        $cartItems = $this->cartPage->getCartItems();
        if (count($cartItems) === 0) {
            throw new ExpectationException("No items found in the cart.", $this->getSession());
        }
    }

    /**
     * @Then the cart details should show the product, quantity, unit price, total, items total, shipping, and order total
     * @throws ExpectationException
     */
    public function theCartDetailsShouldShowTheDetails(): void
    {
        $details = $this->cartPage->getCartDetails();

        $requiredKeys = ['product', 'quantity', 'unit_price', 'total', 'items_total', 'shipping', 'order_total'];
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $details)) {
                throw new ExpectationException("Cart detail '$key' is missing.", $this->getSession());
            }
        }
    }

    /**
     * @When I proceed to checkout
     */
    public function iProceedToCheckout(): void
    {
        $this->cartPage->proceedToCheckout();
        $this->checkoutPage->waitForPageLoad();
    }

    /**
     * @Then the checkout page should be displayed
     * @throws ExpectationException
     */
    public function theCheckoutPageShouldBeDisplayed(): void
    {
        $currentPath = parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH);
        if ($currentPath !== '/checkout') {
            throw new ExpectationException("Checkout page is not displayed. Current path: $currentPath", $this->getSession());
        }
    }

    /**
     * @When I fill in the shipping information with :info
     */
    public function iFillInTheShippingInformationWith($info): void
    {
        $this->checkoutPage->fillShippingInformation($info);
    }

    /**
     * @Then the shipping information should be accepted
     * @throws ExpectationException
     */
    public function theShippingInformationShouldBeAccepted(): void
    {
        if (!$this->checkoutPage->isShippingInformationAccepted()) {
            throw new ExpectationException("Shipping information was not accepted.", $this->getSession());
        }
    }

    /**
     * @When I use the same address for billing
     */
    public function iUseTheSameAddressForBilling(): void
    {
        $this->checkoutPage->useShippingAsBilling();
    }

    /**
     * @Then the billing address should be set to the same as the shipping address
     * @throws ExpectationException
     */
    public function theBillingAddressShouldBeSetToTheSameAsTheShippingAddress(): void
    {
        if (!$this->checkoutPage->isBillingAddressSameAsShipping()) {
            throw new ExpectationException("Billing address is not the same as shipping address.", $this->getSession());
        }
    }

    /**
     * @When I select :method as the shipping method
     */
    public function iSelectAsTheShippingMethod($method): void
    {
        $this->checkoutPage->selectShippingMethod($method);
    }

    /**
     * @Then the shipping method and its cost should be displayed
     * @throws ExpectationException
     */
    public function theShippingMethodAndItsCostShouldBeDisplayed(): void
    {
        if (!$this->checkoutPage->isShippingMethodDisplayed()) {
            throw new ExpectationException("Shipping method is not displayed.", $this->getSession());
        }

        if (!$this->checkoutPage->isShippingCostDisplayed()) {
            throw new ExpectationException("Shipping cost is not displayed.", $this->getSession());
        }
    }

    /**
     * @When I enter valid card details with :cardInfo
     */
    public function iEnterValidCardDetailsWith($cardInfo): void
    {
        $this->checkoutPage->enterCardDetails($cardInfo);
    }

    /**
     * @Then the card details should be accepted
     * @throws ExpectationException
     */
    public function theCardDetailsShouldBeAccepted(): void
    {
        if (!$this->checkoutPage->areCardDetailsAccepted()) {
            throw new ExpectationException("Card details were not accepted.", $this->getSession());
        }
    }

    /**
     * @When I complete the purchase
     */
    public function iCompleteThePurchase(): void
    {
        $this->checkoutPage->completePurchase();
    }

    /**
     * @Then the order confirmation page should be displayed
     * @throws ExpectationException
     */
    public function theOrderConfirmationPageShouldBeDisplayed(): void
    {
        $currentPath = parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH);
        if (!str_contains($currentPath, '/order-confirmation')) {
            throw new ExpectationException("Order confirmation page is not displayed. Current path: $currentPath", $this->getSession());
        }
    }
}