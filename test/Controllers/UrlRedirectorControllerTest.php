<?php


namespace UrlShortener\Test\Controllers;


use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use UrlShortener\Controllers\UrlRedirectorController;
use UrlShortener\Dal\UrlShortenerDao;
use \Mobile_Detect;
use UrlShortener\Exceptions\NotFoundException;
use UrlShortener\Test\Fixtures\UrlShortenedEntityProvider;
use PHPUnit\Framework\TestCase;

class UrlRedirectorControllerTest extends TestCase
{

    private $dataProvider;
    private $container;
    private $deviceDetector;
    private $store;
    private $request;
    private $response;
    protected function setUp()
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->store = $this->createMock(UrlShortenerDao::class);
        $this->deviceDetector = $this->createMock(Mobile_Detect::class);
        $this->dataProvider = new UrlShortenedEntityProvider();
        $this->response = new Response();
        $this->request = $this->createMock(Request::class);
    }

    public function testForwardRedirectsToTargetUrlWhenShortenedUrlIsFound() {

        $model = $this->dataProvider->provideEntity();
        $this->store
            ->method("findByShortenedUrl")
            ->with("random")
            ->willReturn($model);

        $map = [
            [Mobile_Detect::class, $this->deviceDetector],
            [UrlShortenerDao::class,  $this->store]
        ];

        $this->container->expects($this->exactly(2))
            ->method('get')
             ->will($this->returnValueMap($map));

        $controller = new UrlRedirectorController($this->container);

        $this->request
            ->method("getHeader")
            ->with("User-Agent")
            ->willReturn([
                'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36'
            ]);
        $args = ["shortenedUrl" => "random"];

        $response = $controller->forward($this->request, $this->response, $args);

        $this->assertEquals(
            $response->getHeader("Location")[0],
            $model->getId()
        );
    }

    /**
     * @expectedException \UrlShortener\Exceptions\NotFoundException
     */
    public function testForwardThrowsNotFoundExceptionWhenShortenedUrlIsNotFound() {

        $model = $this->dataProvider->provideEntity();
        $this->store
            ->method("findByShortenedUrl")
            ->with("random")
            ->will($this->throwException(new NotFoundException));

        $map = [
            [Mobile_Detect::class, $this->deviceDetector],
            [UrlShortenerDao::class,  $this->store]
        ];

        $this->container->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap($map));

        $controller = new UrlRedirectorController($this->container);

        $this->request
            ->method("getHeader")
            ->with("User-Agent")
            ->willReturn([
                'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36'
            ]);
        $args = ["shortenedUrl" => "random"];

        $response = $controller->forward($this->request, $this->response, $args);

        $this->assertEquals(
            $response->getHeader("Location")[0],
            $model->getId()
        );
    }

    public function testForwardRedirectsToMobileTargetUrlWhenRequestIsMadeFromAMobile() {

        $devices =["mobile" => "http://acm.com", "tablet" => "http://techcrunch.com"];
        $model = $this->dataProvider->provideEntity("random", $devices);
        $this->store
            ->method("findByShortenedUrl")
            ->with("random")
            ->willReturn($model);

        $this->deviceDetector
            ->method("isMobile")
            ->willReturn(true);

        $map = [
            [Mobile_Detect::class, $this->deviceDetector],
            [UrlShortenerDao::class,  $this->store]
        ];

        $this->container->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap($map));

        $controller = new UrlRedirectorController($this->container);

        $this->request
            ->method("getHeader")
            ->with("User-Agent")
            ->willReturn([
                'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3'
            ]);
        $args = ["shortenedUrl" => "random"];

        $response = $controller->forward($this->request, $this->response, $args);

        $this->assertEquals(
            $response->getHeader("Location")[0],
            $devices["mobile"]
        );
    }


    public function testForwardRedirectsToTabletTargetUrlWhenRequestIsMadeFromATablet() {

        $devices =["mobile" => "http://acm.com", "tablet" => "http://techcrunch.com"];
        $model = $this->dataProvider->provideEntity("random", $devices);
        $this->store
            ->method("findByShortenedUrl")
            ->with("random")
            ->willReturn($model);

        $this->deviceDetector
            ->method("isTablet")
            ->willReturn(true);

        $map = [
            [Mobile_Detect::class, $this->deviceDetector],
            [UrlShortenerDao::class,  $this->store]
        ];

        $this->container->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap($map));

        $controller = new UrlRedirectorController($this->container);

        $this->request
            ->method("getHeader")
            ->with("User-Agent")
            ->willReturn([
                'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3'
            ]);
        $args = ["shortenedUrl" => "random"];

        $response = $controller->forward($this->request, $this->response, $args);

        $this->assertEquals(
            $response->getHeader("Location")[0],
            $devices["tablet"]
        );
    }
}