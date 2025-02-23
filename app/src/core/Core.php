<?php

namespace Minuz\SkolieAPI\core;

use Minuz\SkolieAPI\exceptions\RouteNotFound;
use Minuz\SkolieAPI\http\Request;
use Minuz\SkolieAPI\http\Response;
use Minuz\SkolieAPI\http\Router;
use Minuz\SkolieAPI\tools\URLDecomposer;

class Core
{
    public static function dispatch(Router $router)
    {
        $prefixController = 'Minuz\\SkolieAPI\\controllers\\';

        $url = Request::path();
        URLDecomposer::Detach($url, $urlData);

        $route = $urlData['path'];

        if (Request::path() == '/') {
            Response::Response(200, 'Ok', 'Hello from BaseAPI!');
            return;
        }
        try {
            [$controllerClass, $action] = $router->resolve($route, Request::method());
        } catch (RouteNotFound) {
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
