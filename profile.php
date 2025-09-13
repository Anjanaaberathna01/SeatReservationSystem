<?php
session_start();
include("includes/connect.php");

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$db = new database();
$conn = $db->connect();

$user_id = $_SESSION['user_id'];
$message = "";

// Fetch current user data
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update profile if form submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $university = $_POST['university'];
    $location = $_POST['location'];
    $language = $_POST['language'];

    $update = "UPDATE users SET name=?, mobile=?, university=?, location=?, language=? WHERE id=?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("sssssi", $name, $mobile, $university, $location, $language, $user_id);

    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
        $_SESSION['user_name'] = $name;
    } else {
        $message = "Error updating profile: " . $stmt->error;
    }
}

// Handle password change
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    // Check current password
    $check = $conn->prepare("SELECT password FROM users WHERE id=?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $res = $check->get_result();
    $row = $res->fetch_assoc();

    if (password_verify($current_password, $row['password'])) {
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_pass = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $update_pass->bind_param("si", $hashed_new_password, $user_id);
        if ($update_pass->execute()) {
            $message = "Password updated successfully!";
        } else {
            $message = "Error updating password.";
        }
    } else {
        $message = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #DDF4E7;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .profile-heading {
            text-align: left;
            margin-left: 250px;
            margin-bottom: 20px;
        }

        .profile-container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 20px;
            max-width: 1000px;
            padding: 0 20px;
        }

        /* Left Profile Box */
        .profile-box {
            flex: 2;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            min-width: 400px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .profile-box h2 {
            text-align: left;
            margin-bottom: 20px;
            color: #333;
        }

        .profile-box label {
            font-weight: 500;
            display: block;
            margin: 10px 0 5px;
            color: #555;
        }

        .profile-box input {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .profile-box input[readonly] {
            background: #eee;
            cursor: not-allowed;
        }

        .profile-box button {
            width: 100%;
            padding: 10px;
            background: #26667F;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .profile-box button:hover {
            background: #124170;
        }

        .message {
            text-align: left;
            margin-bottom: 15px;
            color: green;
            font-weight: bold;
        }

        /* Right Side Rows */
        .profile-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .side-row {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            font-size: 14px;
            color: #444;
            min-width: 250px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .side-row:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .profile-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 20px;
        }

        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid #124170;
            margin-bottom: 10px;
        }

        .profile-info h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .profile-info p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }

        /* Password Form in Side Row */
        .side-row form {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .side-row input {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .side-row button {
            padding: 8px;
            background: #26667F;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .side-row button:hover {
            background: #124170;
        }

        /* Active / Inactive */
        .active {
            color: green;
            font-weight: bold;
        }

        .inactive {
            color: red;
            font-weight: bold;
        }

        /* Responsive */
        @media (max-width: 800px) {
            .profile-container {
                flex-direction: column;
            }

            .profile-side {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .side-row {
                flex: 1 1 45%;
            }
        }

        @media (max-width: 500px) {
            .side-row {
                flex: 1 1 100%;
            }
        }
    </style>
</head>

<body>
    <?php include("header.php"); ?>

    <div class="profile-heading">
        <h1>Your Profile</h1>
        <p>View and manage your personal information</p>
    </div>

    <div class="container">
        <div class="profile-container">

            <!-- Left Column: Profile Form -->
            <div class="profile-box">
                <h2>Personal Information</h2>
                <?php if ($message)
                    echo "<p class='message'>$message</p>"; ?>
                <form method="POST">
                    <input type="hidden" name="update_profile">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

                    <label for="email">Email (cannot change)</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>

                    <label for="mobile">Mobile Number</label>
                    <input type="text" name="mobile" value="<?php echo htmlspecialchars($user['mobile']); ?>">

                    <label for="university">University</label>
                    <input type="text" name="university" value="<?php echo htmlspecialchars($user['university']); ?>">

                    <label for="location">Location</label>
                    <input type="text" name="location" value="<?php echo htmlspecialchars($user['location']); ?>">

                    <label for="language">Language</label>
                    <input type="text" name="language" value="<?php echo htmlspecialchars($user['language']); ?>">

                    <button type="submit">Update Profile</button>
                </form>
            </div>

            <!-- Right Column: Side Rows -->
            <div class="profile-side">

                <!-- Profile Card -->
                <div class="side-row profile-card">
                    <img src="https://icons.veryicon.com/png/o/business/multi-color-financial-and-business-icons/user-139.png"
                        alt="User Photo" class="profile-pic">
                    <div class="profile-info">
                        <h3><?php echo htmlspecialchars($_SESSION['user_name']); ?></h3>
                        <p>📍 Location: <?php echo htmlspecialchars($user['location']); ?></p>
                    </div>
                </div>

                <!-- Registration Info & Password Change -->
                <div class="side-row">
                    📅 <strong>Registered on:</strong>
                    <?php echo date("F j, Y", strtotime($user['created_at'])); ?><br><br>
                    <strong>User Role:</strong> <?php echo htmlspecialchars($user['role']); ?><br><br>
                    🟢 <strong>Status:</strong>
                    <?php echo ($user['status'] === 'Active') ? '<span class="active">Active</span>' : '<span class="inactive">Inactive</span>'; ?><br>

                    <h4>Change Password</h4>
                    <form method="POST">
                        <input type="password" name="current_password" placeholder="Current Password" required>
                        <input type="password" name="new_password" placeholder="New Password" required>
                        <button type="submit" name="change_password">Update Password</button>
                    </form>
                </div>

                <!-- Next Step -->
                <div class="side-row">
                    🎯 <strong>Next Step:</strong>
                    <a href="../reservation/my-reservations.php"
                        style="text-decoration:none; color:#333; font-weight:bold;">
                        View your seat reservation today!
                    </a>
                </div>

            </div>
        </div>
    </div>

</body>

</html>