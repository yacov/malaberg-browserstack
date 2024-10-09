<?php

namespace Page;

/**
 * CartPage handles actions on the shopping cart page.
 */
class CartPage extends BasePage
{
    /**
     * Retrieves all items in the cart.
     *
     * @return array An array of cart item elements.
     */
    public function getCartItems()
    {
        return $this->session->getPage()->findAll('css', '.cart-item');
    }

    /**
     * Proceeds to the checkout process.
     */
    public function proceedToCheckout()
    {
        $this->clickButton('Proceed to Checkout');
    }

    /**
     * Retrieves detailed information about the cart.
     *
     * @return array An associative array with cart details.
     */
    public function getCartDetails()
    {
        $details = [];
        $details['product'] = $this->getElement('.cart-product-name')->getText();
        $details['quantity'] = $this->getElement('.cart-quantity')->getText();
        $details['unit_price'] = $this->getElement('.cart-unit-price')->getText();
        $details['total'] = $this->getElement('.cart-total')->getText();
        $details['items_total'] = $this->getElement('.cart-items-total')->getText();
        $details['shipping'] = $this->getElement('.cart-shipping')->getText();
        $details['order_total'] = $this->getElement('.cart-order-total')->getText();

        return $details;
    }
}