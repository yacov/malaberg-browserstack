<?php

namespace Page;
use PHPUnit\Framework\Assert;


class ConfirmationPage extends BasePage
{
    public function waitForPageToLoad(int $timeout = 5000): void
    {
        // Placeholder for element selector
        $selector = 'ORDER_CONFIRMATION_MESSAGE_SELECTOR';
        $this->waitForElementVisible($selector);
    }

    public function getOrderNumber()
    {
        // Placeholder for element selector
        $selector = 'ORDER_NUMBER_SELECTOR';
        return $this->findElement($selector)->getText();
    }

    public function verifyOrderDetails($expectedData): void
    {
        // Verify billing address
        $billingAddress = $this->findElement('BILLING_ADDRESS_SELECTOR')->getText();
        Assert::assertEquals($expectedData['shippingInfo'], $billingAddress, "Billing address does not match.");

        // Verify shipping address
        $shippingAddress = $this->findElement('SHIPPING_ADDRESS_SELECTOR')->getText();
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