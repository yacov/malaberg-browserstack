<?php

namespace Page;

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
        return $this->isElementVisible('h1.checkout-title');
    }

    /**
     * Fills in the checkout form with the provided information.
     *
     * @param string $email Customer's email address.
     * @param string $firstName Customer's first name.
     * @param string $lastName Customer's last name.
     * @param string $phone Customer's phone number.
     * @param string $address Customer's street address.
     * @param string $city Customer's city.
     * @param string $postcode Customer's postal code.
     * @param string $country Customer's country (must match an option in the dropdown).
     * @throws ElementNotFoundException
     */
    public function fillInCheckoutForm(
        string $email,
        string $firstName,
        string $lastName,
        string $phone,
        string $address,
        string $city,
        string $postcode,
        string $country
    ): void {
        $this->enterText('#app_one_page_checkout_customer_email', $email);
        $this->enterText('#app_one_page_checkout_billingAddress_firstName', $firstName);
        $this->enterText('#app_one_page_checkout_billingAddress_lastName', $lastName);
        $this->enterText('#app_one_page_checkout_billingAddress_phoneNumber', $phone);
        $this->enterText('#app_one_page_checkout_billingAddress_street', $address);
        $this->enterText('#app_one_page_checkout_billingAddress_city', $city);
        $this->enterText('#app_one_page_checkout_billingAddress_postcode', $postcode);
        $this->selectDropdownOption('#app_one_page_checkout_billingAddress_countryCode', $country);
    }

    /**
     * Checks if an element is visible on the page.
     *
     * @param string $selector The CSS selector.
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
     * Opens the checkout page.
     */
    public function load(): void
    {
        $this->open();
    }

    /**
     * Waits for the checkout page to load completely.
     *
     * @param int $timeout The maximum time to wait in milliseconds.
     * @throws ElementNotFoundException
     */
    public function waitForPageToLoad(int $timeout = 10000): void
    {
        parent::waitForPageToLoad($timeout);
        $this->waitForElementVisible('h1.checkout-title', $timeout);
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
        $element = $this->findElement($selector);
        $this->scrollToElement($element);
        $element->setValue($text);
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
        $this->scrollToElement($element);
        $element->selectOption($optionText);
    }

    /**
     * Finds and returns a web element using the provided selector.
     *
     * @param string $selector The CSS selector.
     * @param int $timeout The maximum time to wait in milliseconds.
     * @return NodeElement The found element.
     * @throws ElementNotFoundException If the element is not found within the timeout.
     */
    protected function findElement(string $selector, int $timeout = 5000): NodeElement
    {
        return parent::findElement($selector, $timeout);
    }

    /**
     * Scrolls the page to bring an element into view.
     *
     * @param NodeElement $element The element to scroll to.
     */
    protected function scrollToElement(NodeElement $element): void
    {
        parent::scrollToElement($element);
    }

    /**
     * Waits for an element to be visible on the page.
     *
     * @param string $selector The CSS selector.
     * @param int $timeout The maximum time to wait in milliseconds.
     * @throws ElementNotFoundException If the element is not visible within the timeout.
     */
    protected function waitForElementVisible(string $selector, int $timeout = 5000): void
    {
        parent::waitForElementVisible($selector, $timeout);
    }


    public function getPageTitle(): string
    {
        return $this->session->getPage()->find('css', 'PAGE_TITLE_SELECTOR')->getText();
    }

    /**
     * @throws ElementNotFoundException
     */
    public function getShippingCost(): string
    {
        // Placeholder for element selector
        $selector = 'SHIPPING_COST_SELECTOR';
        return $this->findElement($selector)->getText();
    }

    public function isProcessingPageDisplayed(): bool
    {
        // Placeholder for element selector
        $selector = 'PROCESSING_ICON_SELECTOR';
        return $this->isElementVisible($selector);
    }
}