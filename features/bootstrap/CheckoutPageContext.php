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
     * @Then the checkout page is displayed
     */
    public function theCheckoutPageIsDisplayed(): void
    {
        Assert::assertTrue($this->checkoutPage->isCheckoutPage(), "The checkout page is not displayed.");
    }

    /**
     * @When I provide shipping information:
     */
    public function iProvideShippingInformation(TableNode $table): void
    {
        $shippingInfo = $table->getRowsHash();
        $this->checkoutPage->fillShippingInformation($shippingInfo);
        $this->sharedData->set('shippingInfo', $shippingInfo);
    }

    /**
     * @When I choose to use the same address for billing
     */
    public function iChooseToUseTheSameAddressForBilling(): void
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
     * @Then the shipping method and cost are displayed
     */
    public function theShippingMethodAndCostAreDisplayed(): void
    {
        Assert::assertTrue($this->checkoutPage->isShippingMethodDisplayed(), "Shipping method is not displayed.");
        Assert::assertTrue($this->checkoutPage->isShippingCostDisplayed(), "Shipping cost is not displayed.");
    }

    /**
     * @When I provide payment details:
     */
    public function iProvidePaymentDetails(TableNode $table): void
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
     * @Then the order confirmation page is displayed
     */
    public function theOrderConfirmationPageIsDisplayed(): void
    {
        Assert::assertTrue($this->checkoutPage->isOrderConfirmationPageDisplayed(), "Order confirmation page is not displayed.");
    }

    /**
     * @Then I see the order number
     */
    public function iSeeTheOrderNumber(): void
    {
        $orderNumber = $this->checkoutPage->getOrderNumber();
        Assert::assertNotEmpty($orderNumber, "Order number is not displayed.");
        $this->sharedData->set('orderNumber', $orderNumber);
    }

    /**
     * @Then the order details are correct
     */
    public function theOrderDetailsAreCorrect(): void
    {
        $expectedData = $this->sharedData->getAll();
        $this->checkoutPage->verifyOrderDetails($expectedData);
    }
}
