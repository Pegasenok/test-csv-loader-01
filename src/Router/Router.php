<?php


namespace App\Router;


use App\Controller\MainController;

class Router
{
    private array $controllers;
    private array $routes = [
        '' => [MainController::class, 'index'], /** @see MainController::index() */
        'upload' => [MainController::class, 'upload'], /** @see MainController::upload() */
        'status' => [MainController::class, 'status'], /** @see MainController::status() */
    ];

    /**
     * Router constructor.
     * @param array $controllers
     */
    public function __construct(array $controllers)
    {
        $this->controllers = $controllers;
    }

    /**
     * @return callable
     * @throws \Exception
     */
    public function findController(): callable
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = trim($path, '/');

        return $this->getCallableController($path);
    }

    /**
     * @param string $path
     * @return array
     * @throws \Exception
     */
    public function getCallableController(string $path): array
    {
        $routeCallable = $this->routes[$path];
        $routeController = $routeCallable[0];
        $routeAction = $routeCallable[1];

        if (!isset($routeCallable)) {
            throw new \Exception('not found', 404);
        }

        if (!is_callable($routeCallable)) {
            throw new \Exception('bad action', 400);
        }

        if (!isset($this->controllers[$routeController])) {
            throw new \Exception('no routeCallable', 400);
        }

        return [$this->controllers[$routeController], $routeAction];
    }
}