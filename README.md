# behat-browserstack

[Behat](https://github.com/Behat/Behat) Integration with BrowserStack.

## Setup

- Clone the repo
- Install dependencies `composer install`
- Update `*.conf.yml` files inside the `config/` directory with your BrowserStack Username and Access Key. (These can be found in the [settings](https://www.browserstack.com/accounts/settings) section on BrowserStack accounts page)
- Alternatively, you can export the environment variables for the Username and Access Key of your BrowserStack account. `export BROWSERSTACK_USERNAME=<browserstack-username> && export BROWSERSTACK_ACCESS_KEY=<browserstack-access-key>`

### Run the tests

- To run single test, run `composer single`
- To run parallel tests, run `composer parallel`
- To run local tests, run `composer local`

### Notes

- In order to test on different set of browsers, check out our [code generator](https://www.browserstack.com/automate/python#setting-os-and-browser)
