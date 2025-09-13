<?php
session_start();
include("includes/connect.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("⚠️ You must be logged in to access this page.");
}

$user_id = intval($_SESSION['user_id']);

$db = new database();
$conn = $db->connect();

// Fetch user's registered email from your table
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("⚠️ User not found.");
}
$user = $result->fetch_assoc();
$registeredEmail = $user['email']; // <-- This is the email to send OTP

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer-master/src/Exception.php';
require 'PHPMailer/PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer/PHPMailer-master/src/SMTP.php';

$otpMessage = "";
$otpVerified = false;

// Generate and send OTP
if (isset($_POST['send_otp'])) {
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_email'] = $registeredEmail;
    $_SESSION['otp_expire'] = time() + 300; // 5 min

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; // your Gmail
        $mail->Password = 'your-app-password';    // 16-char app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', 'Seat Reservation System');
        $mail->addAddress($registeredEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body = "Your OTP code is: <b>$otp</b>. It is valid for 5 minutes.";

        $mail->send();
        $otpMessage = "✅ OTP has been sent to your registered email: $registeredEmail";
    } catch (Exception $e) {
        $otpMessage = "❌ Failed to send OTP. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Verify OTP
if (isset($_POST['verify_otp'])) {
    $userOtp = trim($_POST['otp']);
    if (isset($_SESSION['otp']) && isset($_SESSION['otp_expire'])) {
        if (time() > $_SESSION['otp_expire']) {
            $otpMessage = "❌ OTP expired. Request again.";
        } elseif ($userOtp == $_SESSION['otp']) {
            $otpMessage = "✅ OTP verified successfully!";
            $otpVerified = true;
        } else {
            $otpMessage = "❌ Invalid OTP.";
        }
    } else {
        $otpMessage = "❌ No OTP found. Please request OTP first.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password - OTP</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #DDF4E7;
            padding: 20px;
        }

        .container {
            max-width: 400px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #124170;
        }

        input[type=email],
        input[type=text],
        button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        input[readonly] {
            background: #e0e0e0;
            cursor: not-allowed;
        }

        button {
            background: #26667F;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #1f5566;
        }

        .message {
            text-align: center;
            margin-top: 10px;
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Forgot Password</h2>

        <form method="POST">
            <label>Registered Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($registeredEmail) ?>" readonly>
            <button type="submit" name="send_otp">Send OTP</button>
        </form>

        <form method="POST">
            <label>Enter OTP</label>
            <input type="text" name="otp" placeholder="Enter OTP sent to your email" required>
            <button type="submit" name="verify_otp">Verify OTP</button>
        </form>

        <?php if ($otpMessage) { ?>
            <div class="message"><?= $otpMessage ?></div>
        <?php } ?>

        <?php if ($otpVerified) { ?>
            <p style="text-align:center; color:green;">You can now <a href="reset_password.php">reset your password</a>.</p>
        <?php } ?>
    </div>
</body>

</html>