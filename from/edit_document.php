<?php
session_start();
require_once('../includes/db_connect.php'); // เชื่อมต่อฐานข้อมูล
require_once('../includes/function.php');  // ฟังก์ชันเสริม (ถ้ามี)

$code = $_GET['code'] ?? '';
$message = '';

// ดึงข้อมูลเอกสารจากฐานข้อมูล
$stmt = $pdo->prepare("SELECT * FROM documents WHERE code = ?");
$stmt->execute([$code]);
$doc = $stmt->fetch();

if (!$doc) {
    die("ไม่พบข้อมูลเอกสารที่ต้องการแก้ไข");
}

// ลบไฟล์แนบ
if (isset($_POST['delete_file'])) {
    $filename = '../ITA_upload/' . $doc['filename'];
    if (file_exists($filename)) {
        unlink($filename);
    }

    // อัปเดตฐานข้อมูลให้ล้างชื่อไฟล์
    $stmt = $pdo->prepare("UPDATE documents SET filename = NULL WHERE code = ?");
    $stmt->execute([$code]);

    $message = "✅ ลบไฟล์แนบเรียบร้อยแล้ว";

    // อัปเดตตัวแปรในหน้านี้
    $doc['filename'] = null;
}

// บันทึกการแก้ไขชื่อเรื่อง
if (isset($_POST['save'])) {
    $subject = trim($_POST['subject']);
    $new_title = $doc['code'] . ' ' . $subject;

    $stmt = $pdo->prepare("UPDATE documents SET title = ? WHERE code = ?");
    $stmt->execute([$new_title, $code]);

    $message = "✅ บันทึกข้อมูลเรียบร้อยแล้ว";
    $doc['title'] = $new_title;
}
?>

<?php include('edit_form_template.php'); ?>
