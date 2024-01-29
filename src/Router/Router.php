<?php

namespace App\Router;

class Router
{
    private $url;
    private $routes = [];
    private $namedRoutes = [];
    private $basePath = '';

    public function __construct($url)
    {
        $this->url = trim($url, '/');
    }

    public function setBasePath($basePath)
    {
        $this->basePath = trim($basePath, '/');
    }

    public function get($path, $callable, $name = null)
    {
        return $this->add($path, $callable, $name, 'GET');
    }

    public function post($path, $callable, $name = null)
    {
        return $this->add($path, $callable, $name, 'POST');
    }

    private function add($path, $callable, $name, $method)
    {
        $path = $this->basePath . '/' . trim($path, '/');
        $route = new Route($path, $callable);
        $this->routes[$method][] = $route;

        if (is_string($callable) && $name === null) {
            $name = $callable;
        }

        if ($name) {
            $this->namedRoutes[$name] = $route;
        }

        return $route;
    }

    public function run()
    {
        if (!isset($this->routes[$_SERVER['REQUEST_METHOD']])) {
            throw new \Exception('REQUEST_METHOD does not exist');
        }

        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
            if ($route->match($this->url)) {
                return $route->call();
            }
        }

        header("HTTP/1.0 404 Not Found");
        echo '404 Not Found';
    }

    public function url($name, $params = [])
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \Exception('No route matches this name');
        }

        return $this->namedRoutes[$name]->getUrl($params);
    }
}
