<?php
include("../includes/connect.php");

$db = new database();
$conn = $db->connect();

// New admin credentials
$adminName = "admin";       // username
$password = "Admin@123";    // password you want

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert into database
$stmt = $conn->prepare("INSERT INTO admins (admin_name, password) VALUES (?, ?)");
$stmt->bind_param("ss", $adminName, $hashedPassword);

if ($stmt->execute()) {
    echo "✅ Admin user created successfully!<br>";
    echo "👉 Username: $adminName<br>";
    echo "👉 Password: $password<br>";
} else {
    echo "❌ Error: " . $stmt->error;
}
?>