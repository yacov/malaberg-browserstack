<?php
namespace Features\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\RawMinkContext;

class BrowserStackContext extends RawMinkContext implements Context
{
    /**
     * @BeforeScenario
     */
    public function setupBrowserStack(BeforeScenarioScope $scope): void
    {
        $session = $this->getSession();
        
        if (!$session->isStarted()) {
            $session->start();
        }

        $driver = $session->getDriver();
        
        if (method_exists($driver, 'getWebDriverSession')) {
            $webDriver = $driver->getWebDriverSession();
            
            // Set test name in BrowserStack using capabilities
            $scenarioTitle = $scope->getScenario()->getTitle();
            $capabilities = $webDriver->getCapabilities();
            $capabilities->setCapability('name', $scenarioTitle);
        }
    }

    /**
     * @AfterScenario
     */
    public function tearDownBrowserStack(AfterScenarioScope $scope): void
    {
        $session = $this->getSession();
        $driver = $session->getDriver();

        if (method_exists($driver, 'getWebDriverSession')) {
            $webDriver = $driver->getWebDriverSession();
            
            // Set test status in BrowserStack
            $status = $scope->getTestResult()->isPassed() ? 'passed' : 'failed';
            $reason = $scope->getTestResult()->isPassed() ? '' : $scope->getTestResult()->getMessage();
            
            $capabilities = $webDriver->getCapabilities();
            $capabilities->setCapability('browserstack.status', $status);
            $capabilities->setCapability('browserstack.reason', $reason);
        }

        $session->stop();
    }

    private function updateTestStatus($status, $reason = ''): void
    {
        $sessionId = $this->getSession()->getDriver()->getWebDriverSession()->getSessionId();
        $username = $this->getMinkParameter('browser_stack')['username'];
        $accessKey = $this->getMinkParameter('browser_stack')['access_key'];

        $url = "https://$username:$accessKey@api.browserstack.com/automate/sessions/$sessionId.json";
        $data = [
            'status' => $status,
            'reason' => $reason
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $result = curl_exec($ch);
        curl_close($ch);
    }
}
