<?php
include("../includes/connect.php");

$db = new database();
$conn = $db->connect();

$newHash = password_hash("000", PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE admins SET password=? WHERE admin_name='admin'");
$stmt->bind_param("s", $newHash);
$stmt->execute();

echo "✅ Admin password has been updated with a hash!";
?>