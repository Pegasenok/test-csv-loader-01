<?php

use App\Controller\MainController;
use App\Router\Router;

require dirname(__DIR__).'/vendor/autoload.php';

ob_start();

set_exception_handler(function (Throwable $exception) {
    http_response_code($exception->getCode());
    echo 'Error ' . $exception->getMessage();
});

$main = new MainController();
$main->setConfiguration(require_once getcwd() . '/../config/devConfiguration.php');
$controllers[MainController::class] = $main;

$router = new Router($controllers);
$controller = $router->findController();

/**
 * @see MainController
 */
try {
    $response = call_user_func($controller);
} catch (\App\Exception\NotFoundException $e) {
    echo "Not found.";
    http_response_code(404);
}

if ($response instanceof JsonSerializable) {
    if (ob_get_length() == 0) {
        header('Content-Type: application/json');
    }
    echo json_encode($response);
} else {
    echo $response;
}
