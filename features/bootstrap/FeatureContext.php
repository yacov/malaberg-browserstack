<?php
namespace Features\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Features\Bootstrap\Page\HomePage;
use Features\Bootstrap\Page\ProductPage;
use Features\Bootstrap\Page\CartPage;
use Features\Bootstrap\Page\CheckoutPage;
use Features\Bootstrap\Page\ConfirmationPage;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Behat\Mink\Mink;
use Exception;


class FeatureContext extends RawMinkContext implements Context
{
    /**
     * @var
     */
    protected $homePage;
    protected $productPage;
    protected $cartPage;
    protected $checkoutPage;
    protected $confirmationPage;
    protected $sharedData;
    protected $mink;

    public function __construct()
    {
        $this->sharedData = [];
    }
#

      /**
     * @Then I verify the URL is :expectedUrl
     */
    public function iVerifyTheUrlIs($expectedUrl)
    {
        $actualUrl = $this->getSession()->getCurrentUrl();
        Assert::assertEquals($expectedUrl, $actualUrl, "Expected URL '$expectedUrl', but found '$actualUrl'");
    }

    /**
     * @Then I verify the page title is :expectedTitle
     */
    public function iVerifyThePageTitleIs($expectedTitle)
    {
        $actualTitle = $this->getSession()->getPage()->find('css', 'title')->getText();
        Assert::assertEquals($expectedTitle, $actualTitle, "Expected page title '$expectedTitle', but found '$actualTitle'");
    }

   /**
     * @Given /^I am on the "([^"]*)" product page$/
     */
    public function userIsOnProductPage($productName): void
    {
        $this->productPage->loadWithName($productName);
    } 

    /**
     * @Then /^I should see "([^"]*)"$/
     */
    public function iShouldSee($text)
    {
        $this->assertPageContainsText($text);
    }

    /**
     * @BeforeScenario
     */
    public function initializePageObjects(BeforeScenarioScope $scope)
    {
        $session = $this->getSession();
        $this->homePage = new HomePage($session);
        $this->productPage = new ProductPage($session);
        $this->cartPage = new CartPage($session);
        $this->checkoutPage = new CheckoutPage($session);
        $this->confirmationPage = new ConfirmationPage($session);
    }


    // Main Test Flow: Select Product
    // ----------------------------------

    /**
     * @Given /^I memorize the page title$/
     */
    public function iMemorizeThePageTitle(): void
    {
        $title = $this->homePage->getPageTitle();
        SharedDataContext::getInstance()->set('pageTitle', $title);
    }

    // Product Page Steps
    /**
     * @When I navigate to the product page
     */
    public function iNavigateToTheProductPage()
    {
        $this->productPage->open();
    }



    /**
     * @When /^I subscribe to product$/
     * @throws ElementNotFoundException
     */
    public function userSubscribesToProduct(): void
    {
        $this->productPage->clickToSubscribe();
    }

    /**
     * @When /^I set the quantity to "([^"]*)"$/
     * @throws ElementNotFoundException
     */
    public function userSetsTheQuantityTo($quantity): void
    {
        $this->productPage->selectSize($quantity);
        $this->sharedData['quantity'] = $quantity;
    }

    /**
     * @When /^I select "([^"]*)"$/
     * @throws ElementNotFoundException
     */
    public function userSelectsPurchaseType($purchaseType): void
    {
        $selectedPurchaseType = $this->productPage->selectPurchaseOption($purchaseType);
        $this->sharedData['purchaseType'] = $selectedPurchaseType;
    }

    /**
     * @When /^(?:I|user) click "Add to cart"$/
     * @throws ElementNotFoundException
     */
    public function userAddsTheProductToTheCart(): void
    {
        $this->productPage->addToCart();
    }

    /**
     * @When /^I add the product "([^"]*)" to the cart$/
     * @throws ElementNotFoundException
     */
    public function userAddsSpecificProductToTheCart($productName): void
    {
        $this->productPage->load($productName);
        $this->productPage->addToCart();
    }

    /**
     * @When /^I add default product to the cart$/
     * @throws ElementNotFoundException
     */
    public function userAddsDefaultProductToTheCart(): void
    {
        $this->productPage->loadDefaultProduct();
        $this->productPage->addToCart();
    }

    /**
     * @When /^I navigate to the "([^"]*)" product page$/
     */
    public function iNavigateToTheProductPageByName($productName): void
    {
        $this->productPage->navigateToProduct($productName);
    }

    /**
     * @Then /^Expected sum of products should be calculated correctly$/
     */
    public function expectedSumOfProductsShouldBeCalculatedCorrectly(): void
    {
        $this->iMemorizeTheProductDetails();
        $this->productPage->verifySumOfProducts();
    }

    /**
     * @When /^I memorize the product details$/
     */
    public function iMemorizeTheProductDetails(): void
    {
        $productName = $this->productPage->getProductName();
        $purchaseType = $this->productPage->getSelectedPurchaseOption();
        $quantity = $this->productPage->getQuantity();

        $sharedData = SharedDataContext::getInstance();
        $sharedData->setMultiple([
            'productName' => $productName,
            'purchaseType' => $purchaseType,
            'quantity' => $quantity,
        ]);
    }

    /**
     * @When /^I add the product to the cart$/
     */
    public function iAddTheProductToTheCart(): void
    {
        $this->productPage->addToCart();
    }

    /**
     * @Then /^the purchase type should be "([^"]*)"$/
     */
    public function thePurchaseTypeShouldBe(string $purchaseType): void
    {
        $actualPurchaseType = $this->productPage->getSelectedPurchaseOption();
        if ($actualPurchaseType !== $purchaseType) {
            throw new \Exception("Expected purchase type '$purchaseType', but found '$actualPurchaseType'");
        }
    }

    // Cart Operations
    // ----------------------------------

    /**
     * @When /^I (?:proceed to|navigate to) the shopping cart page$/
     */
    public function iProceedToTheShoppingCartPage(): void
    {
        $this->productPage->goToCart();
    }

    /**
     * @When /^I update the quantity to "([^"]*)"$/
     */
    public function iUpdateTheQuantityTo($quantity): void
    {
        $this->cartPage->updateQuantity($quantity);
        $this->cartPage->updateCart();
        $this->cartPage->waitForCartToUpdate();
    }

    /**
     * @Then /^Total price should be updated correctly$/
     * @throws Exception
     */
    public function totalPriceShouldBeUpdatedCorrectly(): void
    {
        $unitPrice = $this->cartPage->getUnitPrice();
        $quantity = $this->cartPage->getQuantity();
        $expectedTotal = $unitPrice * $quantity;
        $actualTotal = $this->cartPage->getTotalPrice();
        if ($actualTotal != $expectedTotal) {
            throw new Exception("Total price is $actualTotal, expected $expectedTotal");
        }
    }

    /**
     * @When /^I decrease the quantity to "([^"]*)"$/
     */
    public function iDecreaseTheQuantityTo($quantity): void
    {
        $this->cartPage->updateQuantity($quantity);
        $this->cartPage->updateCart();
        $this->cartPage->waitForCartToUpdate();
    }

    /**
     * @When /^I remove the item$/
     */
    public function iRemoveTheItem(): void
    {
        $this->cartPage->removeItem();
        $this->cartPage->waitForCartToUpdate();
    }

    /**
     * @Then /^The cart should be empty$/
     * @throws Exception
     */
    public function theCartShouldBeEmpty(): void
    {
        if (!$this->cartPage->isCartEmpty()) {
            throw new Exception("Cart is not empty or doesn't display the correct empty message");
        }
    }

    /**
     * @When /^I apply a valid coupon code "([^"]*)"$/
     */
    public function iApplyAValidCouponCode($couponCode): void
    {
        $this->cartPage->applyCoupon($couponCode);
        $this->cartPage->waitForCartToUpdate();
    }

    /**
     * @Then /^The discount should be applied$/
     * @throws Exception
     */
    public function theDiscountShouldBeApplied(): void
    {
        if (!$this->cartPage->isDiscountApplied()) {
            throw new Exception("Discount was not applied correctly");
        }
    }

    /**
     * @Then /^The order total should be calculated correctly$/
     * @throws Exception
     */
    public function theOrderTotalShouldBeCalculatedCorrectly(): void
    {
        if (!$this->cartPage->verifyOrderTotal()) {
            throw new Exception("Order total is not calculated correctly");
        }
    }

    /**
     * @When /^I apply an invalid coupon code "([^"]*)"$/
     */
    public function iApplyAnInvalidCouponCode($couponCode): void
    {
        $this->cartPage->applyCoupon($couponCode);
        $this->cartPage->waitForCartToUpdate();
    }

    /**
     * @Then /^an error message should be displayed$/
     * @throws Exception
     */
    public function anErrorMessageShouldBeDisplayed(): void
    {
        if (!$this->cartPage->isErrorMessageDisplayed()) {
            throw new Exception("Error message was not displayed");
        }
    }

    /**
     * @When /^I try to proceed to checkout with an empty cart$/
     */
    public function iTryToProceedToCheckoutWithAnEmptyCart(): void
    {
        $this->cartPage->proceedToCheckout();
    }

    /**
     * @Then /^I should be prevented from proceeding$/
     * @throws Exception
     */
    public function iShouldBePreventedFromProceeding(): void
    {
        if (!$this->cartPage->isPreventedFromCheckout()) {
            throw new Exception("User was not prevented from proceeding with an empty cart");
        }
    }

    /**
     * @Then /^I verify the cart contains the correct product details$/
     */
    public function iVerifyTheCartContainsTheCorrectProductDetails(): void
    {
        $sharedData = SharedDataContext::getInstance();
        $expectedData = $sharedData->getMultiple(['productName', 'purchaseType', 'quantity']);
        $this->cartPage->verifyCartContents($expectedData);
    }

    // Checkout Operations
    // ----------------------------------

    /**
     * @When /^I proceed to checkout$/
     * @throws ElementNotFoundException
     */
    public function iProceedToCheckout(): void
    {
        $this->cartPage->proceedToCheckout();
    }

    /**
     * @Then /^The checkout page is displayed$/
     */
    public function theCheckoutPageIsDisplayed(): void
    {
        if (!$this->checkoutPage->isCheckoutPageDisplayed()) {
            throw new \Exception("Checkout page is not displayed.");
        }
    }

    /**
     * @When /^I fill in the shipping information with:$/
     */
    public function iFillInTheShippingInformationWith(TableNode $table): void
    {
        try {
            $this->checkoutPage->waitForCheckoutForm();
            $shippingInfo = $table->getRowsHash();
            $this->checkoutPage->fillInCheckoutForm($shippingInfo);
            SharedDataContext::getInstance()->set('shippingInfo', $shippingInfo);
        } catch (\Exception $e) {
            throw new \Exception("Error filling in shipping information: " . $e->getMessage());
        }
    }

    /**
     * @When /^I (use|choose|select) the same address for billing$/
     */
    public function iChooseToUseTheSameAddressForBilling(): void
    {
        $this->checkoutPage->useSameAddressForBillingAndShipping();
    }

    /**
     * @When /^I select "([^"]*)" as the shipping method$/
     */
    public function iSelectAsTheShippingMethod($method): void
    {
        $this->checkoutPage->selectShippingMethod($method);
        SharedDataContext::getInstance()->set('shippingMethod', $method);
    }

    /**
     * @Then /^The shipping method "([^"]*)" should be selected$/
     */
    public function theShippingMethodShouldBeSelected($method): void
    {
        if (!$this->checkoutPage->isShippingMethodSelected($method)) {
            throw new \Exception("The shipping method '$method' is not selected.");
        }
    }

    /**
     * @Then /^The shipping cost is displayed$/
     */
    public function theShippingCostIsDisplayed(): void
    {
        if (!$this->checkoutPage->isShippingCostDisplayed()) {
            throw new \Exception("Shipping cost is not displayed.");
        }
    }

    /**
     * @When /^I provide payment details:$/
     */
    public function iProvidePaymentDetails(TableNode $table): void
    {
        $paymentDetails = $table->getRowsHash();
        $this->checkoutPage->enterPaymentDetails($paymentDetails);
    }

    /**
     * @When /^I complete the purchase$/
     */
    public function iCompleteThePurchase(): void
    {
        $this->checkoutPage->completePurchase();
    }

    /**
     * @Given /^I verify the product details and pricing are correct$/
     */
    public function iVerifyTheProductDetailsAndPricingAreCorrect(): void
    {
        $this->checkoutPage->verifyOrderTotal();
    }

    /**
     * @Given /^I verify the shipping cost is "([^"]*)"$/
     */
    public function iVerifyTheShippingCostIs($cost): void
    {
        $actualCost = $this->checkoutPage->getShippingCost();
        if ($actualCost !== $cost) {
            throw new \Exception(sprintf("Expected shipping cost to be %s, but got %s", $cost, $actualCost));
        }
    }

    /**
     * @Given /^I enter the payment details:$/
     */
    public function iEnterThePaymentDetails(TableNode $table): void
    {
        $paymentDetails = $table->getRowsHash();
        $this->checkoutPage->enterPaymentDetails($paymentDetails);
    }

    // Wait for Confirmation Screen
    // ----------------------------------

    /**
     * @Given /^I wait for the order confirmation page to load$/
     * @throws ElementNotFoundException
     */
    public function iWaitForTheOrderConfirmationPageToLoad()
    {
        $this->confirmationPage->waitForPageToLoad();
    }

    // Confirmation Page
    // ----------------------------------

    /**
     * @Then /^The order confirmation page is displayed$/
     */
    public function theOrderConfirmationPageIsDisplayed(): void
    {
        if (!$this->confirmationPage->isOrderConfirmationPageDisplayed()) {
            throw new \Exception("Order confirmation page is not displayed.");
        }
    }

    /**
     * @Then /^I see the order number$/
     */
    public function iSeeTheOrderNumber(): void
    {
        $orderNumber = $this->confirmationPage->getOrderNumber();
        if (empty($orderNumber)) {
            throw new \Exception("Order number is not displayed.");
        }
        SharedDataContext::getInstance()->set('orderNumber', $orderNumber);
    }

    /**
     * @Then /^I verify the order details are correct$/
     * @throws ElementNotFoundException
     */
    public function iVerifyTheOrderDetailsAreCorrect(): void
    {
        $sharedData = SharedDataContext::getInstance();
        $expectedData = $sharedData->getAll();
        $this->confirmationPage->verifyOrderDetails($expectedData);
    }

    /**
     * @Then /^I should see the order processing page$/
     */
    public function iShouldSeeTheOrderProcessingPage()
    {
        throw new \Behat\Behat\Tester\Exception\PendingException();
    }

    // Additional Shared Methods

    /**
     * @return mixed
     */
    public function getHomePage(): mixed
    {
        return $this->homePage;
    }

    protected function setSharedData($key, $value): void
    {
        $this->sharedData[$key] = $value;
    }

    protected function getSharedData($key)
    {
        return $this->sharedData[$key] ?? null;
    }

 

}
