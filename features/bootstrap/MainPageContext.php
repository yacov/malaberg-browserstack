<?php

namespace Features\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Exception;
use Page\HomePage;
use Behat\Mink\Exception\ElementNotFoundException;

/**
 * Defines application features from the Main Page context.
 */
class MainPageContext extends MinkContext implements Context
{
    /**
     * @var HomePage
     */
    private HomePage $homePage;

    /**
     * @var SharedDataContext
     */
    private SharedDataContext $sharedData;

    /**
     * Initializes context.
     */
    public function __construct(SharedDataContext $sharedData)
    {
        $this->homePage = new HomePage($this->getSession());
        $this->sharedData = $sharedData;
    }

    /**
     * @Given user opens :pageName page
     * @throws Exception
     */
    public function userOpensPage($pageName): void
    {
        if (strtolower($pageName) === 'main') {
            $this->homePage->open();
        } else {
            throw new Exception("Unknown page: $pageName");
        }
    }

    /**
     * @Then title contains :title
     * @throws Exception
     */
    public function titleContains($title): void
    {
        $actualTitle = $this->getSession()->getPage()->find('css', 'title')->getText();
        if (!str_contains($actualTitle, $title)) {
            throw new Exception("Title '$actualTitle' does not contain '$title'");
        }
    }

    /**
     * @When user clicks on the SHOP NOW button
     * @throws ElementNotFoundException
     */
    public function userClicksOnTheShopNowButton(): void
    {
        $this->homePage->clickShopNow();
    }

    /**
     * @Given I open the home page
     */
    public function iOpenTheHomePage(): void
    {
        $this->homePage->open();
    }

    /**
     * @Given I memorize the page title
     */
    public function iMemorizeThePageTitle(): void
    {
        $title = $this->homePage->getPageTitle();
        $this->sharedData->set('pageTitle', $title);
    }
}