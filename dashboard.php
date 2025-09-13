<?php
session_start();
include("includes/connect.php");
$conn = (new database())->connect();

$user_id = $_SESSION['user_id'];
$query = "SELECT email FROM users WHERE id = '$user_id' LIMIT 1";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    $_SESSION['user_email'] = $row['email'];
}

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;400&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #DDF4E7;
            margin: 0;
            padding: 0;
        }

        /* Dashboard Header */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 100%;
            margin: 30px auto 20px;
            padding: 0 20px;
        }

        .dashboard-header h1 {
            margin: 0;
            font-size: 24px;
            color: black;
        }

        .email-bar {
            background-color: #DDF4E7;
        }

        .user-dropdown {
            position: relative;
            display: inline-block;
        }

        .dropbtn {
            background-color: #DDF4E7;
            color: #333;
            padding: 10px 16px;
            font-size: 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }

        /* Dropdown Content */
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #DDF4E7;
            min-width: 180px;
            border-radius: 8px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            z-index: 1;
            padding: 8px 0;
        }

        .dropdown-content a {
            color: #333;
            padding: 10px 16px;
            text-decoration: none;
            display: block;
            font-size: 14px;
            transition: background 0.3s;
        }

        .dropdown-content a:hover {
            background-color: #67C090;
        }

        /* Show on hover */
        .user-dropdown:hover .dropdown-content {
            display: block;
        }

        /* Creative Logout Style */
        .logout-btn {
            color: #e74c3c !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logout-btn span {
            margin-left: 8px;
        }

        .logout-btn:hover {
            background: #ffeaea;
        }

        /* Dashboard content */
        .dashboard-content {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .dashboard-item {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            padding: 25px 20px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .dashboard-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .item-icon {
            font-size: 50px;
            margin-bottom: 15px;
        }

        .item-icon.seat-icon {
            color: #4CAF50;
        }

        .item-icon.profile-icon {
            color: #0542c5;
        }

        .item-icon.stats-icon {
            color: #f39c12;
        }

        .dashboard-item h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #333;
        }

        .dashboard-item p {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }

        .item-btn {
            display: inline-block;
            padding: 10px 18px;
            background: #26667F;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            transition: background 0.3s;
        }

        .item-btn:hover {
            background: #124170;
        }
    </style>
</head>

<body>
    <?php include("header.php"); ?>

    <div class="dashboard-header">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>

        <?php if (isset($_SESSION['user_email'])): ?>
            <div class="user-dropdown">
                <button class="dropbtn">
                    👤 <?php echo htmlspecialchars($_SESSION['user_email']); ?>
                </button>
                <div class="dropdown-content">
                    <a href="profile.php">👤 Profile</a>
                    <a href="change_password.php">🔑 Change Password</a>
                    <a href="#" class="logout-btn" onclick="confirmLogout(event)">
                        🚪 <span>Log Out</span>
                    </a>
                </div>
            </div>

        <?php else: ?>
            <p style="color: red; font-size: 14px;">⚠️ Email not set</p>
        <?php endif; ?>
    </div>

    <div class="dashboard-content">
        <!-- Seat Reservation Column -->
        <div class="dashboard-item">
            <div class="item-icon seat-icon">🎟️</div>
            <h3>Seat Reservation</h3>
            <p>Reserve your seats easily for events or shows.</p>
            <a href="reservation/reservations.php" class="item-btn">Reserve Now</a>
        </div>

        <!-- Profile Column -->
        <div class="dashboard-item">
            <div class="item-icon profile-icon">👤</div>
            <h3>Profile</h3>
            <p>View and update your personal information.</p>
            <a href="profile.php" class="item-btn">View Profile</a>
        </div>

        <!-- Stats Column -->
        <div class="dashboard-item">
            <div class="item-icon stats-icon">📊</div>
            <h3>Stats</h3>
            <p>Check your reservation history and statistics.</p>
            <a href="reservation/my-reservations.php" class="item-btn">View Stats</a>
        </div>
    </div>

    <script>
        // Logout confirmation
        function confirmLogout(e) {
            e.preventDefault();
            if (confirm("Are you sure you want to log out?")) {
                // Clear session on server (optional: make a logout.php that calls session_destroy)
                window.location.href = "../log/index.php";
            }
        }
    </script>
</body>

</html>