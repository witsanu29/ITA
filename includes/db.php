<?php
//include 'session_check.php';  // ต้องอยู่บรรทัดแรกเสมอ
$host = 'localhost';
$user = 'sa';
$pass = 'sa';
$dbname = 'ita_db';
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// กำหนด Charset สำหรับภาษาไทย
$conn->set_charset("utf8mb4");
?>