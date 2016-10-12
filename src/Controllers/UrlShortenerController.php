<?php


namespace UrlShortener\Controllers;


use Interop\Container\ContainerInterface;
use UrlShortener\Exceptions\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use UrlShortener\Dal\UrlShortenerDao;
use UrlShortener\Models\UrlShortener;
use  \UrlShortener\key;
use DateTime;

class UrlShortenerController
{

    /**
     * UrlShortenerController constructor.
     */
    private $store;
    const ALLOWED_TRIALS = 5;
    public function __construct(ContainerInterface $ci)
    {
        $this->store = $ci->get(UrlShortenerDao::class);
    }

    private function create(UrlShortener $model, int $trials = 0, int $keyLength = 6) :int {
        try {
            return $this->store->create($model);
        } catch (\Exception $e){
            if($trials < self::ALLOWED_TRIALS){
                $trials += 1;
                $keyLength += 1;
                $model->setShortenedUrl(\UrlShortener\key($keyLength));
                return $this->create($model, $trials, $keyLength);
            }
            throw $e;
        }
    }

    public function save(Request $request, Response $response, array $args) :Response {

        $body = $request->getParsedBody();
        $devicesJson = $body && !empty($body["devices"]) ? json_encode($body["devices"]) : "{}";
         try {
             $e = $this->store->findById($args["originalUrl"]);

             $e->setRedirectsJson($devicesJson);
             $this->store->update($e);
             return $response->withStatus(204);
         } catch (NotFoundException $exe){
             $model = new UrlShortener(
                 $createdAt = new DateTime(),
                 $updatedAt = new DateTime(),
                 $shortenedUrl =  \UrlShortener\key(),
                 $id = $args["originalUrl"],
                 $redirectsJson = $devicesJson,
                 $baseUrl = ""
             );
             $this->create($model);
             return $response->withStatus(201);
         }
    }


    public function find(Request $request, Response $response, array $args) :Response {
        $e = $this->store->findById($args["originalUrl"]);

        $response->withJson($e);
        return $response;
    }

    //Pagination will be nice
    public function findAll(Request $request, Response $response) :Response {
        $e = $this->store->findAll();
        $response->withJson($e);
        return $response;
    }

    public function delete(Request $request, Response $response, array $args) :Response {
        $numberDeleted = $this->store->delete($args["originalUrl"]);
        return $response->withStatus( $numberDeleted ? 204 : 404);
    }
}
