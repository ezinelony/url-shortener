<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use GuzzleHttp\Client;
use \Behat\MinkExtension\Context\MinkContext ;
use   \Psr\Http\Message\RequestInterface;
use   \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\UriInterface;

require 'bootstrap.php';

class FeatureContext extends MinkContext // implements \Behat\MinkExtension\Context\MinkAwareContext
{

    public $response;
    public $client;
    private $baseUrl;

    /**
     * Hold Symfony kernel object.
     *
     * @var object Kernel Object.
     */
    protected $kernel;

    /**
     * Where the failure images will be saved.
     *
     * @var string Path to save failure screen-shots.
     */
    protected $screenShotPath;

    /**
     * Where the output files will be saved.
     *
     * @var string Path to save output files.
     */
    protected $outputPath;




    /**
     * Holds request payload.
     *
     * @var JSON object
     */
    private $requestPayload;


    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->client = new Client(["base_uri" =>$baseUrl]);
    }



    /**
     * @Then I should be redirected to :address
     */
    public function iShouldBeRedirectedTo(string $address)
    {
        $redirects  = $this->response->getHeaderLine('X-Guzzle-Redirect-History');
        if (strpos($redirects, $address) === false) {
            throw new Exception(sprintf(
                'Expected redirect to "%s" but got "%s".',
                $address,
                $redirects
            ));
        }

        return;
    }


    /**
     * @Given I have the JSON payload
     */
    public function iHaveTheJsonPayload(PyStringNode $requestPayload)
    {
        $this->requestPayload = $requestPayload;
    }


    /**
     * @Then /^The response body should be:$/
     *
     * @param PyStringNode $expectedResponseBody
     * @throws \Exception
     */
    public function theResponseBodyShouldBe(PyStringNode $expectedResponseBody)
    {
        $responseBody = trim($this->response->getBody(true));

        if ($responseBody != $expectedResponseBody) {
            throw new Exception(sprintf(
                'Expected response body was "%s" but got "%s".',
                $expectedResponseBody,
                $responseBody
            ));
        }

        return;
    }

    /**
     * @When I send a :arg1 request to :arg2 with userAgent :arg3
     */
    public function iSendARequestToWithUseragent($method, $uri, string $userAgent)
    {
        return $this->iSendARequestTo($method, $uri, $userAgent);
    }

    /**
     * @When /^I send a "([^"]*)" request to "([^"]*)"$/
     * @param $method
     * @param $uri
     * @param string|null $userAgent
     */
    public function iSendARequestTo($method, $uri, string $userAgent = null)
    {
        try {
            $this->response =  $this->client->request($method = $method, $uri = $uri,  $options = [
                'allow_redirects' => [
                    'max'             => 10,        // allow at most 10 redirects.
                    'referer'         => true,
                    'track_redirects' => true
                ],
                'headers' => $userAgent ? ["User-Agent" => $userAgent] : [],
                'json' => json_decode($this->requestPayload, true)
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->response = $e->getResponse();
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $this->response = $e->getResponse();
        }
    }

    /**
     * @Then /^The response code should be "([^"]*)"$/
     *
     * @param $responseCode
     * @throws \Exception
     */
    public function theResponseCodeShouldBe($responseCode)
    {
        if ($responseCode != $this->response->getStatusCode()) {
            throw new Exception(sprintf(
                'Expected response code was "%s" but got "%s".',
                $responseCode,
                $this->response->getStatusCode()
            ));
        }

        return;
    }

    /**
     * @Then /^The response content type should be "([^"]*)"$/
     *
     * @param $contentType
     * @throws \Exception
     */
    public function theResponseContentTypeShouldBe($contentType)
    {
        if (empty($this->response->getHeader("Content-Type")) || strtolower($this->response->getHeader("Content-Type")[0]) != strtolower($contentType)) {
            throw new Exception(sprintf(
                'Expected header Content-Type was "%s" but got "%s".',
                $contentType,
                implode(' ',$this->response->getHeader("Content-Type"))
            ));
        }

        return;
    }

    /**
     * @Then The response should equal json
     */
    public function theResponseShouldEqualJson(PyStringNode $value)
    {
        $response = json_decode($this->response->getBody(), true);

        if ($response != json_decode($value, true)) {
            throw new Exception(sprintf(
                'The response body "%s" \n does not equal json: "%s".',
                json_encode($response),
                $value
            ));
        }

        return;
    }

    /**
     * @Then The field :arg1 in response should equal json
     */
    public function theFieldInResponseShouldEqualJson($field, PyStringNode $value)
    {
        $response = json_decode($this->response->getBody(), true);

        if (! is_array($response)) {
            throw new Exception('The response is not an array.');
        }

        if (! isset($response[$field])) {
            throw new Exception(sprintf(
                'The response array does not equal json: "%s" key.',
                $field
            ));
        }

        if ($response[$field] != json_decode($value,true)) {
            throw new Exception(sprintf(
                'The field "%s" does not equal json: "%s".',
                $field,
                $value
            ));
        }

        return;
    }


    /**
     * @Then The field :arg1 should contain "{\:arg2: \:arg3}" in response
     *
     * @param $field
     * @param $value
     * @throws \Exception
     */
    public function theFieldShouldContainInResponse($field, $value)
    {
        $response = json_decode($this->response->getBody(), true);

        if (! is_array($response)) {
            throw new Exception('The response is not an array.');
        }

        if (! isset($response[$field])) {
            throw new Exception(sprintf(
                'The response array does not contain "%s" key.',
                $field
            ));
        }

        if ($response[$field] != $value) {
            throw new Exception(sprintf(
                'The field "%s" does not contain "%s".',
                $field,
                $value
            ));
        }

        return;
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario()
    {
        tearDown();
        seed();
    }

    /**
     * @AfterScenario
     */
    public function afterScenario()
    {
       tearDown();
    }

}
