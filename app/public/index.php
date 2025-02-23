<?php
require_once __DIR__ . '/../src/config/bootstrap.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Minuz\SkolieAPI\controllers\AssayController;
use Minuz\SkolieAPI\controllers\EntranceController;
use Minuz\SkolieAPI\core\Core;
use Minuz\SkolieAPI\http\Router;


$router = new Router();
$router->registryControllersRoutes([
    EntranceController::class,
    AssayController::class
]);

Core::dispatch($router);
