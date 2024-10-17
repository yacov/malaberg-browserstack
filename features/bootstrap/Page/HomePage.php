<?php

namespace Features\Bootstrap\Page;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;

/**
 * HomePage handles actions on the main landing page.
 */
class HomePage extends BasePage
{
    /**
     * The URL of the home page.
     *
     * @var string
     */
    protected string $url = '/';

    /**
     * Navigates to the main page and waits for it to load.
     * @throws ElementNotFoundException
     */
    public function open(): void
    {
        $this->openPage();
    }

    /**
     * Opens the home page using its URL and waits for the page to load.
     * @throws ElementNotFoundException
     */
    protected function openPage(): void
    {
        $url = $this->getUrl();
        $this->session->visit($url);
        $this->waitForPageToLoad();
        $this->waitForElementVisible('a.btn[href="/range"]');
    }

    /**
     * Retrieves the URL of the home page.
     *
     * @return string The page URL.
     */
    protected function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Clicks the "Shop Now" button on the main page.
     *
     * @throws ElementNotFoundException If the "Shop Now" button is not found.
     */
    public function clickShopNow(): void
    {
        $button = $this->getShopNowButton();
        $this->scrollToElement($button);
        $button->click();
    }

    /**
     * Finds and returns the "Shop Now" button element.
     *
     * @return NodeElement The "Shop Now" button element.
     * @throws ElementNotFoundException If the button is not found.
     */
    protected function getShopNowButton(): NodeElement
    {
        $selector = 'a.btn[href="/range"]';
        return $this->findElement($selector);
    }

    public function getPageTitle(): string
    {
        return $this->session->getPage()->find('css', 'TITLE_SELECTOR')->getText();
    }

    /**
     * @throws ElementNotFoundException
     */
    public function selectProduct($productName): void
    {
        // Placeholder for element selector
        $selector = sprintf('PRODUCT_LINK_SELECTOR', $productName);
        $this->clickElement($selector);
    }
}
