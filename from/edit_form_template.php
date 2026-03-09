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

        <?php if (!empty($message)): ?>
          <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-3">
            <label class="form-label">รหัสหัวข้อ</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($code) ?>" disabled>
          </div>

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
            <?php if (!empty($doc['filename']) && file_exists('../ITA_upload/' . $doc['filename'])): ?>
              <a href="../ITA_upload/<?= htmlspecialchars($doc['filename']) ?>" target="_blank">
                📎 <?= htmlspecialchars($doc['filename']) ?>
              </a>
              <br>
              <button type="submit" name="delete_file" class="btn btn-sm btn-danger mt-2" onclick="return confirm('คุณต้องการลบไฟล์นี้ใช่หรือไม่?')">
                🗑 ลบไฟล์นี้
              </button>
            <?php else: ?>
              <span class="text-muted">ไม่มีไฟล์แนบ</span>
            <?php endif; ?>
          </div>

          <div class="d-flex justify-content-between mt-4">
            <a href="../dashboard.php" class="btn btn-secondary">↩ กลับ</a>
            <button type="submit" name="save" class="btn btn-primary">💾 บันทึก</button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

</body>
</html>
