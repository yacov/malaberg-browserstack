behat-browserstack
=========

This repository provides information and helpful tweaks to run your Rspec Behat on the BrowserStack selenium cloud infrastructure.

###Setup
- Install [Composer](http://getcomposer.org/doc/00-intro.md)
- Run `composer install` to install the dependencies. 

###Configuration
- Change USERNAME, BROWSERSTACK_KEY, BROWSER_NAME, BROWSER_VERSION, OS, OS_VERSION in features/bootstrap/FeatureContext.php

###Run tests
- To run this sample test please run following command in root folder of this repository:
```
./bin/behat --config=behat.yml
```

###Further Reading
- [Behat](http://docs.behat.org/en/v3.0/)
- [BrowserStack documentation for Automate](https://www.browserstack.com/automate/php)
