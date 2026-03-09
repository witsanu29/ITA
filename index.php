<?php
session_start();
include("includes/db.php");

// โหลดรายการเอกสารทั้งหมดพร้อมชื่อหมวดหมู่ (ถ้าต้องการ)
$sql = "SELECT title, filename, upload_date, uploaded_by FROM documents ORDER BY upload_date ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <title>ระบบจัดการเอกสาร ITA</title>

  <!-- ฟอนต์ Sarabun -->
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet" />

  <!-- Bootstrap Cosmo -->
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/cosmo/bootstrap.min.css" rel="stylesheet" />

  <style>
    body {
      font-family: 'Sarabun', sans-serif;
      background-color: #f8f9fa;
    }

    h2 {
      font-weight: 600;
      margin-bottom: 30px;
      text-align: center;
      color: #2d3436;
    }

    .table-wrapper {
      background: #ffffff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }

    .table th, .table td {
      vertical-align: middle;
      text-align: center;
    }

    .table thead th {
      background: linear-gradient(45deg, #2c3e50, #34495e);
      color: white;
      font-weight: 600;
    }

    .btn-primary {
      border-radius: 20px;
    }

    .btn-outline-light {
      border-radius: 20px;
    }

    .navbar-brand {
      font-weight: bold;
      font-size: 1.2rem;
    }

    .badge {
      font-size: 0.85rem;
    }

    .highlight {
      background-color: #ecf0f1;
    }
  </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-2">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">
      📂 ITA หน่วยงาน
    </a>

    <!-- ปุ่มเปิดเมนูบนมือถือ -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- เมนูด้านขวา -->
    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav ms-auto align-items-center gap-2">
        <?php if (isset($_SESSION['user'])): ?>
          <li class="nav-item text-white me-2">
            👤 <strong><?= htmlspecialchars($_SESSION['user']); ?></strong>
          </li>
          <li class="nav-item">
            <a href="upload.php" class="btn btn-outline-warning btn-sm rounded-pill">⬆️ อัปโหลด</a>
          </li>
          <li class="nav-item">
            <a href="logout.php" class="btn btn-outline-light btn-sm rounded-pill">🚪 ออกจากระบบ</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a href="system/login.php" class="btn btn-outline-light btn-sm rounded-pill">🔐 เข้าสู่ระบบ</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>



<!-- Content -->
	<div class="container-lg mt-5">

	<div class="table-wrapper border border-2 border-primary shadow-lg rounded-4 p-4">
  <div class="d-flex align-items-center mb-4">
    <img src="images/ITALogo.png" alt="ITA Logo" class="img-fluid me-3" style="max-height: 60px;">
    <h2 class="mb-0 text-primary">📘 ITA (Integrity and Transparency Assessment)</h2>
  </div>

  <?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-hover table-striped align-middle border rounded-3 overflow-hidden">
        <thead class="table-primary text-dark">
          <tr class="text-center">
            <th scope="col">ลำดับ</th>
            <th scope="col" class="text-start">ชื่อเรื่อง</th>
            <th scope="col">📅 วันที่อัปโหลด</th>
            <th scope="col">📂 ไฟล์ PDF</th>
            <th scope="col">👤 ผู้ที่อัปโหลด</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $i = 1;
            while ($row = $result->fetch_assoc()):
              $parts = explode(' ', $row['title'], 2);
              $code = str_replace("ITA-", "", $parts[0]);
              $subject = $parts[1] ?? '';
              $isSubItem = strpos($code, '-') !== false;
          ?>
          <tr class="<?= $isSubItem ? 'bg-light' : '' ?>">
            <td class="text-center fw-bold"><?= $i++; ?></td>
            <td class="text-start">
              <?php if ($isSubItem): ?>
                <span class="text-secondary ms-3">↳ <?= htmlspecialchars("ITA-" . $code . " " . $subject); ?></span>
              <?php else: ?>
                <span class="fw-bold text-dark">📁 <?= htmlspecialchars("ITA-" . $code . " " . $subject); ?></span>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <span class="badge bg-light text-dark">
                <?= date('d-m-Y H:i', strtotime($row['upload_date'])); ?>
              </span>
            </td>
            <td class="text-center">
              <?php if (file_exists("ITA_upload/" . $row['filename'])): ?>
                <a href="ITA_upload/<?= htmlspecialchars($row['filename']); ?>" target="_blank" class="btn btn-sm btn-outline-success rounded-pill shadow-sm">
                  🔗 เปิด
                </a>
              <?php else: ?>
                <span class="text-danger">❌ ไม่พบไฟล์</span>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <span class="badge bg-secondary rounded-pill"><?= htmlspecialchars($row['uploaded_by']); ?></span>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-warning text-center mt-4 rounded-3 shadow-sm">🔍 ไม่มีเอกสารในระบบ</div>
  <?php endif; ?>
</div>
</div>

<!-- Bootstrap Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Auto logout script -->
<script>
let warningTimer, logoutTimer;
const warningTime = 18 * 60 * 1000;
const logoutTime  = 20 * 60 * 1000;

function startTimers() {
  clearTimeout(warningTimer);
  clearTimeout(logoutTimer);

  warningTimer = setTimeout(() => {
    alert('⏰ ไม่มีการใช้งานมา 18 นาที\nระบบจะออกจากระบบอัตโนมัติใน 2 นาที');
  }, warningTime);

  logoutTimer = setTimeout(() => {
    alert('หมดเวลาการใช้งาน ระบบจะนำคุณออกจากระบบ');
    window.location.href = 'system/logout.php';
  }, logoutTime);
}

['click', 'mousemove', 'keypress', 'scroll'].forEach(evt => {
  document.addEventListener(evt, startTimers, false);
});

startTimers();
</script>
</body>
</html>
