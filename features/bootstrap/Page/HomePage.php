<?php

namespace Page;

/**
 * HomePage handles actions on the main landing page.
 */
class HomePage extends BasePage
{
    /**
     * Navigates to the main page.
     */
    public function open()
    {
        $this->session->visit('/');
        $this->waitForPageLoad();
    }

    /**
     * Clicks the "Shop Now" button.
     */
    public function clickShopNow()
    {
        $this->clickButton('Shop Now');
    }
}
