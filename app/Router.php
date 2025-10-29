<?php
/**
 * Lớp Router xử lý định tuyến ứng dụng
 * Hệ thống quản lý sản xuất nhà máy 4Ever
 */

class Router {
    private $routes = [];
    
    public function addRoute($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }
    


    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Loại bỏ base path nếu có
        $basePath = '/4Ever';
        if (strpos($requestPath, $basePath) === 0) {
            $requestPath = substr($requestPath, strlen($basePath));
        }
        
        // Nếu path rỗng thì chuyển về trang chủ
        if ($requestPath === '' || $requestPath === '/') {
            $requestPath = '/home';
        }

        
        // foreach ($this->routes as $route) {
        //     if ($route['method'] === $requestMethod && $this->matchPath($route['path'], $requestPath)) {
        //         $controllerName = $route['controller'];
        //         $actionName = $route['action'];
                
        //         // Kiểm tra file controller có tồn tại
        //         $controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';
        //         if (file_exists($controllerFile)) {
        //             require_once $controllerFile;
                    
        //             $controller = new $controllerName();
        //             if (method_exists($controller, $actionName)) {
        //                 $controller->$actionName();
        //                 return;
        //             }
        //         }
        //     }
        // }
        foreach ($this->routes as $route) {

            // matchPath now returns false (no match) or an array of captured params (may be empty)
            $matchResult = $this->matchPath($route['path'], $requestPath);
            if ($route['method'] === $requestMethod && $matchResult !== false) {
                $controllerName = $route['controller'];
                $actionName = $route['action'];


                $controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;

                    $controller = new $controllerName();
                    if (method_exists($controller, $actionName)) {
                        // Nếu có tham số bắt được từ đường dẫn, truyền chúng vào action
                        if (is_array($matchResult) && count($matchResult) > 0) {
                            $controller->$actionName(...$matchResult);
                        } else {
                            $controller->$actionName();
                        }
                        return;
                    }
                }
            }
        }
        
        // Nếu không tìm thấy route, hiển thị lỗi 404
        $this->show404();
    }
    

    /**
     * Match a route path to the request path.
     * Supports exact match and simple pattern with (.*) to capture segments.
     * Returns false if no match, or an array of captured params (may be empty).
     */
    private function matchPath($routePath, $requestPath) {
        // If the route contains a simple wildcard pattern (.*), convert to regex
        if (strpos($routePath, '(.*)') !== false) {
            // Escape then restore the (.*) token
            $pattern = '#^' . preg_quote($routePath, '#') . '$#';
            $pattern = str_replace('\\(\\.\\*\\)', '(.*)', $pattern);
            if (preg_match($pattern, $requestPath, $matches)) {
                array_shift($matches); // remove full match
                return $matches; // return captured groups
            }
            return false;
        }

        // Exact match fallback
        return $routePath === $requestPath ? [] : false;

    }

    
    private function show404() {
        http_response_code(404);
        echo "<h1>404 - Trang không tìm thấy</h1>";
        echo "<p>Xin lỗi, trang bạn đang tìm kiếm không tồn tại.</p>";

        // echo "<a href='" . BASE_URL . "'>Quay về trang chủ</a>";
    }
}
?>

