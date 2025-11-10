<?php

class Router {
    private $routes = [];

    // Register a GET route
    public function get($path, $action) {
        $this->addRoute('GET', $path, $action);
    }

    // Register a POST route
    public function post($path, $action) {
        $this->addRoute('POST', $path, $action);
    }
    public function put($path, $action) {
        $this->addRoute('PUT', $path, $action);
    }

    public function delete($path, $action) {
        $this->addRoute('DELETE', $path, $action);
    }
    // Add route to the routes array with dynamic parameter support
    private function addRoute($method, $path, $action) {
        // Convert dynamic route placeholders (e.g., /users/{id}) to regex
        $pathRegex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_,]+)', $path);
        $pathRegex = str_replace('/', '\/', $pathRegex); // Escape slashes for regex
        $this->routes[$method][$pathRegex] = $action;
    }

    // Dispatch the request to the appropriate controller
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Get the requested path from the URL
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $baseFolder = '/quiz/app'; // Adjust this according to your project structure
        if (strpos($path, $baseFolder) === 0) {
            // Remove the base folder from the path
            $path = substr($path, strlen($baseFolder));
        }

        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        // Match the route based on the method and path
        foreach ($this->routes[$method] as $routePattern => $action) {
            // Check if the route matches the pattern
            if (preg_match('/^' . $routePattern . '$/', $path, $matches)) {
                // Extract parameters from the URL
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }

                // Handle controller and method call
                if (is_array($action)) {
                    list($controller, $method) = $action;
                    $controllerInstance = new $controller();
                    
                    // Get request data if any
                    $requestData = json_decode(file_get_contents('php://input'), true);

                    // Pass URL params and request data (in that order) to the controller method
                    $finalParams = array_values($params); // Ensure only values are passed
                    if ($requestData) {
                        $finalParams[] = $requestData; // Append request data if available
                    }

                    // Call the controller method with the params
                    call_user_func_array([$controllerInstance, $method], $finalParams);
                } else {
                    // If no controller, simply call the action
                    call_user_func($action);
                }
                return;
            }
        }

        // If no route matches, return a 404 response
        http_response_code(404);
        echo json_encode(["message" => "Route is eshioze routing not found Not Found"]);
    }
}
