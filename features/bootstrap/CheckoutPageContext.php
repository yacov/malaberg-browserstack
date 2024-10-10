<?php

namespace Features\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Exception;
use Page\CheckoutPage;
use Behat\Mink\Exception\ElementNotFoundException;

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
     * Initializes context.
     */
    public function __construct()
    {
        $this->checkoutPage = new CheckoutPage($this->getSession());
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
}