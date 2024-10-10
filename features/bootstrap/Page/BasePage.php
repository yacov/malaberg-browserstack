<?php

namespace Page;

use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\Session;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;

/**
 * BasePage provides common functionalities for page objects.
 */
abstract class BasePage
{
    /**
     * The Mink session instance.
     *
     * @var Session
     */
    protected Session $session;

    /**
     * Initializes the BasePage with a Mink session.
     *
     * @param Session $session The Mink session.
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Opens the page using its URL and waits for the page to load.
     */
    public function open(): void
    {
        $url = $this->getUrl();
        $this->session->visit($url);
        $this->waitForPageToLoad();
    }

    /**
     * Retrieves the URL of the page.
     *
     * @return string The page URL.
     */
    abstract protected function getUrl(): string;

    /**
     * Finds and returns a web element using the provided locator.
     *
     * @param string $selector The CSS selector.
     * @param int $timeout The maximum time to wait in milliseconds.
     * @return NodeElement The found element.
     * @throws ElementNotFoundException If the element is not found within the timeout.
     */
    protected function findElement(string $selector, int $timeout = 5000): NodeElement
    {
        $this->waitForElementVisible($selector, $timeout);
        $element = $this->session->getPage()->find('css', $selector);

        if (!$element) {
            throw new ElementNotFoundException($this->session, 'element', 'css', $selector);
        }

        return $element;
    }

    /**
     * Finds and returns a list of web elements using the provided locator.
     *
     * @param string $selector The CSS selector.
     * @return NodeElement[] An array of found elements.
     */
    protected function findElements(string $selector): array
    {
        return $this->session->getPage()->findAll('css', $selector);
    }

    /**
     * Scrolls the page to bring an element into view.
     *
     * @param NodeElement $element The element to scroll to.
     */
    protected function scrollToElement(NodeElement $element): void
    {
        $function = <<<JS
        (function(element) {
            element.scrollIntoView({behavior: 'smooth', block: 'center'});
        })(arguments[0]);
        JS;
        $this->session->executeScript($function, [$element->getXpath()]);
    }

    /**
     * Clicks on a web element identified by the provided selector.
     *
     * @param string $selector The CSS selector.
     * @throws ElementNotFoundException
     */
    public function clickElement(string $selector): void
    {
        $element = $this->findElement($selector);
        $this->scrollToElement($element);
        $element->click();
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
     * Gets the text content of an element identified by the provided selector.
     *
     * @param string $selector The CSS selector.
     * @return string The text content of the element.
     * @throws ElementNotFoundException
     */
    public function getElementText(string $selector): string
    {
        $element = $this->findElement($selector);
        $this->scrollToElement($element);
        return $element->getText();
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
     * Checks if an element is present in the DOM.
     *
     * @param string $selector The CSS selector.
     * @return bool True if the element is present, false otherwise.
     */
    public function isElementPresent(string $selector): bool
    {
        $element = $this->session->getPage()->find('css', $selector);
        return $element !== null;
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
     * Gets the title of the current page.
     *
     * @return string The page title.
     */
    public function getPageTitle(): string
    {
        return $this->session->getPage()->find('css', 'title')->getText();
    }

    /**
     * Gets the URL of the current page.
     *
     * @return string The current URL.
     */
    public function getCurrentUrl(): string
    {
        return $this->session->getCurrentUrl();
    }

    /**
     * Waits for the browser to navigate to a specific URL.
     *
     * @param string $url The expected URL.
     * @param int $timeout The maximum time to wait in milliseconds.
     * @throws ExpectationException If the URL does not match within the timeout.
     */
    public function waitForUrlToBe(string $url, int $timeout = 5000): void
    {
        $this->session->wait($timeout, "window.location.href === '$url'");
        if ($this->getCurrentUrl() !== $url) {
            throw new ExpectationException("Expected URL to be '$url', but found '{$this->getCurrentUrl()}'", $this->session);
        }
    }

    /**
     * Switches focus to an iframe on the page.
     *
     * @param string $selector The CSS selector of the iframe.
     * @throws DriverException
     * @throws ElementNotFoundException
     * @throws UnsupportedDriverActionException
     */
    public function switchToFrame(string $selector): void
    {
        $iframe = $this->findElement($selector);
        $driver = $this->session->getDriver();
        $driver->switchToIFrame($iframe->getAttribute('name') ?: $iframe->getAttribute('id'));
    }

    /**
     * Switches focus back to the default content (out of any iframes).
     */
    public function switchToDefaultContent(): void
    {
        $this->session->getDriver()->switchToIFrame();
    }

    /**
     * Gets the value of an attribute from an element.
     *
     * @param string $selector The CSS selector.
     * @param string $attributeName The name of the attribute.
     * @return string|null The value of the attribute or null if not found.
     * @throws ElementNotFoundException
     */
    public function getElementAttribute(string $selector, string $attributeName): ?string
    {
        $element = $this->findElement($selector);
        $this->scrollToElement($element);
        return $element->getAttribute($attributeName);
    }

    /**
     * Waits until the specified element disappears from the page.
     *
     * @param string $selector The CSS selector.
     * @param int $timeout The maximum time to wait in milliseconds.
     */
    public function waitForElementToDisappear(string $selector, int $timeout = 5000): void
    {
        $this->session->wait($timeout, "document.querySelector('$selector') === null");
    }

    /**
     * Checks if an element is not present on the page.
     *
     * @param string $selector The CSS selector.
     * @param int $timeout The maximum time to wait in milliseconds.
     * @return bool True if the element is not present, false otherwise.
     */
    public function isElementNotPresent(string $selector, int $timeout = 5000): bool
    {
        try {
            $this->waitForElementToDisappear($selector, $timeout);
            return true;
        } catch (ExpectationException $e) {
            return false;
        }
    }

    /**
     * Waits for the page to fully load.
     *
     * @param int $timeout The maximum time to wait in milliseconds.
     */
    public function waitForPageToLoad(int $timeout = 5000): void
    {
        $this->session->wait($timeout, "document.readyState === 'complete'");
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
        $endTime = microtime(true) + $timeout / 1000;
        while (microtime(true) < $endTime) {
            $element = $this->session->getPage()->find('css', $selector);
            if ($element && $element->isVisible()) {
                return;
            }
            usleep(100000); // Wait 100ms before checking again
        }
        throw new ElementNotFoundException($this->session, 'element', 'css', $selector);
    }
}