<?php

namespace Todo;

class Application
{
    private $server;

    public function __construct(array $server)
    {
        $this->server = $server;
    }

    public function handle(array $request)
    {
        $controller = $this->getController();

        return $controller($request);
    }

    private function getController()
    {
        $routes = require(BASE_PATH . '/app/routes.php');

        $path = $this->server['PATH_INFO'] ?? '/';
        $method = strtolower($this->server['REQUEST_METHOD']);

        if (! isset($routes[$path][$method])) {
            throw new \Exception(sprintf('Route [%s]%s is not available', $method, $path), 1);
        }

        return new $routes[$path][$method];
    }
}
