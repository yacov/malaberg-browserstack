<?php

namespace Features\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Exception;
use Page\CartPage;
use Behat\Mink\Exception\ElementNotFoundException;

/**
 * Defines application features from the Cart Page context.
 */
class CartPageContext extends MinkContext implements Context
{
    /**
     * @var CartPage
     */
    private CartPage $cartPage;

    /**
     * @var SharedDataContext
     */
    private SharedDataContext $sharedData;

    /**
     * Initializes context.
     */
    public function __construct(SharedDataContext $sharedData)
    {
        $this->cartPage = new CartPage($this->getSession());
        $this->sharedData = $sharedData;
    }

    /**
     * @Given user is on the cart page
     */
    public function userIsOnCartPage(): void
    {
        $this->cartPage->load();
        if (!$this->cartPage->isUrlMatches()) {
            throw new Exception("User is not on the cart page");
        }
    }
    

    /**
     * @When user updates the quantity to :quantity
     */
    public function userUpdatesTheQuantityTo($quantity): void
    {
        $this->cartPage->updateQuantity($quantity);
        $this->cartPage->updateCart();
        $this->cartPage->waitForCartToUpdate();
    }

    /**
     * @Then the total price should be updated correctly
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
     * @When user decreases the quantity to :quantity
     */
    public function userDecreasesTheQuantityTo($quantity): void
    {
        $this->cartPage->updateQuantity($quantity);
        $this->cartPage->updateCart();
        $this->cartPage->waitForCartToUpdate();
    }

    /**
     * @When user removes the item
     */
    public function userRemovesTheItem(): void
    {
        $this->cartPage->removeItem();
        $this->cartPage->waitForCartToUpdate();
    }

    /**
     * @Then the cart should be empty
     * @throws Exception
     */
    public function theCartShouldBeEmpty(): void
    {
        if (!$this->cartPage->isCartEmpty()) {
            throw new Exception("Cart is not empty or doesn't display the correct empty message");
        }
    }

    /**
     * @When user applies a valid coupon code :couponCode
     */
    public function userAppliesAValidCouponCode($couponCode): void
    {
        $this->cartPage->applyCoupon($couponCode);
        $this->cartPage->waitForCartToUpdate();
    }

    /**
     * @Then the discount should be applied
     * @throws Exception
     */
    public function theDiscountShouldBeApplied(): void
    {
        if (!$this->cartPage->isDiscountApplied()) {
            throw new Exception("Discount was not applied correctly");
        }
    }

    /**
     * @Then the order total should be calculated correctly
     * @throws Exception
     */
    public function theOrderTotalShouldBeCalculatedCorrectly(): void
    {
        if (!$this->cartPage->verifyOrderTotal()) {
            throw new Exception("Order total is not calculated correctly");
        }
    }

    /**
     * @When user applies an invalid coupon code :couponCode
     */
    public function userAppliesAnInvalidCouponCode($couponCode): void
    {
        $this->cartPage->applyCoupon($couponCode);
        $this->cartPage->waitForCartToUpdate();
    }

    /**
     * @Then an error message should be displayed
     * @throws Exception
     */
    public function anErrorMessageShouldBeDisplayed(): void
    {
        if (!$this->cartPage->isErrorMessageDisplayed()) {
            throw new Exception("Error message was not displayed");
        }
    }

    /**
     * @When user proceeds to checkout
     */
    public function userProceedsToCheckout(): void
    {
        $this->cartPage->proceedToCheckout();
    }

    /**
     * @When user tries to proceed to checkout with an empty cart
     */
    public function userTriesToProceedToCheckoutWithAnEmptyCart(): void
    {
        $this->cartPage->proceedToCheckout();
    }

    /**
     * @Then user should be prevented from proceeding
     * @throws Exception
     */
    public function userShouldBePreventedFromProceeding(): void
    {
        if (!$this->cartPage->isPreventedFromCheckout()) {
            throw new Exception("User was not prevented from proceeding with an empty cart");
        }
    }

      /**
     * @Then I verify the cart contains the correct product details
     */
    public function iVerifyTheCartContainsTheCorrectProductDetails(): void
    {
        $expectedData = $this->sharedData->getMultiple(['productName', 'purchaseType', 'quantity']);
        $this->cartPage->verifyProductDetails($expectedData);
    }

    /**
     * @When I proceed to checkout
     */
    public function iProceedToCheckout(): void
    {
        $this->cartPage->proceedToCheckout();
    }
}