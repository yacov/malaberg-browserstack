<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;

class BrowserStackContext extends RawMinkContext implements Context
{
    /**
     * @BeforeScenario
     */
    public function setupBrowserStack()
    {
        $session = $this->getSession('browserstack');
        
        // Start the session if it's not already started
        if (!$session->isStarted()) {
            $session->start();
        }

        $driver = $session->getDriver();
        
        if (method_exists($driver, 'getWebDriverSession')) {
            $webDriver = $driver->getWebDriverSession();
        }
    }

    /**
     * @AfterScenario
     */
    public function tearDownBrowserStack()
    {
        $this->getSession('browserstack')->stop();
    }
}