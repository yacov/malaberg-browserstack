<?php

namespace Features\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Page\ProductPage;
use Behat\Mink\Exception\ElementNotFoundException;

/**
 * Defines application features from the Product Page context.
 */
class ProductPageContext extends MinkContext implements Context
{
    /**
     * @var ProductPage
     */
    private ProductPage $productPage;

    /**
     * @var SharedDataContext
     */
    private SharedDataContext $sharedData;

    /**
     * Initializes context.
     */
    public function __construct(SharedDataContext $sharedData)
    {
        $this->productPage = new ProductPage($this->getSession());
        $this->sharedData = $sharedData;
    }

    /**
     * @Given user is on the product page
     */
    public function userIsOnProductPage(): void
    {
        $this->productPage->load();
    }

    /**
     * @When user subscribes to product
     * @throws ElementNotFoundException
     */
    public function userSubscribesToProduct(): void
    {
        $this->productPage->clickToSubscribe();
    }

    /**
     * @When user adds the product to the cart
     * @throws ElementNotFoundException
     */
    public function userAddsTheProductToTheCart(): void
    {
        $this->productPage->addToCart();
    }

    /**
     * @When user adds the product :productName to the cart
     * @throws ElementNotFoundException
     */
    public function userAddsSpecificProductToTheCart($productName): void
    {
        $this->productPage->load();
        $this->productPage->selectProductByName($productName);
        $this->productPage->addToCart();
    }

    /**
     * @When user adds default product to the cart
     * @throws ElementNotFoundException
     */
    public function userAddsDefaultProductToTheCart(): void
    {
        $this->productPage->load();
        $this->productPage->addToCart();
    }

    /**
     * @When I navigate to the :productName product page
     */
    public function iNavigateToTheProductPage($productName): void
    {
        $this->productPage->navigateToProduct($productName);
    }

    /**
     * @When I select :option for the product
     */
    public function iSelectForTheProduct($option): void
    {
        $this->productPage->selectPurchaseOption($option);
    }

    /**
     * @When I set the quantity to :quantity
     */
    public function iSetTheQuantityTo($quantity): void
    {
        $this->productPage->setQuantity($quantity);
    }

    /**
     * @When I memorize the product details
     */
    public function iMemorizeTheProductDetails(): void
    {
        $productName = $this->productPage->getProductName();
        $purchaseType = $this->productPage->getSelectedPurchaseOption();
        $quantity = $this->productPage->getQuantity();

        $this->sharedData->setMultiple([
            'productName' => $productName,
            'purchaseType' => $purchaseType,
            'quantity' => $quantity,
        ]);
    }

    /**
     * @When I add the product to the cart
     */
    public function iAddTheProductToTheCart(): void
    {
        $this->productPage->clickAddToCart();
    }

    /**
     * @When I proceed to the shopping cart page
     */
    public function iProceedToTheShoppingCartPage(): void
    {
        $this->productPage->goToCart();
    }
}