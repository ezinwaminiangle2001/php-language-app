<?php
/**
 * API Router
 */

namespace App;

class Router {
    private $routes = [];
    private $method;
    private $uri;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * Register GET route
     */
    public function get($pattern, $callback) {
        $this->addRoute('GET', $pattern, $callback);
    }

    /**
     * Register POST route
     */
    public function post($pattern, $callback) {
        $this->addRoute('POST', $pattern, $callback);
    }

    /**
     * Register PUT route
     */
    public function put($pattern, $callback) {
        $this->addRoute('PUT', $pattern, $callback);
    }

    /**
     * Register DELETE route
     */
    public function delete($pattern, $callback) {
        $this->addRoute('DELETE', $pattern, $callback);
    }

    /**
     * Add route to registry
     */
    private function addRoute($method, $pattern, $callback) {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback
        ];
    }

    /**
     * Match and execute route
     */
    public function dispatch() {
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route['pattern'], $route['method'])) {
                return call_user_func($route['callback']);
            }
        }
        
        // Route not found
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }

    /**
     * Check if route matches
     */
    private function matchRoute($pattern, $method) {
        if ($this->method !== $method) {
            return false;
        }

        $pattern = preg_replace('/{([a-zA-Z_][a-zA-Z0-9_]*)}/', '(?P<$1>[^/]+)', $pattern);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '^' . $pattern . '$';

        return preg_match('/' . $pattern . '/', $this->uri, $matches) === 1;
    }

    /**
     * Get URI
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * Get method
     */
    public function getMethod() {
        return $this->method;
    }
}
