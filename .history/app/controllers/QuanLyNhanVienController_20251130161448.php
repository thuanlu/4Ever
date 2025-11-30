<?php
require_once __DIR__ . '/BaseController.php';

class QuanLyNhanVienController extends BaseController
{
    public function index()
    {
        require_once __DIR__ . '/../models/NhanVien.php';
        require_once __DIR__ . '/../../config/database.php';
        $db = getPDO();
        $nhanvienModel = new NhanVien($db);
        $nhanviens = $nhanvienModel->getAll();
        require_once __DIR__ . '/../views/xuongtruong/quanlynhanvien/index.php';
    }

    // TODO: Implement CRUD methods (create, store, edit, update, delete)
}
