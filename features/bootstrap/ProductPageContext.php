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
     * Initializes context.
     */
    public function __construct()
    {
        $this->productPage = new ProductPage($this->getSession());
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
}