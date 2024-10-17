<?php

namespace Features\Bootstrap\Page;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;

/**
 * CheckoutPage handles actions on the checkout page.
 */
class CheckoutPage extends BasePage
{


    /**
     * The URL of the checkout page.
     *
     * @var string
     */
    protected string $url = 'https://aeonstest.info/checkout/';

    /**
     * Initializes the CheckoutPage with a Mink session.
     *
     * @param Session $session The Mink session.
     */
    public function __construct(Session $session)
    {
        parent::__construct($session);
    }

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
     * Checks if the current URL matches the checkout page URL.
     *
     * @return bool True if URLs match, false otherwise.
     */
    public function isUrlMatches(): bool
    {
        return $this->getCurrentUrl() === $this->url;
    }

    /**
     * Verifies that the current page is the checkout page by checking the presence of the checkout header.
     *
     * @return bool True if on the checkout page, false otherwise.
     */
    public function isCheckoutPage(): bool
    {
        return $this->isElementVisible('//*[@class=\'checkout-title\']');
    }

    /**
     * Fills in the checkout form with the provided information.
     *
     * @param array $shippingInfo An array containing shipping information.
     * @throws ElementNotFoundException
     */
    public function fillInCheckoutForm(array $shippingInfo): void
    {
        try {
            $this->enterText('#app_one_page_checkout_customer_email', $shippingInfo['Email'] ?? '');
            $this->enterText('#app_one_page_checkout_billingAddress_firstName', $shippingInfo['FirstName'] ?? '');
            $this->enterText('#app_one_page_checkout_billingAddress_lastName', $shippingInfo['LastName'] ?? '');
            $this->enterText('#app_one_page_checkout_billingAddress_phoneNumber', $shippingInfo['Phone'] ?? '');
            $this->enterText('#app_one_page_checkout_billingAddress_street', $shippingInfo['Address'] ?? '');
            $this->enterText('#app_one_page_checkout_billingAddress_city', $shippingInfo['City'] ?? '');
            $this->enterText('#app_one_page_checkout_billingAddress_postcode', $shippingInfo['Postcode'] ?? '');
            $this->selectDropdownOption('#app_one_page_checkout_billingAddress_countryCode', $shippingInfo['Country'] ?? '');
        } catch (\Exception $e) {
            throw new \Exception("Error filling in checkout form: " . $e->getMessage());
        }
    }


    public function useSameAddressForBillingAndShipping(): void
    {
        if (!$this->isSameAsBillingAddressChecked()) {
            $this->findElement('#app_one_page_checkout_differentShippingAddress_0')->click();
        }
    }

    public function isSameAsBillingAddressChecked(): bool
    {
        $element = $this->findElement('#app_one_page_checkout_differentShippingAddress_0');
        return $element->isSelected();
    }

    public function verifyShippingAddressSelection(): bool
    {
        return $this->isSameAsBillingAddressChecked();
    }


    /**
     * Checks if an element is visible on the page.
     *
     * @param string $selector The selector (CSS or XPath).
     * @return bool True if the element is visible, false otherwise.
     */
    public function isElementVisible(string $selector): bool
    {
        try {
            $element = $this->findElement($selector);
            return $element->isVisible();
        } catch (ElementNotFoundException $e) {
            return false;
        }
    }


    /**
     * Waits for the checkout page to load completely.
     * @param int $timeout The maximum time to wait in milliseconds.
     * @throws ElementNotFoundException
     */
    public function waitForPageToLoad(int $timeout = 10000): void
    {
        parent::waitForPageToLoad($timeout);
        $this->waitForElementVisible('//*[@class=\'checkout-title\']', $timeout);
    }

    /**
     * Gets the current URL of the page.
     *
     * @return string The current URL.
     */
    public function getCurrentUrl(): string
    {
        return $this->session->getCurrentUrl();
    }

    /**
     * Enters text into an input field identified by the provided selector.
     *
     * @param string $selector The CSS selector.
     * @param string $text The text to enter.
     * @throws ElementNotFoundException
     */
    public function enterText(string $selector, string $text): void
    {
        try {
            $element = $this->findElement($selector);
            $element->setValue($text);
        } catch (\Exception $e) {
            throw new \Exception("Error entering text for selector '$selector': " . $e->getMessage());
        }
    }

    /**
     * Selects an option from a dropdown menu by visible text.
     *
     * @param string $selector The CSS selector of the select field.
     * @param string $optionText The visible text of the option to select.
     * @throws ElementNotFoundException
     */
    public function selectDropdownOption(string $selector, string $optionText): void
    {
        $element = $this->findElement($selector);
        $element->selectOption($optionText);
    }


    public function getPageTitle(): string
    {
        return $this->session->getPage()->find('xpath', '//*[@id=\'app_one_page_checkout\']//h2')->getText();
    }

    /**
     * @throws ElementNotFoundException
     */
    public function getShippingCost(): string
    {
        $selector = ".order-summary-component .ch-shipping-value span";
        return $this->findElement($selector)->getText();
    }

    /**
 * Selects a shipping method and verifies if it's checked.
 *
 * @param string $method The shipping method to select (e.g., "Domestic tracked")
 * @throws \Exception If the shipping method is not found or cannot be selected
 */
public function selectShippingMethod(string $method): void
{
    $selector = "//span[contains(text(), '$method')]/../../input[@checked='checked']";
    
    try {
        $element = $this->getSession()->getPage()->find('xpath', $selector);
        if (!$element) {
            throw new ElementNotFoundException($this->getSession(), 'radio button', 'xpath', $selector);
        }
        if (!$element->isSelected()) {
            $element->click();
        }
        
        if (!$this->isShippingMethodSelected($method)) {
            throw new \Exception("Failed to select shipping method: $method");
        }
    } catch (ElementNotFoundException $e) {
        throw new \Exception("Shipping method not found: $method");
    }
}

    /**
     * Verifies if a specific shipping method is selected.
     *
     * @param string $method The shipping method to verify (e.g., "Domestic tracked")
     * @return bool True if the specified method is selected, false otherwise
     */
    public function isShippingMethodSelected(string $method): bool
    {
        $selector = "//span[contains(text(), '$method')]/../../input[@checked='checked']";
    
    $element = $this->getSession()->getPage()->find('xpath', $selector);
        return $element !== null;
    }   

    public function isProcessingPageDisplayed(): bool
    {
        // Placeholder for element selector
        $selector = '//*[@class=\'PROCESSING_ICON_SELECTOR\']';
        return $this->isElementVisible($selector);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function completePurchase(): void
    {
        // Placeholder for element selector
        $selector = '//*[@class=\'checkout-main\']//button[@type=\'submit\']';
        $this->findElement($selector)->click();
    }

    public function isShippingMethodDisplayed(): bool
    {
        return $this->isElementVisible('//*[@class=\'shipping-method-selector\']');
    }

    public function isShippingCostDisplayed(): bool
    {
        return $this->isElementVisible('//*[@class=\'shipping-cost-display\']');
    }

    public function isOrderConfirmationPageDisplayed(): bool
    {
        return $this->isElementVisible('//*[@class=\'order-confirmation-header\']');
    }

    public function getOrderNumber(): string
    {
        return $this->findElement('//*[@class=\'order-number\']')->getText();
    }

    public function waitForCheckoutForm($timeout = 10000)
    {
        $this->waitForElementVisible('#app_one_page_checkout_customer_email', $timeout);
    }
}
