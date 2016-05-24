<?php

require "vendor/autoload.php";

use Behat\Behat\Context\BehatContext,
  Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use BrowserStack\Local;

class FeatureContext extends BehatContext {
  private $webDriver;
  private $BROWSER_NAME = 'firefox';
  private $OS = 'Windows';
  private $OS_VERSION = '7';
  private $BROWSER_VERSION = '23.0';
  private $USERNAME = 'BROWSERSTACK_USERNAME'; // Set your username
  private $BROWSERSTACK_ACCESS_KEY = 'BROWSERSTACK_ACCESS_KEY'; // Set your browserstack_access_key
  private $tunnel = 'false';
  private $bs_local;
  public function __construct(array $parameters){
    $this->tunnel = $parameters['tunnel'];
    if ($this->tunnel == "true") {
      $this->bs_local = new Local();
      $bs_local_args = array("key" => 'BS_USERNAME', "forcelocal" => true);
      $this->bs_local->start(bs_local_args);
    }
  }

  public function __destruct() {
    if ($this->tunnel == "true") {
      $this->bs_local->stop();
    }
  }

  /** @Given /^I am on "([^"]*)"$/ */
  public function iAmOnSite($url) {
    print("HHHHHHH >" . $this->tunnel);
    $desiredCap =  array('browser'=> $this->BROWSER_NAME, 'browser_version'=> $this->BROWSER_VERSION, 'os' => $this->OS, 'os_version' => $this->OS_VERSION, 'browserstack.tunnel' => $this->tunnel);
    $this->webDriver = RemoteWebDriver::create("http://".$this->USERNAME.":".$this->BROWSERSTACK_ACCESS_KEY."@hub.browserstack.com/wd/hub", $desiredCap);
    $this->webDriver->get($url);
  }

  /** @When /^I search for "([^"]*)"$/ */
  public function iSearchFor($searchText) {
    $element = $this->webDriver->findElement(WebDriverBy::name("q"));
    if ($element) {
      $element->sendKeys($searchText);
      $element->submit();
    }
  }

  /** @Then /^I get title as "([^"]*)"$/ */
  public function iShouldGet($string) {
    $title = $this->webDriver->getTitle();
    if ((string)  $string !== $title) {
      throw new Exception("Actual output is:\n". $title);
    }
    $this->webDriver->quit();
  }
}
