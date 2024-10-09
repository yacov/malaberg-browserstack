<?php

namespace Page;

use Behat\Mink\Session;

/**
 * ProductPage handles actions on the product detail page.
 */
class ProductPage extends BasePage
{
    /**
     * Constructor.
     *
     * @param Session $session The Mink session.
     */
    public function __construct(Session $session)
    {
        parent::__construct($session);
    }

    /**
     * Sets the quantity of the product to add to the cart.
     *
     * @param int $quantity The desired quantity.
     */
    public function setQuantity($quantity)
    {
        $this->fillField('quantity', $quantity);
    }

    /**
     * Selects a purchase option (e.g., "One-Time Purchase").
     *
     * @param string $option The purchase option to select.
     */
    public function selectPurchaseOption($option)
    {
        $this->selectOption('purchase_option', $option);
    }

    /**
     * Clicks on the "Add To Bag" button.
     */
    public function addToBag()
    {
        $this->clickButton('Add To Bag');
    }
}