<?php

namespace Minuz\BaseApi\Core;

use Minuz\BaseApi\exceptions\RouteNotFound;
use Minuz\BaseApi\http\Request;
use Minuz\BaseApi\http\Response;
use Minuz\BaseApi\http\Router;
use Minuz\BaseApi\tools\URLDecomposer;

class Core
{
    public static function dispatch(Router $router)
    {
        $prefixController = 'Minuz\\BaseApi\\controllers\\';

        $url = Request::path();
        URLDecomposer::Detach($url, $urlData);
        
        $route = $urlData['path'];
        
        if ( Request::path() == '/' ) {
            Response::Response(200, 'Ok', 'Hello from BaseAPI!');
            return;
        }
        try {
            [$controllerClass, $action] = $router->resolve($route, Request::method());
        }
        catch (RouteNotFound) {
            $controllerClass = $prefixController . 'WrongRequestController';
            $controller = new $controllerClass();
            $controller->index(new Request, new Response);
        
            return;
        }
        
        $controller = new $controllerClass();

        $controller->$action(new Request, new Response, $urlData['id'], $urlData['query']);

        return;

    }
}