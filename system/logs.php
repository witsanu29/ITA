$user = $_SESSION['user'];
$action = "แก้ไขเอกสาร ID $id";
$conn->query("INSERT INTO logs (user, action, doc_id) VALUES ('$user', '$action', $id)");
