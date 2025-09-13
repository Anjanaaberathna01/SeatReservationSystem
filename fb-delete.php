<?php
session_start();
include("includes/connect.php");

$conn = (new database())->connect();

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Delete user data based on Facebook ID
    $stmt = $conn->prepare("DELETE FROM users WHERE facebook_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();

    header('Content-Type: application/json');
    echo json_encode([
        "url" => "https://abcd1234.ngrok.io/seat_reservation_system/fb-delete.php", // Replace with your actual URL
        "status" => "success"
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "error",
        "message" => "Missing user_id"
    ]);
}