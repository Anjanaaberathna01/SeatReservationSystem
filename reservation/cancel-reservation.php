<?php
session_start();
include("../includes/connect.php");

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to cancel a reservation.");
}

if (!isset($_GET['id'])) {
    die("❌ Missing reservation ID.");
}

$reservation_id = intval($_GET['id']);
$user_id = intval($_SESSION['user_id']);

$db = new database();
$conn = $db->connect();

// Make sure reservation belongs to the logged-in user
$stmt = $conn->prepare("DELETE FROM reservations WHERE reservation_id = ? AND user_id = ?");
$stmt->bind_param("ii", $reservation_id, $user_id);

$success = $stmt->execute();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Cancel Reservation</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;400&display=swap');

        body {
            font-family: "Poppins", sans-serif;
            background: #DDF4E7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .message-box {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            width: 400px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .success {
            color: #27ae60;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .error {
            color: #e74c3c;
            font-size: 20px;
            margin-bottom: 15px;
        }

        a {
            display: inline-block;
            margin-top: 12px;
            padding: 8px 14px;
            background: #26667F;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.3s;
        }

        a:hover {
            background: #124170;
        }
    </style>
</head>

<body>
    <div class="message-box">
        <?php if ($success) { ?>
            <div class="success">✅ Reservation cancelled successfully.</div>
        <?php } else { ?>
            <div class="error">❌ Error cancelling reservation.</div>
        <?php } ?>
        <a href="my-reservations.php">Back to My Reservations</a>
    </div>
</body>

</html>