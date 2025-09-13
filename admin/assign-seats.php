<?php
session_start();
include("../includes/connect.php");
if (!isset($_SESSION['admin_name'])) {
    header("Location: index.php");
    exit;
}

$db = new database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user = intval($_POST['user_id']);
    $seat = intval($_POST['seat_id']);
    $date = $_POST['date'];

    $stmt = $conn->prepare("INSERT INTO reservations (seat_id,user_id,reservation_date) VALUES (?,?,?)");
    $stmt->bind_param("iis", $seat, $user, $date);
    if ($stmt->execute())
        $msg = "✅ Seat Assigned!";
    else
        $msg = "❌ Error: " . $stmt->error;
}

// Users & Seats list
$users = $conn->query("SELECT id,name FROM users");
$seats = $conn->query("SELECT * FROM seats");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Assign Seat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #5499cdff, #eef3f7);
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header placeholder */
        header {
            width: 100%;
        }

        .main-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        h2 {
            color: #124170;
            margin-bottom: 25px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            text-align: left;
            font-weight: 500;
            margin-bottom: 5px;
            color: #333;
        }

        select,
        input[type="date"] {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            transition: border 0.3s, box-shadow 0.3s;
            width: 100%;
            box-sizing: border-box;
        }

        select:focus,
        input:focus {
            border-color: #26667F;
            box-shadow: 0 0 5px rgba(38, 102, 127, 0.5);
            outline: none;
        }

        button {
            padding: 12px;
            border-radius: 8px;
            border: none;
            background: #26667F;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        button:hover {
            background: #124170;
            transform: scale(1.05);
        }

        .message {
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            color: white;
            display: inline-block;
        }

        .success {
            background: #27ae60;
        }

        .error {
            background: #e74c3c;
        }

        @media (max-width: 500px) {
            .container {
                padding: 30px 20px;
            }

            select,
            input,
            button {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <!-- Include header at top -->
    <header>
        <?php include("admin-header.php"); ?>
    </header>

    <div class="main-container">
        <div class="container">
            <h2>Assign Seat</h2>
            <?php if (!empty($msg)): ?>
                <div class="message <?= strpos($msg, '✅') !== false ? 'success' : 'error' ?>">
                    <?= $msg ?>
                </div>
            <?php endif; ?>
            <form method="post" id="assignForm">
                <label>User:</label>
                <select name="user_id" required>
                    <?php while ($u = $users->fetch_assoc()): ?>
                        <option value="<?= $u['id'] ?>"><?= $u['name'] ?></option>
                    <?php endwhile; ?>
                </select>

                <label>Seat:</label>
                <select name="seat_id" required>
                    <?php while ($s = $seats->fetch_assoc()): ?>
                        <option value="<?= $s['seat_id'] ?>">Table <?= $s['table_number'] ?> - Seat <?= $s['seat_number'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label>Date:</label>
                <input type="date" name="date" required>

                <button type="submit">Assign</button>
            </form>
        </div>
    </div>

    <script>
        // Clear message when user changes form
        const form = document.getElementById('assignForm');
        form.addEventListener('input', () => {
            const msg = document.querySelector('.message');
            if (msg) msg.style.display = 'none';
        });
    </script>
</body>

</html>