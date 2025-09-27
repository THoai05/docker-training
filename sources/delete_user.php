<?php
session_start();
require_once 'models/UserModel.php';
$userModel = new UserModel();

$id = $_POST['id'] ?? null;
$token = $_POST['csrf_token'] ?? '';

// Kiểm tra token CSRF
if (!$id || !$token || $token !== ($_SESSION['csrf_token'] ?? '')) {
    die('Invalid CSRF token. Delete action blocked!');
}

// Token hợp lệ, thực hiện xóa
$userModel->deleteUserById($id);

// Redirect về danh sách
header('Location: list_users.php');
exit;
