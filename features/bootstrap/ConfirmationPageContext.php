<?php

namespace Features\Bootstrap;

use Behat\MinkExtension\Context\MinkContext;
use PHPUnit\Framework\Assert;

class ConfirmationPageContext extends MinkContext
{
    private ConfirmationPage $confirmationPage;
    private SharedDataContext $sharedData;

    public function __construct(SharedDataContext $sharedData)
    {
        $this->confirmationPage = new ConfirmationPage($this->getSession());
        $this->sharedData = $sharedData;
    }

    /**
     * @Then I memorize the order number
     */
    public function iMemorizeTheOrderNumber(): void
    {
        $orderNumber = $this->confirmationPage->getOrderNumber();
        $this->sharedData->set('orderNumber', $orderNumber);
    }

    /**
     * @Then I verify the order details are correct
     */
    public function iVerifyTheOrderDetailsAreCorrect(): void
    {
        $expectedData = $this->sharedData->getAll();
        $this->confirmationPage->verifyOrderDetails($expectedData);
    }
}