<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
session_start();
include '../includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];
    $assigned_sections = isset($_POST['assigned_sections']) ? implode(',', $_POST['assigned_sections']) : '';

    if ($username && $password && $role) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, password, role, assigned_sections) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed_password, $role, $assigned_sections);

        if ($stmt->execute()) {
            $message = "✅ ลงทะเบียนผู้ใช้เรียบร้อยแล้ว";
        } else {
            $message = "❌ เกิดข้อผิดพลาด: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "❗ กรุณากรอกข้อมูลให้ครบถ้วน";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ลงทะเบียนผู้ใช้</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2 class="mb-4">📋 ลงทะเบียนผู้ใช้</h2>
  <?php if ($message): ?>
    <div class="alert alert-info"><?= $message ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label for="username" class="form-label">ชื่อผู้ใช้</label>
      <input type="text" class="form-control" name="username" required>
    </div>

 <form method="post">
    <div class="mb-3">
      <label for="full_name" class="form-label">ชื่อ นามสกุล</label>
      <input type="text" class="form-control" name="full_name" required>
    </div>
	
    <div class="mb-3">
      <label for="password" class="form-label">รหัสผ่าน</label>
      <input type="password" class="form-control" name="password" required>
    </div>

    <div class="mb-3">
      <label for="role" class="form-label">บทบาท (Role)</label>
      <select class="form-select" name="role" required>
        <option value="">-- เลือกบทบาท --</option>
        <option value="admin">admin</option>
        <option value="staff">staff</option>
        <option value="coordinator">coordinator</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">✅ สมัครสมาชิก</button>
  </form>
</div>
</body>
</html>
