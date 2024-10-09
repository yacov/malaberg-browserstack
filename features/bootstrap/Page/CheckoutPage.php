<?php

namespace Page;

/**
 * CheckoutPage handles actions on the checkout page.
 */
class CheckoutPage extends BasePage
{
    /**
     * Fills in the shipping information form.
     *
     * @param string $info Shipping info in the format "Name, Address, City, Postcode, Country".
     */
    public function fillShippingInformation($info)
    {
        $parts = explode(', ', $info);
        $this->fillField('shipping_name', $parts[0]);
        $this->fillField('shipping_address', $parts[1]);
        $this->fillField('shipping_city', $parts[2]);
        $this->fillField('shipping_postcode', $parts[3]);
        $this->selectOption('shipping_country', $parts[4]);
    }

    /**
     * Uses the shipping address as the billing address.
     */
    public function useShippingAsBilling()
    {
        $this->clickButton('Use shipping address for billing');
    }

    /**
     * Selects a shipping method.
     *
     * @param string $method The shipping method to select.
     */
    public function selectShippingMethod($method)
    {
        $this->selectOption('shipping_method', $method);
    }

    /**
     * Enters the credit card details for payment.
     *
     * @param string $cardInfo Card info in the format "CardNumber, ExpiryDate, CVC".
     */
    public function enterCardDetails($cardInfo)
    {
        $parts = explode(', ', $cardInfo);
        $this->fillField('card_number', $parts[0]);
        $this->fillField('card_expiry', $parts[1]);
        $this->fillField('card_cvc', $parts[2]);
    }

    /**
     * Completes the purchase process.
     */
    public function completePurchase()
    {
        $this->clickButton('Complete Purchase');
    }

    /**
     * Checks if the shipping information was accepted.
     *
     * @return bool True if accepted, false otherwise.
     */
    public function isShippingInformationAccepted()
    {
        return !$this->session->getPage()->hasContent('Invalid shipping information');
    }

    /**
     * Checks if the billing address matches the shipping address.
     *
     * @return bool True if they are the same, false otherwise.
     */
    public function isBillingAddressSameAsShipping()
    {
        $element = $this->session->getPage()->findField('billing_same_as_shipping');
        return $element ? $element->isChecked() : false;
    }

    /**
     * Checks if the selected shipping method is displayed.
     *
     * @return bool True if displayed, false otherwise.
     */
    public function isShippingMethodDisplayed()
    {
        return $this->getElement('.shipping-method')->isVisible();
    }

    /**
     * Checks if the shipping cost is displayed.
     *
     * @return bool True if displayed, false otherwise.
     */
    public function isShippingCostDisplayed()
    {
        return $this->getElement('.shipping-cost')->isVisible();
    }

    /**
     * Checks if the card details were accepted.
     *
     * @return bool True if accepted, false otherwise.
     */
    public function areCardDetailsAccepted()
    {
        return !$this->session->getPage()->hasContent('Invalid card details');
    }
}