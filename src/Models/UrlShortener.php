<?php

namespace UrlShortener\Models;

use DateTime;
use InvalidArgumentException;
use JsonSerializable;

class UrlShortener  implements JsonSerializable
{
    /**
     * @param string $redirectsJson
     */
    public function setRedirectsJson(string $redirectsJson)
    {
        $this->validateRedirectTypes($redirectsJson);
        $this->redirectsJson = $redirectsJson;
    }

    /**
     * @param string $shortenedUrl
     */
    public function setShortenedUrl(string $shortenedUrl)
    {
        $this->shortenedUrl = $shortenedUrl;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }
    /**
     * @return string
     */
    public function getDeviceRedirectsJson(): string
    {
        return $this->redirectsJson;
    }

    private $redirectsJson;
    /**
     * @var string
     */
    private $shortenedUrl;
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * UrlShortener constructor.
     * @param DateTime $createdAt
     * @param DateTime $updatedAt
     * @param string $shortenedUrl
     * @param string $baseUrl
     * @param string $id
     * @param string $redirectsJson
     */
    public function __construct(
        DateTime $createdAt,
        DateTime $updatedAt,
        string $shortenedUrl,
        string $id,
        string $redirectsJson = "{}",
        string $baseUrl = ""
    )
    {
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->shortenedUrl = $shortenedUrl;
        $this->id = $id;
        $this->baseUrl = $baseUrl;
        $this->validateRedirectTypes($redirectsJson);
        $this->redirectsJson = $redirectsJson;

    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getCreatedAtAsString(): string
    {
        return $this->createdAt->format("c");
    }
    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function getUpdatedAtAsString(): string
    {
        return $this->updatedAt->format("c");
    }

    private function validateRedirectTypes(string $redirects): bool {
        $decoded = json_decode($redirects, true);

        if(json_last_error() || !is_array($decoded))
            throw new InvalidArgumentException("Only json is accepted ".json_last_error_msg());


        foreach ($decoded as $key => $target){
            if(!(is_string($key) && is_string($target))){
                throw new InvalidArgumentException(
                    " A flat json => {'devices':{'a': 'b'}} is required for devices"
                );
            }
        }


        return true;
    }
    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getShortenedUrl(): string
    {
        return $this->shortenedUrl;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * UrlDeviceRedirect[]
     */
    public function getRedirects() :array
    {
        return json_decode($this->redirectsJson, true);
    }

    function asArray() : array
    {
        return [
            "createdAt" => $this->getCreatedAtAsString(),
            "updatedAt" => $this->getUpdatedAtAsString(),
            "originalUrl" => $this->getId(),
            "url" => $this->getBaseUrl()."/".$this->getShortenedUrl(),
            "devices" => json_decode($this->redirectsJson)
        ];
    }

    public function jsonSerialize() :array
    {
        return $this->asArray();
    }
}