<?php
session_start();
include("../includes/db.php");

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $sql = "SELECT * FROM users WHERE username = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();

    // ✅ ตรวจสอบรหัสผ่านแบบเข้ารหัส
    if (password_verify($password, $row['password'])) {
      $_SESSION['user'] = $username;
      $_SESSION['role'] = $row['role'];
      $_SESSION['user_id'] = $row['id'];
      header("Location: ../dashboard.php");
      exit;
    } else {
      $error = "รหัสผ่านไม่ถูกต้อง";
    }
  } else {
    $error = "ชื่อผู้ใช้ไม่ถูกต้อง";
  }

  $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <title>เข้าสู่ระบบ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/lux/bootstrap.min.css" rel="stylesheet" />
  <style>
    body, html {
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding-top: 60px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8f9fa;
    }

    .login-card {
      background: #fff;
      padding: 3rem;
      border-radius: 16px;
      box-shadow: 0 12px 32px rgba(0,0,0,0.15);
      width: 420px;
      max-width: 90%;
    }

    .btn-login {
      border-radius: 50px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      transition: all 0.3s ease;
      padding: 0.7rem 2rem;
      font-size: 1.1rem;
    }

    .btn-login:hover {
      box-shadow: 0 6px 18px rgba(0,0,0,0.35);
      transform: translateY(-2px);
    }

    h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: #343a40;
      font-weight: 700;
      letter-spacing: 1px;
      font-size: 2rem;
    }
  </style>
</head>
<body>

<div class="login-card">
  <h2>เข้าสู่ระบบ</h2>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post" novalidate>
    <div class="mb-3">
      <label for="username" class="form-label">ชื่อผู้ใช้</label>
      <input type="text" id="username" name="username" class="form-control" placeholder="กรุณากรอกชื่อผู้ใช้" required>
    </div>

    <div class="mb-4">
      <label for="password" class="form-label">รหัสผ่าน</label>
      <input type="password" id="password" name="password" class="form-control" placeholder="กรุณากรอกรหัสผ่าน" required>
    </div>

    <button type="submit" class="btn btn-primary w-100 btn-login mb-3">เข้าสู่ระบบ</button>

    <div class="row g-2">
      <div class="col">
        <a href="../index.php" class="btn btn-outline-secondary w-100">↩ กลับหน้าหลัก</a>
      </div>
      <div class="col">
        <a href="register.php" class="btn btn-outline-primary w-100">📝 สมัครสมาชิก</a>
      </div>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
