<?php


namespace Features\Bootstrap\Page;


use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Behat\Mink\Exception\ElementNotFoundException;
use Exception;
use InvalidArgumentException;
use RuntimeException;

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
    protected string $url = 'https://aeonstest.info/products/';

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
     * Loads the product page for a specific product.
     *
     * @param string $productName The name of the product to load
     * @throws ElementNotFoundException
     */
    public function loadWithName(string $productName): void
    {
        $productSlug = $this->convertNameToSlug($productName);
        $fullUrl = $this->url . $productSlug;
        $this->getSession()->visit($fullUrl);
        $this->waitForPageToLoad();
    }

    /**
     * Converts a product name to a URL-friendly slug.
     * 
     * @param string $name The product name
     * @return string The URL-friendly slug
     */
    private function convertNameToSlug(string $name): string
    {
        return strtolower(str_replace(' ', '-', $name));
    }

    /**
     * Loads the product page.
     * @throws Exception
     */
    public function load(): void
    {
        try {
            $this->open();
        } catch (ElementNotFoundException $e) {
            throw new Exception('Product page not found');
        }
    }

    /**
     * Verifies that the total sum on the "Add to Cart" button is displayed correctly for selected product combinations,
     * and checks if the Subscription option is selected when required.
     * @throws Exception
     */
public function verifySumOfProducts(): void
{
    // Locate the "Add to Cart" button and retrieve its total sum
    $addToCartButton = $this->getSession()->getPage()->find('css', 'button.add-product #product-price');

    // Ensure the "Add to Cart" button is found on the page
    if (null === $addToCartButton) {
        throw new Exception('Add to Cart button not found on the page');
    }

    // Get the total sum displayed on the button (expected format: "£xx.xx")
    $displayedTotal = $addToCartButton->getText();
    
    // Define the expected values based on product selection
    $selectedSizeOption = $this->getSession()->getPage()->find('css', '.choose-item.active');
    $productQuantity = $this->getSession()->getPage()->find('css', 'input#sylius_add_to_cart_cartItem_quantity');
    $pricingMode = $this->getSession()->getPage()->find('css', '.purchase-option.active .product-variant-label-info');
    
    // Verify Subscription option is selected if mode is Subscribe & Save
    $subscriptionOption = $this->getSession()->getPage()->find('css', '.purchase-option.active[data-variant-option-subscription="yes"]');

    if ($selectedSizeOption && $productQuantity && $pricingMode) {
        $sizeOption = trim($selectedSizeOption->find('css', 'p')->getText());
        $quantity = (int) trim($productQuantity->getAttribute('value'));
        $mode = trim($pricingMode->getText());

        // Verify subscription selection if mode is "Subscribe & Save"
        if ($mode === 'Subscribe & Save' && !$subscriptionOption) {
            throw new Exception('Subscription option is not selected, but "Subscribe & Save" mode was chosen.');
        }

        // Calculate expected total based on product and pricing mode
        $expectedTotal = $this->calculateExpectedTotal($sizeOption, $quantity, $mode);

        // Assert that the displayed total matches the expected total
        if ($displayedTotal !== $expectedTotal) {
            throw new Exception(sprintf(
                'Displayed total "%s" does not match expected total "%s" for product "%s" with quantity "%d".',
                $displayedTotal,
                $expectedTotal,
                $sizeOption,
                $quantity
            ));
        }

        // Assert: PASS
        $this->assertSession()->pageTextContains($expectedTotal);
    } else {
        throw new Exception('Unable to retrieve product selection details.');
    }
}

    /**
     * Helper method to calculate expected total based on product size, quantity, and pricing mode.
     * @param string $sizeOption
     * @param int $quantity
     * @param string $mode (One-Time Purchase or Subscribe & Save)
     * @return string (expected total in format "£xx.xx")
     * @throws Exception
     */
private function calculateExpectedTotal(string $sizeOption, int $quantity, string $mode): string
{

    // Define the unit price based on product size and mode
    $unitPrice = match ($sizeOption) {
        '1 Jar' => $mode === 'One-Time Purchase' ? 69.95 : 49.95,
        '3 Jars' => $mode === 'One-Time Purchase' ? 59.95 : 39.95,
        '6 Jars' => $mode === 'One-Time Purchase' ? 49.95 : 29.95,
        default => throw new Exception('Unknown product size: ' . $sizeOption),
    };

    // Calculate the expected total
    $expectedTotal = number_format($unitPrice * $quantity, 2);

    // Return the expected total with currency symbol
    return '£' . $expectedTotal;
}


    /**
     * Opens the product page using its URL and waits for the page to load.
     * @throws ElementNotFoundException
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
        $button->click();
    }

    

    /**
     * Selects the size of the product.
     *
     * @param string $sizeOption Either '1 Jar', '2 Jars', or '3 Jars'.
     * @throws InvalidArgumentException If an invalid size option is provided.
     * @throws ElementNotFoundException If the size option element is not found.
     */
    public function selectSize(string $sizeOption): void
    {
        $sizeMap = [
            '1 Jar' => 1,
            '3 Jars' => 3,
            '6 Jars' => 6
        ];

        if (!isset($sizeMap[$sizeOption])) {
            throw new InvalidArgumentException('Invalid size option');
        }

        $dataValue = $sizeMap[$sizeOption];
        $selector = ".choose-item.set-quantity[data-value=\"$dataValue\"]";
        
        try {
            $element = $this->findElement($selector);
            $element->click();
        } catch (ElementNotFoundException $e) {
            throw new RuntimeException("Unable to find size option: $sizeOption", 0, $e);
        } catch (RuntimeException $e) {
            throw new RuntimeException("Unable to select size option: $sizeOption", 0, $e);
        }
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
     * @throws ElementNotFoundException If the button is not found.
     */
    protected function getAddToCartButton()
    {
        $selector = '#sylius-product-adding-to-cart button.add-product';
        return $this->findElement($selector);
    }

    /**
     * Finds and returns the subscribe button element.
     *
     * @return NodeElement The subscribe button element.
     * @throws ElementNotFoundException If the button is not found.
     */
    protected function getSubscribeButton(): NodeElement
    {
        $selector = ".purchase-option[data-variant-option-subscription='yes']";
        return $this->findElement($selector);
    }

    /**
     * Finds and returns the FAQ title element.
     *
     * @return NodeElement The FAQ title element.
     * @throws ElementNotFoundException If the element is not found.
     */
    protected function getFaqTitleElement(): NodeElement
    {
        $selector = 'p.h1';
        return $this->findElement($selector);
    }

    /**
     * Finds and returns an array of accordion button elements.
     *
     * @return NodeElement[] The accordion button elements.
     */
    protected function getAccordionButtons(): array
    {
        $selector = '.accordion-button';
        return $this->findElements($selector);
    }

    /**
     * Finds and returns an array of expanded accordion sections.
     *
     * @return NodeElement[] The expanded accordion sections.
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
     * @throws ElementNotFoundException
     */
    public function waitForPageToLoad(int $timeout = 10000): void
    {
        parent::waitForPageToLoad($timeout);
        $this->waitForElementVisible('#sylius-product-adding-to-cart', $timeout);
    }

    /**
     * Scrolls to the specified element.
     *
     * @param NodeElement $element The element to scroll to.
     */
    protected function scrollToElement(NodeElement $element): void
    {
        parent::scrollToElement($element);
    }

    /**
     * Finds an element using a link text locator.
     *
     * @param string $linkText The text of the link to find.
     * @return NodeElement The found link element.
     * @throws ElementNotFoundException If the link is not found.
     */
    protected function findElementLink(string $linkText): NodeElement
    {
        $element = $this->session->getPage()->findLink($linkText);
        if (!$element) {
            throw new ElementNotFoundException($this->session, 'link', 'text', $linkText);
        }
        return $element;
    }

    /**
     * @throws ElementNotFoundException
     */
    public function getProductName(): string
    {
        // Placeholder for element selector
        $selector = '.content-grid .title';
        return $this->findElement($selector)->getText();
    }

    /**
     * @throws ElementNotFoundException
     */
    public function getSelectedPurchaseOption(): bool|array|string|null
    {
        // Placeholder for element selector
        $selector = '#sylius-product-adding-to-cart .active.purchase-option .product-variant-label-info.ratio-title';
        return $this->findElement($selector)->getValue();
    }

    /**
     * @throws ElementNotFoundException
     */
    public function getQuantity(): ?string
    {
        // Placeholder for element selector
        $selector = '#sylius-product-adding-to-cart .active.set-quantity';
        return $this->findElement($selector)->getAttribute("data-value");
    }

    /**
     * @throws ElementNotFoundException
     */
    public function selectPurchaseOption(string $purchaseOption): string
    {   //if string is one-time, then click the one-time button, if subscription, then click the subscribe button
        if ($purchaseOption === 'One-Time Purchase') {
            $selector = '#sylius_add_to_cart_cartItem_variant_1';
        } elseif ($purchaseOption === 'Subscribe & Save') {
            $selector = '#sylius_add_to_cart_cartItem_variant_0';
        } else {
            throw new InvalidArgumentException('Invalid purchase option');
        }
       // $this->scrollToElement($this->findElement($selector));
        $this->findElement($selector)->click();
        return $purchaseOption;
    }
}
