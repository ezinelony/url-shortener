<?php

use \UrlShortener\Dal\UrlShortenerDao;
use \UrlShortener\Dal\Sqlite\RelationalUrlShortenerDao;
use \UrlShortener\Dal\Sqlite\SqlitePDO;
use \UrlShortener\Controllers\UrlShortenerController;
use Slim\Container;
use Slim\App;
use \Slim\Http\Request;
use \Slim\Http\Response;
use \UrlShortener\Exceptions\NotFoundException;
use \GuzzleHttp\ClientInterface;


defined('__DIR__') or define('__DIR__', dirname(__FILE__));
define('DOCUMENT_ROOT', __DIR__);

require DOCUMENT_ROOT.'/../vendor/autoload.php';

$ENV_DATABASE_FILE = getenv('ENV_DATABASE_FILE') ?: DOCUMENT_ROOT.'/../config/data/database.db';
$SERVICE_ADDRESS = (isset($_SERVER['HTTPS']) ? "https://":"http://").$_SERVER['HTTP_HOST'];


$container = new Container();

//ExceptionMapper
$container['errorHandler'] = function ($container) {
    return function (Request $request, Response $response, Exception $exception) use ($container) {
        if($exception instanceof  NotFoundException){
            return $response->withStatus(404)
                ->withJson(["errors" => [$exception->getMessage()]]);
        }
        if($exception instanceof  InvalidArgumentException){
            return $response->withStatus(400)
                ->withJson(["errors" => [$exception->getMessage()]]);
        }
        return $response->withStatus(500)
            ->withJson( ["errors" =>  [$exception->getMessage()]]);

    };
};

//DI
$container[UrlShortenerDao::class] =  function ($c) use($ENV_DATABASE_FILE, $SERVICE_ADDRESS) {
    return new RelationalUrlShortenerDao(new SqlitePDO($ENV_DATABASE_FILE), $SERVICE_ADDRESS);
};

$container[Mobile_Detect::class] =  function ($c) {
    return new Mobile_Detect;
};

$container[ClientInterface::class] =  function ($c) {
    return new \GuzzleHttp\Client([]);
};

$app = new App($container);

$app->add(function($request, $response, $next) {
    $response = $next($request, $response);
    return $response->withHeader('Content-Type', 'application/json;charset=utf-8');
});

$app->put('/api/urls/{originalUrl}', '\UrlShortener\Controllers\UrlShortenerController:save');
$app->get('/api/urls', '\UrlShortener\Controllers\UrlShortenerController:findAll');
$app->get('/api/urls/{originalUrl}', '\UrlShortener\Controllers\UrlShortenerController:find');
$app->delete('/api/urls/{originalUrl}', '\UrlShortener\Controllers\UrlShortenerController:delete');

$app->any('/{shortenedUrl}', '\UrlShortener\Controllers\UrlRedirectorController:forward');

$app->run();
