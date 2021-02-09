<?php

namespace Todo;

use Todo\Controllers\Error;

class Application
{
    /**
     * Handle application request
     *
     * @param array $server
     * @param array $request
     * @return mixed
     */
    public function handle(array $server, array $request)
    {
        if ($server['REQUEST_METHOD'] === 'PUT') {
            $request += json_decode(file_get_contents('php://input'), true);
        }

        [$controller, $router] = $this->getController($server);

        $request['Router'] = $router;

        try {
            return $controller($request);
        } catch (\Throwable $th) {
            $error = new Error;

            return $error($th);
        }
    }

    /**
     * Search routes for controller
     *
     * @param array $server
     * @return array
     */
    private function getController($server)
    {
        $routes = require(BASE_PATH . '/app/routes.php');

        $path = $server['PATH_INFO'] ?? '/';
        $method = strtolower($server['REQUEST_METHOD']);

        foreach ($routes as $route => $target) {
            if (isset($target[$method]) && preg_match("/^\\{$route}$/", $path, $matches)) {
                return [new $target[$method], $matches];
            }
        }

        if (! isset($routes[$path][$method])) {
            throw new \Exception(sprintf('Route [%s]%s is not available', $method, $path), 1);
        }
    }
}
