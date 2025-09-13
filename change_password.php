<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("includes/connect.php"); // Your DB connection file

$message = "";

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['change_password'])) {
    $email = trim($_POST['email']);
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);

    $conn = (new database())->connect();

    // Check if email exists
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify current password
        if (password_verify($current_password, $row['password'])) {
            // Hash new password
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password
            $update = "UPDATE users SET password = ? WHERE email = ?";
            $stmt_update = $conn->prepare($update);
            $stmt_update->bind_param("ss", $hashed_new_password, $email);

            if ($stmt_update->execute()) {
                $message = "<span style='color:green;'>✅ Password updated successfully!</span>";
            } else {
                $message = "<span style='color:red;'>❌ Something went wrong. Try again.</span>";
            }
        } else {
            $message = "<span style='color:red;'>⚠️ Current password is incorrect!</span>";
        }
    } else {
        $message = "<span style='color:red;'>⚠️ No user found with this email!</span>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #26667F, #DDF4E7);
        margin: 0;
        padding: 0;
    }

    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 60px);
        /* account for header height */
    }

    .password-box {
        background: #fff;
        padding: 30px;
        width: 400px;
        border-radius: 12px;
        box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    .password-box h2 {
        margin-bottom: 20px;
        color: #26667F;
    }

    .password-box input {
        width: 100%;
        padding: 12px;
        border-radius: 6px;
        border: 1px solid #ccc;
        margin-bottom: 15px;
        font-size: 14px;
    }

    .password-box button {
        width: 100%;
        padding: 12px;
        background: #26667F;
        color: white;
        font-size: 16px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.3s;
    }

    .password-box button:hover {
        background: #124170;
    }

    .forgot-link {
        display: block;
        margin-top: 12px;
        font-size: 14px;
        color: #26667F;
        text-decoration: none;
    }

    .forgot-link:hover {
        text-decoration: underline;
    }

    .message {
        margin-bottom: 15px;
        font-size: 14px;
    }
    </style>
</head>

<body>
    <?php include("header.php") ?>

    <div class="container">
        <div class="password-box">
            <h2>Change Password</h2>
            <?php if (!empty($message))
                echo "<p class='message'>$message</p>"; ?>

            <form method="POST">
                <input type="email" name="email" placeholder="Registered Email" required>
                <input type="password" name="current_password" placeholder="Current Password" required>
                <input type="password" name="new_password" placeholder="New Password" required>
                <button type="submit" name="change_password">Update Password</button>
            </form>

            <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>
        </div>
    </div>
</body>

</html>