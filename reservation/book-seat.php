<?php
session_start();
include("../includes/connect.php");

$db = new database();
$conn = $db->connect();

if (!isset($_SESSION['user_id'])) {
    echo "Not logged in";
    exit;
}

$user_id = intval($_SESSION['user_id']);
$seat_id = isset($_POST['seat_id']) ? intval($_POST['seat_id']) : 0;
$date = isset($_POST['date']) ? $_POST['date'] : '';

if (!$seat_id || !$date) {
    echo "Invalid seat or date";
    exit;
}

// 1️⃣ Check if the user has already booked a seat for this date
$stmt = $conn->prepare("SELECT reservation_id FROM reservations WHERE user_id=? AND reservation_date=? LIMIT 1");
$stmt->bind_param("is", $user_id, $date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "⚠️ You already have a reservation for this date. Only one seat per day is allowed.";
    exit;
}

// 2️⃣ Check if the seat is already booked
$stmt = $conn->prepare("SELECT reservation_id FROM reservations WHERE seat_id=? AND reservation_date=? LIMIT 1");
$stmt->bind_param("is", $seat_id, $date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "❌ Seat already booked by someone else.";
    exit;
}

// 3️⃣ Insert reservation
$stmt = $conn->prepare("INSERT INTO reservations (seat_id, user_id, reservation_date) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $seat_id, $user_id, $date);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Database error: " . $stmt->error;
}