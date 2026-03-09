<?php
session_start();

// หากยังไม่ได้เข้าสู่ระบบ ให้ redirect กลับ
if (!isset($_SESSION['user'])) {
    header("Location: ../system/login.php");
    exit;
}

// กำหนด timeout (20 นาที = 1200 วินาที)
$timeout = 1200;

// หากเคยมีการเก็บเวลา session ล่าสุดไว้
if (isset($_SESSION['last_activity'])) {
    $inactive = time() - $_SESSION['last_activity'];
    if ($inactive > $timeout) {
        session_unset();
        session_destroy();
        echo "<script>
          alert('⏰ หมดเวลาการใช้งาน กรุณาเข้าสู่ระบบใหม่');
          window.location.href = '../index.php';
        </script>";
        exit;
    }
}

// อัปเดตเวลาล่าสุดของการใช้งาน
$_SESSION['last_activity'] = time();
?>
