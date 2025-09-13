<?php
session_start();
if (!isset($_SESSION['admin_name'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <?php include("admin-header.php"); ?>

    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #5499cdff, #eef3f7);
            margin: 0;
            padding: 0;
            text-align: center;
        }

        h1 {
            color: #124170;
            margin-top: 40px;
            margin-bottom: 60px;
            font-size: 32px;
            letter-spacing: 1px;
        }

        .dashboard {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 60px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .top-row,
        .bottom-row {
            display: flex;
            justify-content: center;
            gap: 50px;
            flex-wrap: wrap;
        }

        .card-link {
            background: linear-gradient(135deg, #26667F, #124170);
            color: white;
            text-decoration: none;
            border-radius: 20px;
            width: 250px;
            height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            font-weight: 600;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
            position: relative;
            overflow: hidden;
        }

        .card-link::before {
            content: '';
            position: absolute;
            width: 300%;
            height: 300%;
            background: rgba(255, 255, 255, 0.1);
            top: -100%;
            left: -100%;
            transform: rotate(45deg);
            transition: all 0.5s;
        }

        .card-link:hover::before {
            top: -50%;
            left: -50%;
        }

        .card-link:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.3);
        }

        .card-icon {
            background: rgba(255, 255, 255, 0.2);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px;
            font-size: 28px;
        }

        /* Responsive */
        @media (max-width: 1000px) {

            .top-row,
            .bottom-row {
                gap: 30px;
            }
        }

        @media (max-width: 700px) {

            .top-row,
            .bottom-row {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>

<body>
    <h1>Welcome, <?= $_SESSION['admin_name']; ?></h1>
    <div class="dashboard">
        <!-- First row -->
        <div class="top-row">
            <a href="manage-seats.php" class="card-link">
                <div class="card-icon">➕</div>
                Manage Seats
            </a>
            <a href="view-reservations.php" class="card-link">
                <div class="card-icon">📅</div>
                View Reservations
            </a>
            <a href="assign-seats.php" class="card-link">
                <div class="card-icon">🖊️</div>
                Assign Seat
            </a>
        </div>

        <!-- Second row -->
        <div class="bottom-row">
            <a href="reports.php" class="card-link">
                <div class="card-icon">📊</div>
                Reports
            </a>
            <a href="log-out.php" class="card-link">
                <div class="card-icon">🚪</div>
                Logout
            </a>
        </div>
    </div>

    <script>
        document.querySelector('a[href="logout.php"]').addEventListener('click', function (e) {
            if (!confirm("Are you sure you want to logout?")) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>