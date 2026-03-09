<?php
include("../includes/session_check.php"); // เช็กเซสชันก่อนทำอย่างอื่น
include("../includes/db.php"); // เชื่อมต่อฐานข้อมูล

$message = '';
$code = $_GET['code'] ?? '';

if (!$code) {
    die("ไม่มีรหัสหัวข้อ");
}

// ดึงข้อมูลเอกสาร
$stmt = $conn->prepare("SELECT * FROM documents WHERE title LIKE ?");
$like = "ITA-" . $code . "%";
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();
$doc = $result->fetch_assoc();

if (!$doc) {
    die("ไม่พบเอกสารที่ต้องการแก้ไข");
}

// 🔁 ลบไฟล์แนบเดิม
if (isset($_POST['delete_file'])) {
    $old_file = "../ITA_upload/" . $doc['filename'];
    if (file_exists($old_file)) {
        unlink($old_file);
    }

    $stmt = $conn->prepare("UPDATE documents SET filename = NULL WHERE id = ?");
    $stmt->bind_param("i", $doc['id']);
    $stmt->execute();
    $doc['filename'] = null;
    $message = "ลบไฟล์แนบเรียบร้อยแล้ว";
}

// 💾 อัปเดตชื่อเรื่อง
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $subject = $_POST['subject'] ?? '';
    $title = "ITA-" . $code . " " . $subject;

    $stmt = $conn->prepare("UPDATE documents SET title = ? WHERE id = ?");
    $stmt->bind_param("si", $title, $doc['id']);
    if ($stmt->execute()) {
        $message = "แก้ไขข้อมูลสำเร็จ";
        $doc['title'] = $title;
    } else {
        $message = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล";
    }
}

// 📎 อัปโหลดไฟล์ใหม่
if (isset($_FILES['new_file']) && $_FILES['new_file']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "../ITA_upload/";
    $originalName = basename($_FILES['new_file']['name']);
    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
    $newFileName = "ITA-" . $code . "-" . time() . "." . $ext;
    $targetFile = $uploadDir . $newFileName;

    if (move_uploaded_file($_FILES['new_file']['tmp_name'], $targetFile)) {
        // ลบไฟล์เก่า
        if (!empty($doc['filename']) && file_exists($uploadDir . $doc['filename'])) {
            unlink($uploadDir . $doc['filename']);
        }

        $stmt = $conn->prepare("UPDATE documents SET filename = ? WHERE id = ?");
        $stmt->bind_param("si", $newFileName, $doc['id']);
        $stmt->execute();

        $doc['filename'] = $newFileName;
        $message = "อัปโหลดไฟล์ใหม่เรียบร้อยแล้ว";
    } else {
        $message = "เกิดข้อผิดพลาดในการอัปโหลดไฟล์";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <title>แก้ไขเอกสาร ITA รหัส <?= htmlspecialchars($code) ?></title>
  <!-- ใช้ธีม Minty จาก Bootswatch -->
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/minty/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f5f9fc;
    }
    .card-form {
      background: #fff;
      padding: 2.5rem;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    }
    .form-label {
      font-weight: 600;
    }
    .btn-primary {
      box-shadow: 0 4px 12px rgba(0,123,255,0.3);
    }
    .btn-secondary {
      box-shadow: 0 4px 10px rgba(108,117,125,0.2);
    }
  </style>
</head>
<body>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="card-form">

        <h3 class="text-primary mb-4">✏️ แก้ไขเอกสาร ITA รหัส <?= htmlspecialchars($code) ?></h3>

        <?php if ($message): ?>
          <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

	<!--ฟอร์ม -->
<form method="post" enctype="multipart/form-data">
  <div class="mb-3">
    <label class="form-label">ชื่อเรื่อง</label>
    <?php
      $parts = explode(' ', $doc['title'], 2);
      $subject = $parts[1] ?? '';
    ?>
    <input type="text" name="subject" class="form-control" value="<?= htmlspecialchars($subject) ?>" required>
  </div>

  <div class="mb-3">
    <label class="form-label">ไฟล์แนบเดิม</label><br>

   <?php if (!empty($doc['filename']) && file_exists("../ITA_upload/" . $doc['filename'])): ?>
  <div class="mb-3">
    <label class="form-label">ไฟล์แนบเดิม:</label><br>
    <a href="../ITA_upload/<?= htmlspecialchars($doc['filename']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
      📄 <?= htmlspecialchars($doc['filename']) ?>
    </a>
    <button type="submit" name="delete_file" class="btn btn-danger btn-sm ms-2" onclick="return confirm('คุณต้องการลบไฟล์แนบนี้หรือไม่?')">
      🗑 ลบไฟล์
    </button>
  </div>
	<?php else: ?>
  <div class="mb-3 text-muted">ไม่มีไฟล์แนบเดิม</div>
	<?php endif; ?>
  </div>

  <div class="mb-3">
    <label class="form-label">อัปโหลดไฟล์ใหม่ (ถ้ามี)</label>
    <input type="file" name="new_file" class="form-control">
  </div>

  <div class="d-flex justify-content-between mt-4">
    <button type="submit" name="save" class="btn btn-primary">💾 บันทึก</button>
	<a href="../dashboard.php" class="btn btn-secondary">↩ กลับ</a>
    </div>
</form>

<?php if (!empty($message)): ?>
  <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

      </div>
    </div>
  </div>
</div>
<script>
let warningTimer, logoutTimer;
const warningTime = 18 * 60 * 1000; // 18 นาที (แจ้งเตือน)
const logoutTime  = 20 * 60 * 1000; // 20 นาที (logout)

function startTimers() {
  clearTimeout(warningTimer);
  clearTimeout(logoutTimer);

  warningTimer = setTimeout(() => {
    alert('⏰ ระบบตรวจพบว่าไม่มีการใช้งานมาเป็นเวลา 18 นาที\nหากไม่มีการใช้งานต่อ จะออกจากระบบอัตโนมัติใน 2 นาที');
  }, warningTime);

  logoutTimer = setTimeout(() => {
    alert('หมดเวลาการใช้งาน ระบบจะนำคุณออกจากระบบ');
    window.location.href = 'system/logout.php';
  }, logoutTime);
}

['click', 'mousemove', 'keypress', 'scroll'].forEach(evt => {
  document.addEventListener(evt, startTimers, false);
});

startTimers(); // เริ่มนับทันทีเมื่อโหลดหน้า
</script>
</body>
</html>
