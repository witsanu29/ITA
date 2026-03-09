<?php
include("../includes/session_check.php"); // เช็กเซสชันก่อนทำอย่างอื่น
include("../includes/db.php");

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $user = $_SESSION['user'];

    $title = "ITA-" . $code . " " . $subject;

    // ตรวจสอบว่ามีไฟล์อัปโหลดและไม่มีข้อผิดพลาด
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {

        // ตรวจสอบชนิด MIME ของไฟล์จริง ด้วย finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['pdf']['tmp_name']);
        finfo_close($finfo);

        if ($mime === 'application/pdf') {

            // ตรวจสอบขนาดไฟล์ไม่เกิน 10 MB (ปรับตามต้องการ)
            $maxFileSize = 10 * 1024 * 1024; // 10 MB
            if ($_FILES['pdf']['size'] <= $maxFileSize) {

                 // ใหม่: อนุญาตทั้งตัวเลขและขีดกลาง เช่น 02-1
				$safe_code = preg_replace('/[^0-9\-]/', '', $code);
				$new_name = "ITA-" . $safe_code . "_" . uniqid() . ".pdf";

				 // สร้างโฟลเดอร์ถ้ายังไม่มี
				$uploadDir = "../ITA_upload/";
				if (!is_dir($uploadDir)) {
				 mkdir($uploadDir, 0755, true);
				}

                if (move_uploaded_file($_FILES['pdf']['tmp_name'], $uploadDir . $new_name)) {
                    $stmt = $conn->prepare("INSERT INTO documents (title, filename, upload_date, uploaded_by) VALUES (?, ?, NOW(), ?)");
                    $stmt->bind_param("sss", $title, $new_name, $user);

                    if ($stmt->execute()) {
                        header("Location: ../dashboard.php");
                        exit();
                    } else {
                        $message = "❌ เกิดข้อผิดพลาดในการบันทึกข้อมูล";
                    }
                } else {
                    $message = "❌ ไม่สามารถอัปโหลดไฟล์ได้";
                }

            } else {
                $message = "❌ ขนาดไฟล์ต้องไม่เกิน 10 MB";
            }

        } else {
            $message = "❌ กรุณาอัปโหลดไฟล์ PDF เท่านั้น";
        }

    } else {
        $message = "❌ กรุณาเลือกไฟล์ PDF ที่ถูกต้องสำหรับอัปโหลด";
    }
}

$latestDoc = null;
$sql = "SELECT title, upload_date FROM documents ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $latestDoc = $result->fetch_assoc();
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <title>เพิ่มเอกสาร ITA</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  

  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card-form {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      padding: 2.5rem;
    }
    .form-label {
      font-weight: 500;
    }
    .btn-success {
      box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }
    .btn-secondary {
      box-shadow: 0 4px 12px rgba(108, 117, 125, 0.2);
    }
  </style>
</head>
<body>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card-form">
        <h3 class="mb-4 text-success">➕ เพิ่มเอกสารใหม่</h3>

        <?php if (!empty($message)): ?>
		 <div class="alert alert-danger" role="alert">
			<?= htmlspecialchars($message) ?>
			</div>
		<?php endif; ?>

        <form method="post" enctype="multipart/form-data">
          <div class="row mb-3">

            <div class="col-md-4">
			<label class="form-label">ITA-(ใส่เลข =01 รือ 02-1)</label>
			<input type="text" name="code" class="form-control" pattern="[0-9\-]+" title="กรอกเฉพาะตัวเลขและขีดกลาง เช่น 02-1" required />
			</div>

            <div class="col-md-8">
              <label class="form-label">ชื่อเรื่อง</label>
              <input type="text" name="subject" class="form-control" required />
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">ไฟล์ PDF</label>
            <input type="file" name="pdf" class="form-control" accept="application/pdf" required />
          </div>

          <div class="d-flex justify-content-between">
		  <button type="submit" class="btn btn-success">📤 อัปโหลด</button>
            <a href="../dashboard.php" class="btn btn-secondary">↩ กลับหน้าจัดการ</a>
            
          </div>
		  </form>

		<?php if ($latestDoc): ?>
			<div class="mt-4 p-3 border border-success rounded bg-light shadow-sm">
			<h5 class="text-success mb-2">📌 เอกสารล่าสุดที่เพิ่ม</h5>
		<?php
		$parts = explode(' ', $latestDoc['title'], 2);
		$code = $parts[0] ?? '';
		 $subject = $parts[1] ?? '';
		?>
		<p class="mb-0">
      <strong>รหัส:</strong> <?= htmlspecialchars($code) ?><br>
      <strong>ชื่อเรื่อง:</strong> <?= htmlspecialchars($subject) ?><br>
      <small class="text-muted">เพิ่มเมื่อ: <?= date('d/m/Y', strtotime($latestDoc['upload_date'])) ?></small>
		</p>
		</div>
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