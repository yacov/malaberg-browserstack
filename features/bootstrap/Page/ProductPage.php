<?php


namespace Page;


use Behat\Mink\Session;
use Behat\Mink\Exception\ElementNotFoundException;
use InvalidArgumentException;

/**
 * ProductPage handles actions on the product detail page.
 */
class ProductPage extends BasePage
{
    /**
     * The URL of the product page.
     *
     * @var string
     */
    protected $url = 'https://aeonstest.info/products/aeons-total-harmony';

    /**
     * Initializes the ProductPage with a Mink session.
     *
     * @param Session $session The Mink session.
     */
    public function __construct(Session $session)
    {
        parent::__construct($session);
    }

    /**
     * Retrieves the URL of the product page.
     *
     * @return string The page URL.
     */
    protected function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Loads the product page.
     */
    public function load(): void
    {
        $this->open();
    }

    /**
     * Opens the product page using its URL and waits for the page to load.
     */
    public function open(): void
    {
        $this->session->visit($this->getUrl());
        $this->waitForPageToLoad();
        // Wait for a unique element on the product page to ensure it's loaded
        $this->waitForElementVisible('#sylius-product-adding-to-cart button.add-product');
    }

    /**
     * Adds the product to the cart by clicking the 'Add to Cart' button.
     *
     * @throws ElementNotFoundException If the 'Add to Cart' button is not found.
     */
    public function addToCart(): void
    {
        $button = $this->getAddToCartButton();
        $this->scrollToElement($button);
        $button->click();
    }

    /**
     * Selects the size of the product.
     *
     * @param string $sizeOption Either '250ml' or '3bottles'.
     * @throws InvalidArgumentException If an invalid size option is provided.
     * @throws ElementNotFoundException If the size option element is not found.
     */
    public function selectSize(string $sizeOption): void
    {
        if ($sizeOption === '250ml') {
            $radioButton = $this->getSizeRadioButton250ml();
        } elseif ($sizeOption === '3bottles') {
            $radioButton = $this->getSizeRadioButton3Bottles();
        } else {
            throw new InvalidArgumentException('Invalid size option');
        }

        $this->scrollToElement($radioButton);
        $radioButton->click();
    }

    /**
     * Clicks the subscribe button for the product.
     *
     * @throws ElementNotFoundException If the subscribe button is not found.
     */
    public function clickToSubscribe(): void
    {
        $button = $this->getSubscribeButton();
        $this->scrollToElement($button);
        $button->click();
    }

    /**
     * Gets the text of the FAQ title.
     *
     * @return string The FAQ title text.
     * @throws ElementNotFoundException If the FAQ title element is not found.
     */
    public function getFaqTitle(): string
    {
        $element = $this->getFaqTitleElement();
        return $element->getText();
    }

    /**
     * Clicks on a specific accordion button.
     *
     * @param int $index Zero-based index of the accordion button to click.
     * @throws InvalidArgumentException If the provided index is out of range.
     */
    public function clickAccordionButton(int $index): void
    {
        $buttons = $this->getAccordionButtons();

        if (isset($buttons[$index])) {
            $button = $buttons[$index];
            $this->scrollToElement($button);
            $button->click();
        } else {
            throw new InvalidArgumentException('Invalid accordion button index');
        }
    }

    /**
     * Checks if a specific accordion section is expanded.
     *
     * @param int $index Zero-based index of the accordion section to check.
     * @return bool True if the section is expanded, false otherwise.
     */
    public function isSectionExpanded(int $index): bool
    {
        $expandedSections = $this->getExpandedSections();
        return isset($expandedSections[$index]);
    }

    /**
     * Selects a product by its name.
     *
     * @param string $productName Name of the product to select.
     * @throws ElementNotFoundException If the product link is not found.
     */
    public function selectProductByName(string $productName): void
    {
        $productLink = $this->findElementLink($productName);
        $this->scrollToElement($productLink);
        $productLink->click();
    }

    /**
     * Finds and returns the 'Add to Cart' button element.
     *
     * @return \Behat\Mink\Element\NodeElement The 'Add to Cart' button element.
     * @throws ElementNotFoundException If the button is not found.
     */
    protected function getAddToCartButton()
    {
        $selector = '#sylius-product-adding-to-cart button.add-product';
        return $this->findElement($selector);
    }

    /**
     * Finds and returns the 250ml size radio button element.
     *
     * @return \Behat\Mink\Element\NodeElement The 250ml size radio button element.
     * @throws ElementNotFoundException If the radio button is not found.
     */
    protected function getSizeRadioButton250ml()
    {
        $selector = '#sylius_add_to_cart_cartItem_variant_0';
        return $this->findElement($selector);
    }

    /**
     * Finds and returns the 3 bottles size radio button element.
     *
     * @return \Behat\Mink\Element\NodeElement The 3 bottles size radio button element.
     * @throws ElementNotFoundException If the radio button is not found.
     */
    protected function getSizeRadioButton3Bottles()
    {
        $selector = '#sylius_add_to_cart_cartItem_variant_1';
        return $this->findElement($selector);
    }

    /**
     * Finds and returns the subscribe button element.
     *
     * @return \Behat\Mink\Element\NodeElement The subscribe button element.
     * @throws ElementNotFoundException If the button is not found.
     */
    protected function getSubscribeButton()
    {
        $selector = ".purchase-option[data-variant-option-subscription='yes']";
        return $this->findElement($selector);
    }

    /**
     * Finds and returns the FAQ title element.
     *
     * @return \Behat\Mink\Element\NodeElement The FAQ title element.
     * @throws ElementNotFoundException If the element is not found.
     */
    protected function getFaqTitleElement()
    {
        $selector = 'p.h1';
        return $this->findElement($selector);
    }

    /**
     * Finds and returns an array of accordion button elements.
     *
     * @return \Behat\Mink\Element\NodeElement[] The accordion button elements.
     */
    protected function getAccordionButtons(): array
    {
        $selector = '.accordion-button';
        return $this->findElements($selector);
    }

    /**
     * Finds and returns an array of expanded accordion sections.
     *
     * @return \Behat\Mink\Element\NodeElement[] The expanded accordion sections.
     */
    protected function getExpandedSections(): array
    {
        $selector = '.accordion-collapse.show';
        return $this->findElements($selector);
    }

    /**
     * Waits for the page to load completely.
     *
     * @param int $timeout The maximum time to wait in milliseconds.
     */
    public function waitForPageToLoad(int $timeout = 10000): void
    {
        parent::waitForPageToLoad($timeout);
        $this->waitForElementVisible('#sylius-product-adding-to-cart', $timeout);
    }

    /**
     * Scrolls to the specified element.
     *
     * @param \Behat\Mink\Element\NodeElement $element The element to scroll to.
     */
    protected function scrollToElement($element): void
    {
        parent::scrollToElement($element);
    }

    /**
     * Finds an element using a link text locator.
     *
     * @param string $linkText The text of the link to find.
     * @return \Behat\Mink\Element\NodeElement The found link element.
     * @throws ElementNotFoundException If the link is not found.
     */
    protected function findElementLink(string $linkText)
    {
        $element = $this->session->getPage()->findLink($linkText);
        if (!$element) {
            throw new ElementNotFoundException($this->session, 'link', 'text', $linkText);
        }
        return $element;
    }
}