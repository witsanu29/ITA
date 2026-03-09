<?php
include("includes/auth.php"); // ต้องล็อกอินก่อนเข้าหน้านี้
include("includes/db.php"); 
include("session_user.php"); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = $_POST['title'];
  $category = $_POST['category'];
  $file = $_FILES['pdf'];

  if ($file['type'] == "application/pdf") {
    $new_name = uniqid() . ".pdf";
    move_uploaded_file($file['tmp_name'], "uploads/$new_name");

    $stmt = $conn->prepare("INSERT INTO documents (title, filename, category_id, upload_date) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("ssi", $title, $new_name, $category);
    $stmt->execute();

    echo "อัปโหลดสำเร็จ";
  } else {
    echo "เฉพาะไฟล์ PDF เท่านั้น!";
  }
}
?>
<form method="post" enctype="multipart/form-data">
  <input type="text" name="title" required placeholder="ชื่อเรื่อง">
  <select name="category" required>
    <?php
    $res = $conn->query("SELECT * FROM categories");
    while ($row = $res->fetch_assoc()) {
      echo "<option value='{$row['id']}'>{$row['name']}</option>";
    }
    ?>
  </select>
  <input type="file" name="pdf" accept="application/pdf" required>
  <button type="submit">อัปโหลด</button>
</form>
