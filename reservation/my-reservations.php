<?php
session_start();
include("../includes/connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../log/index.php");

    die("⚠️ You must be logged in to view your reservations.");

}

$user_id = intval($_SESSION['user_id']);

$db = new database();
$conn = $db->connect();

// Join reservations with seats
$stmt = $conn->prepare("
    SELECT r.reservation_id, r.reservation_date, r.created_at, 
           s.table_number, s.seat_number
    FROM reservations r
    INNER JOIN seats s ON r.seat_id = s.seat_id
    WHERE r.user_id = ?
    ORDER BY r.reservation_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Reservations</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;400&display=swap');

        body {
            font-family: Arial, sans-serif;
            background: #DDF4E7;
            padding: 20px;
        }

        h2 {
            text-align: left;
            margin-left: 20px;
        }

        table {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
            background: #DDF4E7;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #26667F;
            color: white;
        }

        tr:hover {
            background: #67C090;
        }

        .btn-cancel {
            padding: 6px 12px;
            background: #e74c3c;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }

        .btn-cancel:hover {
            background: #c0392b;
        }
    </style>
</head>

<body>
    <?php include("reservation_header.php"); ?>
    <h2> My Reservations</h2>
    <table>
        <tr>
            <th>Table Number</th>
            <th>Seat Number</th>
            <th>Reservation Date</th>
            <th>Booked At</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['table_number']) ?></td>
                <td><?= htmlspecialchars($row['seat_number']) ?></td>
                <td><?= htmlspecialchars($row['reservation_date']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td>
                    <a class="btn-cancel" href="cancel-reservation.php?id=<?= $row['reservation_id'] ?>">Cancel</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>

</html>