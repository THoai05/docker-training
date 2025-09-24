<!-- <?php
        //session_start();
        //session_destroy();
        //header('location: login.php');
        ?> -->
<?php
// logout.php - dùng khi session PHP + SessionHandlerRedis
require_once __DIR__ . '/configs/redis.php';              // $redis
require_once __DIR__ . '/sessions/SessionHandlerRedis.php';

$handler = new SessionHandlerRedis($redis);
session_set_save_handler($handler, true);
session_start();

// Lấy session id hiện tại
$sid = session_id();

// Hủy session PHP (gọi handler->destroy)
$_SESSION = [];
session_unset();
session_destroy();

// Xóa một số key liên quan (nếu bạn lưu thêm csrf:<sid> hoặc session:<sid> thủ công)
if (!empty($sid)) {
    // ví dụ bạn lưu csrf và session dưới tên csrf:$sid, session:$sid
    $redis->del("session:$sid");
    $redis->del("csrf:$sid");
}

// Xóa cookie session ở client (PHPSESSID)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Trả trang có script xóa localStorage và chuyển hướng
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Logout</title>
</head>

<body>
    <p>Bạn đã đăng xuất. Đang chuyển về trang đăng nhập…</p>
    <script>
        // Xóa token/session trong localStorage (nếu có)
        localStorage.removeItem('session_id');
        localStorage.removeItem('csrf_token');
        localStorage.removeItem('user_id');
        localStorage.removeItem('username');

        // Redirect ngay
        window.location.href = 'login.php';
    </script>
</body>

</html>