<?php

namespace Page;

use Behat\Mink\Session;
use Behat\Mink\Exception\ElementNotFoundException;

/**
 * BasePage provides common functionalities for page objects.
 */
abstract class BasePage
{
    /**
     * @var Session The Mink session instance.
     */
    protected $session;

    /**
     * Constructor.
     *
     * @param Session $session The Mink session.
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Retrieves an element using a CSS selector.
     *
     * @param string $selector The CSS selector.
     * @return \Behat\Mink\Element\NodeElement The found element.
     * @throws ElementNotFoundException If the element is not found.
     */
    protected function getElement($selector)
    {
        return $this->waitForElement($selector);
    }

    /**
     * Fills a field with the specified value.
     *
     * @param string $field The field name or selector.
     * @param string $value The value to fill in.
     */
    protected function fillField($field, $value)
    {
        $this->session->getPage()->fillField($field, $value);
    }

    /**
     * Clicks a button on the page.
     *
     * @param string $button The button name or selector.
     */
    public function clickButton($button)
    {
        $this->session->getPage()->pressButton($button);
    }

    /**
     * Selects an option from a dropdown or radio buttons.
     *
     * @param string $select The select field name or selector.
     * @param string $option The option to select.
     */
    protected function selectOption($select, $option)
    {
        $this->session->getPage()->selectFieldOption($select, $option);
    }

    /**
     * Waits for an element to be present and visible.
     *
     * @param string $selector The CSS selector.
     * @param int $timeout The timeout in milliseconds.
     * @return \Behat\Mink\Element\NodeElement The found element.
     * @throws ElementNotFoundException If the element is not found within the timeout.
     */
    protected function waitForElement($selector, $timeout = 5000)
    {
        $this->session->wait($timeout, "document.querySelector('$selector') !== null");
        $element = $this->session->getPage()->find('css', $selector);

        if ($element && $element->isVisible()) {
            return $element;
        }

        throw new ElementNotFoundException($this->session, 'element', 'css', $selector);
    }

    /**
     * Waits for the page to fully load.
     *
     * @param int $timeout The timeout in milliseconds.
     */
    public function waitForPageLoad($timeout = 5000)
    {
        $this->session->wait($timeout, "document.readyState === 'complete'");
    }
}