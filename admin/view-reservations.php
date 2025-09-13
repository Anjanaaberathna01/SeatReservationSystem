<?php
session_start();
include("../includes/connect.php");
if (!isset($_SESSION['admin_name'])) {
    header("Location: index.php");
    exit;
}

$db = new database();
$conn = $db->connect();

// Get filters
$date = isset($_GET['date']) ? trim($_GET['date']) : '';
$user = isset($_GET['user']) ? trim($_GET['user']) : '';

// Base query
$sql = "SELECT r.reservation_id, r.reservation_date, r.created_at, u.name, s.table_number, s.seat_number
        FROM reservations r
        JOIN seats s ON r.seat_id = s.seat_id
        JOIN users u ON r.user_id = u.id
        WHERE 1=1";

// Apply filters separately
if ($date !== '') {
    $sql .= " AND r.reservation_date = '" . $conn->real_escape_string($date) . "'";
}
if ($user !== '') {
    $sql .= " AND u.name LIKE '%" . $conn->real_escape_string($user) . "%'";
}

$sql .= " ORDER BY r.reservation_date DESC";
$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html>

<head>
    <title>View Reservations</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #5499cdff, #eef3f7);
            padding: 20px;
            margin: 0;
        }

        h2 {
            color: #124170;
            margin-bottom: 20px;
            margin-left: 20px;
        }

        /* Filter form styling */
        form {
            display: flex;
            justify-content: left;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            margin-left: 20px;
        }

        form input[type="date"],
        form input[type="text"] {
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        form button {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        form button[type="submit"] {
            background: #26667F;
            color: white;
        }

        form button[type="submit"]:hover {
            background: #124170;
            transform: scale(1.05);
        }

        form button[type="button"] {
            background: #999;
            color: white;
        }

        form button[type="button"]:hover {
            background: #666;
            transform: scale(1.05);
        }

        /* Table styling */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        th,
        td {
            text-align: center;
            padding: 12px;
        }

        th {
            background: #26667F;
            color: white;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        tr {
            background: white;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        tr:nth-child(even) {
            background: #f8f8f8;
        }

        tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        td {
            font-size: 15px;
            color: #333;
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            form {
                flex-direction: column;
            }

            form input,
            form button {
                width: 100%;
            }

            th,
            td {
                padding: 8px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <?php include("admin-header.php"); ?>

    <h2>All Reservations</h2>
    <form method="get">
        <label>Select Date:</label>
        <input type="date" name="date" value="<?= htmlspecialchars($date) ?>">

        <label>Search by User:</label>
        <input type="text" name="user" placeholder="Enter user name" value="<?= htmlspecialchars($user) ?>">

        <button type="submit">Filter</button>
        <button type="button" onclick="window.location='view-reservations.php'">Clear</button>
    </form>

    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Table</th>
                <th>Seat</th>
                <th>Date</th>
                <th>Booked At</th>
            </tr>
            <?php if ($res->num_rows > 0): ?>
                <?php while ($row = $res->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['reservation_id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['table_number']) ?></td>
                        <td><?= htmlspecialchars($row['seat_number']) ?></td>
                        <td><?= htmlspecialchars($row['reservation_date']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No reservations found</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

</body>

</html>