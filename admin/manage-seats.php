<?php
session_start();
include("../includes/connect.php");
if (!isset($_SESSION['admin_name'])) {
    header("Location: index.php");
    exit;
}

$db = new database();
$conn = $db->connect();

// Add seat
if (isset($_POST['add'])) {
    $table = intval($_POST['table_number']);
    $seat = intval($_POST['seat_number']);
    $stmt = $conn->prepare("INSERT INTO seats (table_number, seat_number) VALUES (?, ?)");
    $stmt->bind_param("ii", $table, $seat);
    $stmt->execute();
}

// Delete seat
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM seats WHERE seat_id=$id");
}

// Fetch seats
$seats = $conn->query("SELECT * FROM seats ORDER BY table_number, seat_number");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Manage Seats</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding: 20px;
            background: linear-gradient(135deg, #5499cdff, #eef3f7);
            margin: 0;
        }

        h2 {
            color: #124170;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Form styling */
        form {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        form input {
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            width: 150px;
        }

        form button {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            background: #26667F;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        form button:hover {
            background: #124170;
            transform: scale(1.05);
        }

        /* Table styling */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        th,
        td {
            padding: 12px;
            text-align: center;
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

        td a {
            color: #e74c3c;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        td a:hover {
            color: #c0392b;
        }

        @media (max-width: 600px) {
            form {
                flex-direction: column;
                align-items: center;
            }

            form input,
            form button {
                width: 90%;
                font-size: 14px;
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

    <h2>Manage Seats</h2>

    <form method="post">
        <input type="number" name="table_number" placeholder="Table Number" required>
        <input type="number" name="seat_number" placeholder="Seat Number" required>
        <button type="submit" name="add">Add Seat</button>
    </form>

    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>Table</th>
                <th>Seat</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $seats->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['seat_id'] ?></td>
                    <td><?= $row['table_number'] ?></td>
                    <td><?= $row['seat_number'] ?></td>
                    <td><a href="?delete=<?= $row['seat_id'] ?>" onclick="return confirm('Delete this seat?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <script>
        // Optional: highlight row when clicked
        document.querySelectorAll('table tr').forEach(row => {
            row.addEventListener('click', () => {
                row.style.backgroundColor = '#d0ebff';
                setTimeout(() => row.style.backgroundColor = '', 500);
            });
        });
    </script>
</body>

</html>