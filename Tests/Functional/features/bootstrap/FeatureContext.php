<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';
//
include __DIR__ . '/../../lib/SimpleGalleryClient.php';
/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    private $simpleGalleryClient;

    private $imageUrl;
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->simpleGalleryClient = new SimpleGalleryClient('http://localhost/test/api');
    }

    /**
     * @Given /^an image\'s url$/
     */
    public function anImageSUrl()
    {
        $this->imageUrl = 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c3/KL_Sentral_at_Night.jpg/800px-KL_Sentral_at_Night.jpg?' . time();
    }

    /**
     * @When /^call the api to add an image$/
     */
    public function callTheApiToAddAnImage()
    {
        $this->result = $this->simpleGalleryClient->addFavourite($this->imageUrl);
    }

    /**
     * @Then /^I should get a valid JSON with a response true$/
     */
    public function iShouldGetAValidJsonWithAResponseTrue()
    {
        assertTrue(is_array($this->result));
        assertTrue($this->result['response']);
    }

    /**
     * @Then /^I should get a valid JSON with a response false$/
     */
    public function iShouldGetAValidJsonWithAResponseFalse()
    {
        assertTrue(is_array($this->result));
        assertFalse($this->result['response']);
    }

    /**
     * @Given /^a wrong image\'s url$/
     */
    public function aWrongImageSUrl()
    {
        $this->imageUrl = 'wrongUrl';
    }

    /**
     * @Given /^a message saying "([^"]*)"$/
     */
    public function aMessageSaying($text)
    {
        assertEquals($this->result['message'], $text);
    }

    /**
     * @When /^call the api to add an image without an url$/
     */
    public function callTheApiToAddAnImageWithoutAnUrl()
    {
        $this->result = $this->simpleGalleryClient->addFavourite();
    }

    /**
     * @Given /^i add an image\'s url$/
     */
    public function iAddAnImageSUrl()
    {
        $this->imageUrl = 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c3/KL_Sentral_at_Night.jpg/800px-KL_Sentral_at_Night.jpg?' . time();
        $this->result = $this->simpleGalleryClient->addFavourite($this->imageUrl);
    }

    /**
     * @Given /^i give the same image\'s url$/
     */
    public function iGiveTheSameImageSUrl()
    {
        $this->imageUrl = $this->imageUrl;
    }

    /**
     * @When /^call the api to add the image$/
     */
    public function callTheApiToAddTheImage()
    {
        $this->result = $this->simpleGalleryClient->addFavourite($this->imageUrl);
    }


    /**
     * @When /^I call \/favourites on the api$/
     * @Given /^I call \/Favourites on the api$/
     */
    public function iCallFavouritesOnTheApi()
    {
        $this->result = $this->simpleGalleryClient->getFavourites();

    }

    /**
     * @Then /^I should get an array in JSON$/
     */
    public function iShouldGetAnArrayInJson()
    {
        assertTrue(is_array($this->result));
    }

    /**
     * @Given /^it must contain an "([^"]*)", "([^"]*)" and "([^"]*)"$/
     */
    public function itMustContainAnIdAnd($id, $url, $description)
    {
        $data = $this->result[0];

        assertTrue(array_key_exists($id, $data));
        assertTrue(array_key_exists($url, $data));
        assertTrue(array_key_exists($description, $data));
    }


    /**
     * @When /^I call PUT \/favourite\/id on the api$/
     */
    public function iCallPutFavouriteIdOnTheApi()
    {
        $data = $this->result[0];
        $id = $data['id'];

        $this->result = $this->simpleGalleryClient->editFavourite($id, 'test');
    }

    /**
     * @When /^I call PUT \/favourite\/id on the api with a wrong id$/
     */
    public function iCallPutFavouriteIdOnTheApiWithAWrongId()
    {
        $this->result = $this->simpleGalleryClient->editFavourite('WrongId', 'test');
    }

    /**
     * @When /^I call DELETE \/favourite\/id on the api$/
     */
    public function iCallDeleteFavouriteIdOnTheApi()
    {
        $data = $this->result[0];
        $id = $data['id'];

        $this->result = $this->simpleGalleryClient->deleteFavourite($id);

    }

    /**
     * @When /^I call DELETE \/favourite\/id on the api with a wrong id$/
     */
    public function iCallDeleteFavouriteIdOnTheApiWithAWrongId()
    {
        $this->result = $this->simpleGalleryClient->deleteFavourite('WrongId');
    }

}
