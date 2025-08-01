<?php

namespace App\Core;

use Exception;

/**
 * Kernel
 * 
 * The application kernel handles HTTP requests and responses
 */
class Kernel
{
    /**
     * @var array
     */
    private array $routes = [];
    
    /**
     * @var array
     */
    private array $middleware = [];
    
    /**
     * @var array
     */
    private array $errorHandlers = [];
    
    /**
     * @var string|null
     */
    private ?string $currentRoute = null;
    
    /**
     * @var array
     */
    private array $routeParams = [];
    
    /**
     * Constructor
     */
    public function __construct()
    {
        // Register default error handlers
        $this->registerErrorHandler(404, function() {
            header('HTTP/1.1 404 Not Found');
            echo '404 Not Found';
        });
        
        $this->registerErrorHandler(500, function() {
            header('HTTP/1.1 500 Internal Server Error');
            echo '500 Internal Server Error';
        });
    }
    
    /**
     * Register a route
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $path URL path
     * @param callable $handler Route handler
     * @param array $middleware Middleware to apply to this route
     * @return self
     */
    public function route(string $method, string $path, callable $handler, array $middleware = []): self
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
        
        return $this;
    }
    
    /**
     * Register a GET route
     * 
     * @param string $path URL path
     * @param callable $handler Route handler
     * @param array $middleware Middleware to apply to this route
     * @return self
     */
    public function get(string $path, callable $handler, array $middleware = []): self
    {
        return $this->route('GET', $path, $handler, $middleware);
    }
    
    /**
     * Register a POST route
     * 
     * @param string $path URL path
     * @param callable $handler Route handler
     * @param array $middleware Middleware to apply to this route
     * @return self
     */
    public function post(string $path, callable $handler, array $middleware = []): self
    {
        return $this->route('POST', $path, $handler, $middleware);
    }
    
    /**
     * Register a PUT route
     * 
     * @param string $path URL path
     * @param callable $handler Route handler
     * @param array $middleware Middleware to apply to this route
     * @return self
     */
    public function put(string $path, callable $handler, array $middleware = []): self
    {
        return $this->route('PUT', $path, $handler, $middleware);
    }
    
    /**
     * Register a DELETE route
     * 
     * @param string $path URL path
     * @param callable $handler Route handler
     * @param array $middleware Middleware to apply to this route
     * @return self
     */
    public function delete(string $path, callable $handler, array $middleware = []): self
    {
        return $this->route('DELETE', $path, $handler, $middleware);
    }
    
    /**
     * Register middleware
     * 
     * @param callable $middleware Middleware function
     * @return self
     */
    public function middleware(callable $middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }
    
    /**
     * Register an error handler
     * 
     * @param int $code HTTP status code
     * @param callable $handler Error handler
     * @return self
     */
    public function registerErrorHandler(int $code, callable $handler): self
    {
        $this->errorHandlers[$code] = $handler;
        return $this;
    }
    
    /**
     * Handle an error
     * 
     * @param int $code HTTP status code
     * @return void
     */
    public function handleError(int $code): void
    {
        if (isset($this->errorHandlers[$code])) {
            call_user_func($this->errorHandlers[$code]);
        } else {
            // Default error handler
            header('HTTP/1.1 ' . $code);
            echo 'Error ' . $code;
        }
    }
    
    /**
     * Match a route
     * 
     * @param string $method HTTP method
     * @param string $path URL path
     * @return array|null Route data or null if no match
     */
    private function matchRoute(string $method, string $path): ?array
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            // Convert route parameters to regex pattern
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $path, $matches)) {
                // Extract route parameters
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }
                
                $this->routeParams = $params;
                $this->currentRoute = $route['path'];
                
                return $route;
            }
        }
        
        return null;
    }
    
    /**
     * Get the current route
     * 
     * @return string|null The current route path or null if no route is matched
     */
    public function getCurrentRoute(): ?string
    {
        return $this->currentRoute;
    }
    
    /**
     * Get route parameters
     * 
     * @return array Route parameters
     */
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }
    
    /**
     * Get a route parameter
     * 
     * @param string $name Parameter name
     * @param mixed $default Default value if parameter doesn't exist
     * @return mixed Parameter value
     */
    public function getRouteParam(string $name, $default = null)
    {
        return $this->routeParams[$name] ?? $default;
    }
    
    /**
     * Run the application
     * 
     * @return void
     */
    public function run(): void
    {
        // Get the request method and path
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Match the route
        $route = $this->matchRoute($method, $path);
        
        if ($route) {
            try {
                // Apply global middleware
                foreach ($this->middleware as $middleware) {
                    $result = call_user_func($middleware);
                    if ($result === false) {
                        $this->handleError(403);
                        return;
                    }
                }
                
                // Apply route-specific middleware
                foreach ($route['middleware'] as $middleware) {
                    $result = call_user_func($middleware);
                    if ($result === false) {
                        $this->handleError(403);
                        return;
                    }
                }
                
                // Call the route handler
                $response = call_user_func($route['handler'], $this->routeParams);
                
                // Output the response
                if (is_string($response)) {
                    echo $response;
                } elseif (is_array($response) || is_object($response)) {
                    header('Content-Type: application/json');
                    echo json_encode($response);
                }
            } catch (Exception $e) {
                // Handle exceptions
                $this->handleError(500);
                error_log($e->getMessage());
            }
        } else {
            // No route matched
            $this->handleError(404);
        }
    }
}