<?php

use App\Controller\Main;
use App\Router\Router;

require dirname(__DIR__).'/vendor/autoload.php';

ob_start();

set_exception_handler(function (Throwable $exception) {
    http_response_code($exception->getCode());
    echo 'Error ' . $exception->getMessage();
});

$main = new Main();
$controllers[Main::class] = $main;

$router = new Router($controllers);
$controller = $router->findController();

/**
 * @see Main
 */
$response = call_user_func($controller);

if ($response instanceof JsonSerializable) {
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    echo $response;
}
