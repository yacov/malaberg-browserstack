<?php

namespace Features\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Exception;
use Page\CheckoutPage;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the Checkout Page context.
 */
class CheckoutPageContext extends MinkContext implements Context
{
    /**
     * @var CheckoutPage
     */
    private CheckoutPage $checkoutPage;

    /**
     * @var SharedDataContext
     */
    private SharedDataContext $sharedData;

    /**
     * Initializes context.
     */
    public function __construct(SharedDataContext $sharedData)
    {
        $this->checkoutPage = new CheckoutPage($this->getSession());
        $this->sharedData = $sharedData;
    }

    /**
     * @Then user should be on the checkout page
     * @throws Exception
     */
    public function userShouldBeOnTheCheckoutPage(): void
    {
        if (!$this->checkoutPage->isUrlMatches()) {
            throw new Exception("User is not on the checkout page");
        }
    }

    /**
     * @When user fills out the checkout form
     */
    public function userFillsOutTheCheckoutForm(): void
    {
        $this->checkoutPage->fillInCheckoutForm(
            'test@example.com',
            'John',
            'Doe',
            '123456789',
            '123 Main St',
            'Testville',
            '12345',
            'US'
        );
    }

    /**
     * @Then the purchase should be successfully completed
     * @throws Exception
     */
    public function thePurchaseShouldBeSuccessfullyCompleted(): void
    {
        $pageContent = $this->getSession()->getPage()->getContent();
        if (!str_contains($pageContent, "Thank you for your purchase!")) {
            throw new Exception("Purchase was not successfully completed");
        }
    }

     /**
     * @Then I verify the URL is :expectedURL
     */
    public function iVerifyTheURLIs($expectedURL): void
    {
        $currentURL = $this->checkoutPage->getCurrentURL();
        Assert::assertStringContainsString($expectedURL, $currentURL);
    }

    /**
     * @Then I verify the page title is :expectedTitle
     */
    public function iVerifyThePageTitleIs($expectedTitle): void
    {
        $pageTitle = $this->checkoutPage->getPageTitle();
        Assert::assertEquals($expectedTitle, $pageTitle);
    }

    /**
     * @Then I verify the product details and pricing are correct
     */
    public function iVerifyTheProductDetailsAndPricingAreCorrect(): void
    {
        $expectedData = $this->sharedData->getMultiple(['productName', 'purchaseType', 'quantity']);
        $this->checkoutPage->verifyProductDetails($expectedData);
    }

    /**
     * @When I fill in the shipping information with:
     */
    public function iFillInTheShippingInformationWith(TableNode $table): void
    {
        $shippingInfo = $table->getRowsHash();
        $this->checkoutPage->fillShippingInformation($shippingInfo);
        $this->sharedData->set('shippingInfo', $shippingInfo);
    }

    /**
     * @When I use the same address for billing
     */
    public function iUseTheSameAddressForBilling(): void
    {
        $this->checkoutPage->useSameAddressForBilling();
    }

    /**
     * @When I select :method as the shipping method
     */
    public function iSelectAsTheShippingMethod($method): void
    {
        $this->checkoutPage->selectShippingMethod($method);
        $this->sharedData->set('shippingMethod', $method);
    }

    /**
     * @When I verify the shipping cost is :expectedCost
     */
    public function iVerifyTheShippingCostIs($expectedCost): void
    {
        $actualCost = $this->checkoutPage->getShippingCost();
        Assert::assertEquals($expectedCost, $actualCost);
    }

    /**
     * @When I enter the payment details:
     */
    public function iEnterThePaymentDetails(TableNode $table): void
    {
        $paymentDetails = $table->getRowsHash();
        $this->checkoutPage->enterPaymentDetails($paymentDetails);
    }

    /**
     * @When I complete the purchase
     */
    public function iCompleteThePurchase(): void
    {
        $this->checkoutPage->completePurchase();
    }

    /**
     * @Then I should see the order processing page
     */
    public function iShouldSeeTheOrderProcessingPage(): void
    {
        Assert::assertTrue($this->checkoutPage->isProcessingPageDisplayed());
    }

    /**
     * @Then I wait for the order confirmation page to load
     */
    public function iWaitForTheOrderConfirmationPageToLoad(): void
    {
        $this->checkoutPage->waitForOrderConfirmationPage();
    }
}
