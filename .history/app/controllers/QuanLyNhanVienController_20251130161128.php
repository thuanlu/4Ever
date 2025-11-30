<?php
require_once __DIR__ . '/BaseController.php';

class QuanLyNhanVienController extends BaseController
{
    public function index()
    {
        // Load employee data from model (to be implemented)
        $nhanviens = [];
        // TODO: Load from model
        require_once __DIR__ . '/../views/xuongtruong/quanlynhanvien/index.php';
    }

    // TODO: Implement CRUD methods (create, store, edit, update, delete)
}
