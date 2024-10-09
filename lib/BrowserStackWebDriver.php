<?php

use Behat\Mink\Driver\Selenium2Driver;

class BrowserStackWebDriver extends Selenium2Driver
{
    public function __construct($browserName = 'safari', $desiredCapabilities = null, $wdHost = null)
    {
        if ($desiredCapabilities === null) {
            $desiredCapabilities = [];
        }

        $browserStackConfig = require __DIR__ . '/../browserstack_config.php';
        $desiredCapabilities = array_merge($desiredCapabilities, $browserStackConfig);

        parent::__construct($browserName, $desiredCapabilities, $wdHost);
    }
}