<?php
include("includes/session_check.php"); // เช็กเซสชันก่อนทำอย่างอื่น
include("includes/db.php"); // เชื่อมต่อฐานข้อมูล

// โหลดรายการเอกสารทั้งหมดพร้อมชื่อหมวดหมู่ (ถ้าต้องการ)
$sql = "SELECT title, filename, upload_date, uploaded_by FROM documents ORDER BY upload_date ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>จัดการเอกสาร ITA</title>
  <!-- Bootstrap Cosmo -->
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/cosmo/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
  
  <style>
    body {
      font-family: 'Sarabun', sans-serif;
      background-color: #f8f9fa;
    }

    .container {
      max-width: 1100px;
    }

    h2 {
      font-weight: 600;
      color: #2c3e50;
    }

    .table-wrapper {
      background: #fff;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }

    table.table {
      box-shadow: 0 4px 8px rgba(0,0,0,0.06);
      border-radius: 10px;
      overflow: hidden;
    }

    thead.table-primary th {
      background: linear-gradient(45deg, #3498db, #2980b9);
      color: #fff;
      font-weight: 600;
      text-align: center;
    }

    tbody tr:hover {
      background-color: #f0f3f5;
    }

    .btn-sm {
      border-radius: 0.4rem;
      box-shadow: 0 2px 5px rgba(0,0,0,0.15);
      transition: 0.3s ease;
    }

    .btn-sm:hover {
      box-shadow: 0 4px 10px rgba(0,0,0,0.25);
    }

    .welcome-bar {
      background: #ffffff;
      border: 1px solid #dee2e6;
      border-radius: 10px;
      padding: 15px 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .welcome-bar strong {
      font-weight: 600;
      color: #2d3436;
    }
  </style>
</head>
<body>

<div class="container mt-5">
  <div class="table-wrapper">

 <div class="d-flex justify-content-start align-items-center mb-3">
  <img src="images/ITALogo.png" alt="ITA Logo" class="img-fluid me-2" style="max-height: 60px;">
  <h2 class="mb-0"> ITA (Integrity and Transparency Assessment)</h2>
</div>

    <div class="welcome-bar d-flex flex-wrap justify-content-between align-items-center mb-4">
      <div class="mb-2 mb-md-0">
        👋 ยินดีต้อนรับ, <strong><?= htmlspecialchars($_SESSION['user']); ?></strong>
      </div>
      <div class="d-flex gap-2">
        <a href="from/add.php" class="btn btn-success btn-sm">➕ อัปโหลดใหม่</a>
        <a href="system/logout.php" class="btn btn-outline-danger btn-sm">🚪 ออกจากระบบ</a>
      </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
      <div class="alert alert-success shadow-sm"><?= htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
      <table class="table table-bordered table-hover table-striped">
        <thead class="table-primary">
          <tr>
            <th>ลำดับ</th>
            <th>ชื่อเรื่อง</th>
            <th>วันที่อัปโหลด</th>
            <th>ไฟล์ PDF</th>
            <th>ผู้ที่อัปโหลด</th>
            <th>จัดการไฟล์</th>
          </tr>
        </thead>
        <tbody>
          <?php
  $lastMainCode = ''; // เก็บหัวข้อหลักล่าสุดที่เจอ เช่น 02
  $i = 1;
  while ($row = $result->fetch_assoc()):
    $parts = explode(' ', $row['title'], 2);
    $titlePart = $parts[1] ?? '';
    $codePart = str_replace("ITA-", "", $parts[0]);

    // แยกหัวข้อหลัก เช่น 02 จาก 02-1
    $mainCode = explode('-', $codePart)[0];
    $isSub = strpos($codePart, '-') !== false;
?>

<tr <?= $isSub ? 'class="table-light"' : '' ?>>
  <td class="text-center"><?= $i++; ?></td>
  <td>
    <?php if ($isSub): ?>
      &nbsp;&nbsp;&nbsp;↳ <?= htmlspecialchars($row['title']); ?>
    <?php else: ?>
      <strong><?= htmlspecialchars($row['title']); ?></strong>
    <?php endif; ?>
  </td>
  <td class="text-center"><?= date('d-m-Y H:i', strtotime($row['upload_date'])); ?></td>
  <td class="text-center">
    <?php if (file_exists("ITA_upload/" . $row['filename'])): ?>
      <a href="ITA_upload/<?= htmlspecialchars($row['filename']); ?>" target="_blank" class="btn btn-outline-primary btn-sm">📄 เปิด</a>
    <?php else: ?>
      <span class="text-danger">❌ ไม่พบไฟล์</span>
    <?php endif; ?>
  </td>
  <td class="text-center"><?= htmlspecialchars($row['uploaded_by']); ?></td>
  <td class="text-center">
    <a href="from/edit.php?code=<?= urlencode($codePart); ?>" class="btn btn-warning btn-sm">แก้ไข</a>
    <a href="from/delete.php?code=<?= urlencode($codePart); ?>" onclick="return confirm('คุณแน่ใจว่าต้องการลบเอกสารนี้?');" class="btn btn-danger btn-sm">ลบ</a>
  </td>
</tr>

<?php endwhile; ?>

        </tbody>
      </table>
    <?php else: ?>
      <p class="text-muted">ไม่มีเอกสารในระบบ</p>
    <?php endif; ?>

  </div>
</div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- ระบบ timeout -->
<script>
let warningTimer, logoutTimer;
const warningTime = 18 * 60 * 1000;
const logoutTime  = 20 * 60 * 1000;

function startTimers() {
  clearTimeout(warningTimer);
  clearTimeout(logoutTimer);

  warningTimer = setTimeout(() => {
    alert('⏰ ไม่มีการใช้งานมา 18 นาที\nระบบจะออกจากระบบภายใน 2 นาที');
  }, warningTime);

  logoutTimer = setTimeout(() => {
    alert('หมดเวลาการใช้งาน ระบบจะออกจากระบบ');
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
