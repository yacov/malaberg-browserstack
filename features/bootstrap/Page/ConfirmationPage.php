<?php

namespace Features\Bootstrap\Page;
use Behat\Mink\Exception\ElementNotFoundException;
use PHPUnit\Framework\Assert;
use Behat\Mink\Session;


class ConfirmationPage extends BasePage
{

    public function __construct(Session $session)
    {
        parent::__construct($session);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function waitForPageToLoad(int $timeout = 5000): void
    {
        // Placeholder for element selector
        $selector = '.section-thank-you h1';
        $this->waitForElementVisible($selector);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function getOrderNumber(): string
    {
        // Placeholder for element selector
        $selector = 'ORDER_NUMBER_SELECTOR';
        return $this->findElement($selector)->getText();
    }

    /**
     * @throws ElementNotFoundException
     */
    public function verifyOrderDetails($expectedData): void
    {
        // Verify billing address
        $billingAddress = $this->findElement('BILLING_ADDRESS_SELECTOR')->getText();
        Assert::assertEquals($expectedData['shippingInfo'], $billingAddress, "Billing address does not match.");

        // Verify shipping address
        $shippingAddress = $this->findElement("//div[contains(text(), 'Shipping address')]/address")->getText();
        Assert::assertEquals($expectedData['shippingInfo'], $shippingAddress, "Shipping address does not match.");

        // Verify product name
        $productName = $this->findElement('CONFIRM_PRODUCT_NAME_SELECTOR')->getText();
        Assert::assertEquals($expectedData['productName'], $productName, "Product name does not match.");

        // Verify quantity
        $quantity = $this->findElement('CONFIRM_QUANTITY_SELECTOR')->getText();
        Assert::assertEquals($expectedData['quantity'], $quantity, "Quantity does not match.");

        // Additional verifications...
    }


    protected function getUrl(): string
    {
        return '/confirmation'; // Replace with the actual URL of your confirmation page
    }
}