<?php


namespace UrlShortener\Controllers;


use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use UrlShortener\Dal\UrlShortenerDao;
use \Mobile_Detect;
use UrlShortener\Models\UrlShortener;

class UrlRedirectorController
{
    /**
     * @var
     */
    private $store;
    private $deviceDetector;
    const ALLOWED_TRIALS = 5;

    /**
     * UrlDirectorController constructor.
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->store = $ci->get(UrlShortenerDao::class);
        $this->deviceDetector = $ci->get(Mobile_Detect::class);
    }

    public function forward(Request $request, Response $response, array $args) :Response {
        $e = $this->store->findByShortenedUrl($args["shortenedUrl"]);
        $target = trim(strtolower($this->getTarget($e)));
        $target = substr($target, 0, 4) == "http" ? $target : "http://".$target;


        return $response->withRedirect($target, 303);

    }

    private function getTarget(UrlShortener $model): string {
        $devices = $model->getRedirects();
        if(empty($devices)){
            return $model->getId();
        }
        $t = $this->deviceDetector ;
        $target = null;

        if($t->isTablet()){
            $target = $this->searchTarget($devices, "tablet") ?:
                $this->searchTarget($devices, "mobile");
        }

        if(!$target && $t->isMobile()){
            $target = $this->searchTarget($devices, "mobile");
        }

        return $target ?: $model->getId();

    }

    private function searchTarget(array  $targets, string $type) :string {
        if(isset($targets[$type])){
            return $targets[$type];
        }
        foreach ($targets as $deviceType => $target){
            if(strpos(strtolower($deviceType), $type) != false ||
                $this->deviceDetector->is(strtolower($deviceType))
            ){
                return $target;
            }
        }

        return null;
    }
}