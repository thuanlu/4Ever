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
            // Kiểm tra method và so khớp đường dẫn bằng regex
            if ($route['method'] === $requestMethod && preg_match('#^' . $route['path'] . '$#', $requestPath, $matches)) {
                $controllerName = $route['controller'];
                $actionName = $route['action'];
                
                $controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    
                    $controller = new $controllerName();
                    if (method_exists($controller, $actionName)) {
                        // Nếu có tham số động trong URL (vd: /qc/view/PYC01)
                        // thì truyền vào controller (vd: view('PYC01'))
                        if (isset($matches[1])) {
                            $controller->$actionName($matches[1]);
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
    
    // private function matchPath($routePath, $requestPath) {
    //     return $routePath === $requestPath;
    // }

    private function matchPath($routePath, $requestPath) {
    $pattern = '#^' . $routePath . '$#';
    return preg_match($pattern, $requestPath);
    }

    
    private function show404() {
        http_response_code(404);
        echo "<h1>404 - Trang không tìm thấy</h1>";
        echo "<p>Xin lỗi, trang bạn đang tìm kiếm không tồn tại.</p>";
        // echo "<a href='" . BASE_URL . "'>Quay về trang chủ</a>";
    }
}
?>
