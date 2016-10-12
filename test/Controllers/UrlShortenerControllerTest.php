<?php


namespace UrlShortener\Test\Controllers;


use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use UrlShortener\Controllers\UrlShortenerController;
use UrlShortener\Dal\UrlShortenerDao;

use UrlShortener\Exceptions\NotFoundException;
use UrlShortener\Models\UrlShortener;
use PHPUnit\Framework\TestCase;
use UrlShortener\Test\Fixtures\UrlShortenedEntityProvider;

class UrlShortenerControllerTest extends TestCase
{

    private $dataProvider;
    private $container;
    private $store;
    private $request;
    private $response;
    protected function setUp()
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->store = $this->createMock(UrlShortenerDao::class);
        $this->dataProvider = new UrlShortenedEntityProvider();
        $this->response = new Response();
        $this->request = $this->createMock(Request::class);
    }

    protected function UrlShortenedEntityProvider(string $sUrl = "random", array $devices =[
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

    public function testDeleteReturnsStatus204WhenShortenedUrlIsFound() {

        $model = $this->dataProvider->provideEntity();
        $this->store
            ->method("delete")
            ->with($model->getId())
            ->willReturn(1);

        $this->container->expects($this->once())
            ->method('get')
             ->willReturn($this->store);

        $controller = new UrlShortenerController($this->container);
        $args = ["originalUrl" => $model->getId()];

        $response = $controller->delete($this->request, $this->response, $args);

        $this->assertEquals($response->getStatusCode(), 204);
    }

    public function testDeleteReturnsStatus404WhenShortenedUrlIsNotFound() {

        $model = $this->dataProvider->provideEntity();
        $this->store
            ->method("delete")
            ->with($model->getId())
            ->willReturn(0);

        $this->container->expects($this->once())
            ->method('get')
            ->willReturn($this->store);

        $controller = new UrlShortenerController($this->container);
        $args = ["originalUrl" => $model->getId()];

        $response = $controller->delete($this->request, $this->response, $args);

        $this->assertEquals($response->getStatusCode(), 404);
    }


    public function testFindReturnsStatus200WhenShortenedUrlIsFound() {

        $model = $this->dataProvider->provideEntity();
        $this->store
            ->method("findById")
            ->with($model->getId())
            ->willReturn($model);

        $this->container->expects($this->once())
            ->method('get')
            ->willReturn($this->store);

        $controller = new UrlShortenerController($this->container);
        $args = ["originalUrl" => $model->getId()];

        $response = $controller->find($this->request, $this->response, $args);

        $this->assertEquals($response->getStatusCode(), 200);
    }

    /**
     * @expectedException \UrlShortener\Exceptions\NotFoundException
     */
    public function testFindThrowsNotFoundExceptionWhenShortenedUrlIsNotFound() {

        $model = $this->dataProvider->provideEntity();
        $this->store
            ->method("findById")
            ->with($model->getId())
            ->will($this->throwException(new NotFoundException));

        $this->container->expects($this->once())
            ->method('get')
            ->willReturn($this->store);

        $controller = new UrlShortenerController($this->container);
        $args = ["originalUrl" => $model->getId()];

        $controller->find($this->request, $this->response, $args);

    }


    public function testFindAllReturnsAllRecords() {

        $model = $this->dataProvider->provideEntity();
        $this->store
            ->method("findAll")
            ->willReturn([$model]);

        $this->container->expects($this->once())
            ->method('get')
            ->willReturn($this->store);

        $controller = new UrlShortenerController($this->container);
        $args = ["originalUrl" => $model->getId()];

        $response = $controller->find($this->request, $this->response, $args);

        $this->assertEquals($response->getStatusCode(), 200);
    }

}