<?php
/**
 * Lightweight URL Router
 * 
 * Maps URL patterns to controller actions with support for
 * dynamic segments (e.g., /jobs/:slug).
 */
class Router
{
    private array $routes = [];
    private string $basePath;

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * Register a GET route
     */
    public function get(string $pattern, callable|array $handler): self
    {
        return $this->addRoute('GET', $pattern, $handler);
    }

    /**
     * Register a POST route
     */
    public function post(string $pattern, callable|array $handler): self
    {
        return $this->addRoute('POST', $pattern, $handler);
    }

    /**
     * Register a route for any method
     */
    public function any(string $pattern, callable|array $handler): self
    {
        $this->addRoute('GET', $pattern, $handler);
        $this->addRoute('POST', $pattern, $handler);
        return $this;
    }

    /**
     * Add a route to the routing table
     */
    private function addRoute(string $method, string $pattern, callable|array $handler): self
    {
        // Convert :param patterns to regex
        $regex = preg_replace('/\:([a-zA-Z_]+)/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        $this->routes[] = [
            'method'  => $method,
            'pattern' => $pattern,
            'regex'   => $regex,
            'handler' => $handler,
        ];

        return $this;
    }

    /**
     * Dispatch the current request to a matching route
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getUri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['regex'], $uri, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Call the handler
                $handler = $route['handler'];

                if (is_array($handler)) {
                    [$controllerClass, $action] = $handler;
                    $controller = new $controllerClass();
                    call_user_func_array([$controller, $action], $params);
                } else {
                    call_user_func_array($handler, $params);
                }

                return;
            }
        }

        // No route matched — 404
        $this->handleNotFound();
    }

    /**
     * Get the clean URI path
     */
    private function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        // Remove base path
        if ($this->basePath && str_starts_with($uri, $this->basePath)) {
            $uri = substr($uri, strlen($this->basePath));
        }

        $uri = '/' . trim($uri, '/');

        return $uri === '' ? '/' : $uri;
    }

    /**
     * Handle 404 Not Found
     */
    private function handleNotFound(): void
    {
        http_response_code(404);
        $lang = Lang::getInstance();
        include VIEWS_PATH . '/public/404.php';
        exit;
    }
}
