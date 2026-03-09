<?php
include("../includes/db.php");
session_start();

$code = $_GET['code'] ?? '';

if (!$code) {
    die("ไม่มีรหัสหัวข้อสำหรับลบ");
}

// หาไฟล์ที่จะลบก่อนลบข้อมูลใน DB
$stmt = $conn->prepare("SELECT filename FROM documents WHERE title LIKE ?");
$like = "ITA-" . $code . "%";
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();
$doc = $result->fetch_assoc();

if (!$doc) {
    die("ไม่พบเอกสารที่ต้องการลบ");
}

// ลบไฟล์ในโฟลเดอร์
$file_path = "ITA_upload/" . $doc['filename'];
if (file_exists($file_path)) {
    unlink($file_path);
}

// ลบข้อมูลในฐานข้อมูล
$stmt = $conn->prepare("DELETE FROM documents WHERE title LIKE ?");
$stmt->bind_param("s", $like);
if ($stmt->execute()) {
    // ลบสำเร็จ กลับไปหน้าจัดการ พร้อมแจ้งเตือน
    header("Location: ../dashboard.php?deleted=1");
    exit;
} else {
    die("ลบข้อมูลไม่สำเร็จ");
}

?>
