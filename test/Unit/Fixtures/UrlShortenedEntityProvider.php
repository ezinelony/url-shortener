<?php


namespace UrlShortener\Test\Fixtures;


use UrlShortener\Models\UrlShortener;
use PHPUnit\Framework\TestCase;

class UrlShortenedEntityProvider extends TestCase
{
    public function provideEntity(string $sUrl = "random", array $devices =[
        "mobile" => "http://acm.com",
        "tablet" => "http://techcrunch.com"
    ]) : UrlShortener {

        return new UrlShortener(
            new \DateTime(),
            new \DateTime(),
            $sUrl,
            "http://test.com",
            json_encode($devices),
            "http://localhos:9999"
        );
    }
}