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
    
    // --- BẮT ĐẦU SỬA LỖI ---
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

        foreach ($this->routes as $route) {
            // 1. Chuyển đổi path của route thành regex
            // (ví dụ: /kehoachsanxuat/edit/(.*) -> #^/kehoachsanxuat/edit/(.*)$#)
            $routePattern = "#^" . $route['path'] . "$#";
            
            // 2. So sánh method VÀ so khớp regex của path
            if ($route['method'] === $requestMethod && preg_match($routePattern, $requestPath, $matches)) {
                
                // 3. Lấy các tham số đã bắt được (bỏ qua $matches[0] vì đó là toàn bộ chuỗi)
                array_shift($matches); 
                $params = $matches; // $params bây giờ là ['KH01']

                $controllerName = $route['controller'];
                $actionName = $route['action'];
                
                $controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    
                    $controller = new $controllerName();
                    if (method_exists($controller, $actionName)) {
                        // 4. Gọi hàm trong controller và truyền mảng $params vào
                        call_user_func_array([$controller, $actionName], $params);
                        return;
                    }
                }
            }
        }
        
        // Nếu không tìm thấy route, hiển thị lỗi 404
        $this->show404();
    }
    
    // Xóa hàm matchPath() cũ vì không còn dùng
    /*
    private function matchPath($routePath, $requestPath) {
        return $routePath === $requestPath;
    }
    */
    // --- KẾT THÚC SỬA LỖI ---
    
    private function show404() {
        http_response_code(404);
        echo "<h1>404 - Trang không tìm thấy</h1>";
        echo "<p>Xin lỗi, trang bạn đang tìm kiếm không tồn tại.</p>";
    }
}
?>