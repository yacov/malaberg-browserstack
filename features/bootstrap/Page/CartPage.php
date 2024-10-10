<?php


namespace Page;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;

/**
 * CartPage handles actions on the shopping cart page.
 */
class CartPage extends BasePage
{
    /**
     * The URL of the cart page.
     *
     * @var string
     */
    protected string $url = 'https://aeonstest.info/cart/';

    /**
     * Retrieves the URL of the page.
     *
     * @return string The page URL.
     */
    protected function getUrl(): string
    {
        return $this->url;
    }


    /**
     * Navigates to the cart page URL.
     */
    public function load(): void
    {
        $this->open();
    }

    /**
     * Waits for the cart page to load.
     *
     * @param int $timeout The maximum time to wait in milliseconds.
     * @throws ElementNotFoundException
     */
    public function waitForPageToLoad(int $timeout = 10000): void
    {
        parent::waitForPageToLoad($timeout);
        $this->waitForElementVisible('.cart-title', $timeout);
    }

    /**
     * Checks if the current URL matches the cart page URL.
     *
     * @return bool True if URLs match, false otherwise.
     */
    public function isUrlMatches(): bool
    {
        return $this->getCurrentUrl() === $this->url;
    }

    /**
     * Gets the title text of the cart page.
     *
     * @return string The cart title.
     * @throws ElementNotFoundException
     */
    public function getCartTitle(): string
    {
        $element = $this->findElement('.cart-title h1');
        return trim($element->getText());
    }

    /**
     * Checks if the specified product is displayed in the cart.
     *
     * @param string $productName The name of the product.
     * @return bool True if the product is displayed, false otherwise.
     */
    public function isProductDisplayed(string $productName): bool
    {
        try {
            $element = $this->findElement('.product-description h3');
            return trim($element->getText()) === $productName;
        } catch (ElementNotFoundException $e) {
            return false;
        }
    }

    /**
     * Checks if the product image is displayed.
     *
     * @return bool True if the product image is displayed, false otherwise.
     */
    public function isProductImageDisplayed(): bool
    {
        try {
            $element = $this->findElement('.product-image-and-description img');
            return $element->isVisible();
        } catch (ElementNotFoundException $e) {
            return false;
        }
    }

    /**
     * Updates the quantity of the product in the cart.
     *
     * @param int $quantity The desired quantity.
     * @throws ElementNotFoundException
     */
    public function updateQuantity(int $quantity): void
    {
        $selector = '#sylius_cart_items_0_quantity';
        $element = $this->findElement($selector);
        $element->setValue('');
        $element->setValue((string)$quantity);
    }

    /**
     * Gets the current quantity value from the quantity input.
     *
     * @return int The quantity.
     * @throws ElementNotFoundException
     */
    public function getQuantity(): int
    {
        $selector = '#sylius_cart_items_0_quantity';
        $element = $this->findElement($selector);
        return (int)$element->getValue();
    }

    /**
     * Gets the unit price of the product.
     *
     * @return float The unit price.
     * @throws ElementNotFoundException
     */
    public function getUnitPrice(): float
    {
        $selector = 'td.numbers span';
        $element = $this->findElement($selector);
        $priceText = $element->getText();
        return $this->getNumericValue($priceText);
    }

    /**
     * Gets the total price from the cart.
     *
     * @return float The total price.
     * @throws ElementNotFoundException
     */
    public function getTotalPrice(): float
    {
        $selector = 'td.numbers:nth-child(4)';
        $element = $this->findElement($selector);
        $priceText = $element->getText();
        return $this->getNumericValue($priceText);
    }

    /**
     * Removes the item from the cart.
     */
    public function removeItem(): void
    {
        $selector = '.remove-item-button';
        $this->clickElement($selector);
    }

    /**
     * Checks if the remove item button is displayed.
     *
     * @return bool True if displayed, false otherwise.
     */
    public function isRemoveButtonDisplayed(): bool
    {
        try {
            $element = $this->findElement('.remove-item-button');
            return $element->isVisible();
        } catch (ElementNotFoundException $e) {
            return false;
        }
    }

    /**
     * Applies a coupon code to the cart.
     *
     * @param string $couponCode The coupon code.
     * @throws ElementNotFoundException
     */
    public function applyCoupon(string $couponCode): void
    {
        $inputSelector = '#sylius_cart_promotionCoupon';
        $buttonSelector = '.coupon-section button[type=submit]';

        $this->enterText($inputSelector, $couponCode);
        $this->clickElement($buttonSelector);
    }

    /**
     * Checks if an error message is displayed.
     *
     * @return bool True if error message is displayed, false otherwise.
     */
    public function isErrorMessageDisplayed(): bool
    {
        try {
            $element = $this->findElement('.alert-danger');
            return $element->isVisible();
        } catch (ElementNotFoundException $e) {
            return false;
        }
    }

    /**
     * Clicks the update cart button to update the cart details.
     */
    public function updateCart(): void
    {
        $selector = '.update-cart-button';
        $this->clickElement($selector);
    }

    /**
     * Waits for the cart to update by checking for the appearance of the success message or any changes in the cart.
     *
     * @param int $timeout The maximum time to wait in milliseconds.
     */
    public function waitForCartToUpdate(int $timeout = 10000): void
    {
        $this->session->wait(
            $timeout,
            "document.querySelector('.alert.alert-success') !== null"
        );
    }

    /**
     * Checks if the coupon was applied successfully.
     *
     * @return bool True if coupon seems to be applied, false otherwise.
     */
    public function isCouponAppliedSuccessfully(): bool
    {
        try {
            $element = $this->findElement('.alert.alert-success');
            return stripos($element->getText(), 'coupon applied') !== false;
        } catch (ElementNotFoundException $e) {
            return $this->verifyOrderTotal();
        }
    }

    /**
     * Checks if the cart is empty.
     *
     * @return bool True if cart is empty and displays the correct message, false otherwise.
     */
    public function isCartEmpty(): bool
    {
        try {
            $element = $this->findElement('.cart-title .alert');
            return trim($element->getText()) === 'Your cart is empty';
        } catch (ElementNotFoundException $e) {
            return false;
        }
    }

    /**
     * Clicks the checkout button to proceed to the checkout page.
     */
    public function proceedToCheckout(): void
    {
        $selector = '.checkout-btn';
        $this->clickElement($selector);
    }

    /**
     * Checks if the success message for adding an item to the cart is displayed.
     *
     * @return bool True if the success message is displayed and contains the expected text, false otherwise.
     */
    public function isSuccessMessageDisplayed(): bool
    {
        try {
            $element = $this->findElement('.alert-success');
            return $element->isVisible() &&
                stripos($element->getText(), 'Item has been added to cart') !== false;
        } catch (ElementNotFoundException $e) {
            return false;
        }
    }

    /**
     * Gets the purchase type text from the cart page.
     *
     * @return string The purchase type.
     * @throws ElementNotFoundException
     */
    public function getPurchaseType(): string
    {
        $selector = "//td[contains(text(), 'Purchase type:')]//following-sibling::td";
        $element = $this->findElement($selector);
        return trim($element->getText());
    }

    /**
     * Checks if the user is prevented from proceeding to checkout due to an empty cart.
     *
     * @return bool True if prevented with an error message, false otherwise.
     */
    public function isPreventedFromCheckout(): bool
    {
        try {
            $element = $this->findElement('.checkout-error-message');
            return $element->isVisible();
        } catch (ElementNotFoundException $e) {
            return false;
        }
    }

    /**
     * Extracts numeric value from text, removing currency symbols and commas.
     *
     * @param string $text The text containing the numeric value.
     * @return float The numeric value.
     */
    protected function getNumericValue(string $text): float
    {
        $text = str_replace(['£', ',', ' '], '', $text);
        return (float)trim($text);
    }

    /**
     * Checks if the discount is applied correctly.
     *
     * @return bool True if discount is applied correctly, false otherwise.
     */
    public function isDiscountApplied(): bool
    {
        try {
            // Check if the 'Promotion discount:' row is present
            $discountRowSelector = ".summary-section p:contains('Promotion discount:')";
            $this->waitForElementVisible($discountRowSelector);

            // Get the items total and promotion discount amounts
            $itemsTotal = $this->getNumericValue($this->findElement(".summary-section p:contains('Items total:') .numbers")->getText());
            $discountAmount = $this->getNumericValue($this->findElement(".summary-section p:contains('Promotion discount:') .numbers")->getText());

            // Calculate expected discount (15% of items total)
            $expectedDiscount = round($itemsTotal * 0.15, 2);

            // Check if the actual discount matches the expected discount
            return abs($discountAmount - $expectedDiscount) < 0.01;
        } catch (ElementNotFoundException $e) {
            return false;
        }
    }

    /**
     * Verifies that the order total is correct based on items total, promotion discount, and shipping cost.
     *
     * @return bool True if order total is correct, false otherwise.
     */
    public function verifyOrderTotal(): bool
    {
        try {
            $itemsTotal = $this->getNumericValue($this->findElement(".summary-section p:contains('Items total:') .numbers")->getText());
            $promotionDiscount = $this->getNumericValue($this->findElement(".summary-section p:contains('Promotion discount:') .numbers")->getText());
            $shippingCost = $this->getNumericValue($this->findElement(".summary-section p:contains('Shipping:') .numbers")->getText());
            $displayedOrderTotal = $this->getNumericValue($this->findElement(".summary-section p:contains('Order total:') .numbers")->getText());

            $calculatedOrderTotal = $itemsTotal - $promotionDiscount + $shippingCost;

            return abs($calculatedOrderTotal - $displayedOrderTotal) < 0.01;
        } catch (ElementNotFoundException $e) {
            return false;
        }
    }
}