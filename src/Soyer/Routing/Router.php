<?php

    /**
     * Copyright 2023 mantvmass
     * 
     * 
     */
    

    namespace Soyer\Routing;

    use Soyer\Routing\Middleware\ActiveMiddleware;
    use ReflectionFunction;
    use Exception;
    use Closure;



    /**
     * This is class main router
     * 
     */
    class Router {


        /**
         * This route from application
         * 
         * @var array|null
         */
        private static $routes = [];


        /**
         * This errorHandlers from application
         * 
         * @var array|null
         */
        private static $errorHandlers = [];


        /**
         * Method for checking for duplicate paths
         * 
         * @param string $path
         * @param string $method
         */
        private static function checkDuplicateRoute(string $path, string $method) {
            foreach (self::$routes as $route) {
                if ($route["path"] == $path && in_array($method, $route["methods"])) {
                    throw new Exception("Duplicate route: $path [$method]");
                }
            }
        }


        /**
         * Add route function
         * 
         * @param string  $path
         * @param array   $method
         * @param Closure $handler
         */
        public static function route(string $path, array $methods, Closure $handler, array $middlewares = []) {

            foreach ($methods as $method) {
                // Call the method to check for duplicate paths
                self::checkDuplicateRoute($path, $method);
            }

            $route = [
                'path' => $path,
                'methods' => $methods,
                'handler' => $handler,
                'middlewares' => $middlewares
            ];

            // Convert route path to regular expression
            $route['regex'] = self::convertToRegex($path);

            // Add route
            self::$routes[] = $route;
        }


        /**
         * Add errorHandler function
         * 
         * @param int $statusCode
         * @param callable $handler
         */
        public static function errorHandler(int $statusCode, callable $handler) {
            self::$errorHandlers[$statusCode] = $handler;
        }


        /**
         * handleException function
         * 
         * @param int $statusCode
         * @param string $message
         */
        public static function handleException(int $statusCode, string $message) {
            if (isset(self::$errorHandlers[$statusCode])) {
                $handler = self::$errorHandlers[$statusCode];
                http_response_code($statusCode);
                $handler(new Exception($message));
            } else {
                http_response_code($statusCode);
                echo "Error $statusCode: $message";
            }
            return;
        }


        /**
         * convertToRegex function
         * 
         * @param string $path
         */
        private static function convertToRegex(string $path) {
            // Convert route path to regular expression
            $regex = preg_replace('/\<(\w+)\>/', '(?P<$1>[^\/]+)', $path);
            $regex = '#^' . str_replace('/', '\/', $regex) . '$#';
            return $regex;
        }


        /**
         * getParams function | get params from router: /hi/<name>
         * 
         * @param array $params_from_route
         * @param callable $function
         */
        private static function getParams(array $params_from_route, callable $function) {
            $reflection = new ReflectionFunction($function);
            $params = $reflection->getParameters();
            $resolvedParams = [];
    
            foreach ($params as $param) {
                $paramName = $param->getName();
                $resolvedParams[] = $params_from_route[$paramName] ?? null;
            }
    
            return $resolvedParams;
        }


        /**
         * listen function
         * 
         * @param string $path
         * @param string $method
         */
        public static function listen(string $path, string $method) { // recieve request
            foreach (self::$routes as $route) {
                // Check if the route path and method match the request
                if (preg_match($route['regex'], $path, $matches) && in_array($method, $route['methods'])) {
                    
                    // Removes an array element whose key is a number.
                    $matches = array_filter($matches, function($key) {
                        return !is_numeric($key);
                    }, ARRAY_FILTER_USE_KEY);

                    // get handler and middlewares
                    $handler = $route['handler'];
                    $middlewares = $route['middlewares'];

                    // combine route params and additional params
                    $params = [];
                    $params = array_merge($matches, $params);
                    $params = self::getParams($params, $handler);

                    // call middlewares and run handler
                    $active = new ActiveMiddleware($middlewares);
                    return $active -> run($handler, $params);
                }
            }
            self::handleException(404, 'Not Found');
        }
    }
?>