<?php

namespace Minuz\SkolieAPI\http;

use Minuz\SkolieAPI\attributes\Route;
use Minuz\SkolieAPI\exceptions\RouteNotFound;


class Router
{
    private array $routes = [];

    public function registryControllersRoutes(array $controllers)
    {
        foreach ($controllers as $controller) {
            $reflectionController = new \ReflectionClass($controller);

            foreach ($reflectionController->getMethods() as $method) {
                $attributes = $method->getAttributes(Route::class, \ReflectionAttribute::IS_INSTANCEOF);

                foreach ($attributes as $attribute) {
                    $route = $attribute->newInstance();

                    $this->register($route->method, $route->path, [$controller, $method->getName()]);
                }
            }
        }
    }



    private function register(string $requestMethod, string $path, array $action)
    {
        $this->routes[$requestMethod][$path] = $action;
    }




    public function routes(): array
    {
        return $this->routes;
    }


    public function resolve(string $path, string $method): array
    {
        $controllerAction = $this->routes[$method][$path] ?? false;
        if (! $controllerAction) {
            throw new RouteNotFound;
        }

        return $controllerAction;
    }
}
