<?php
//  Start session at the very top
session_start();

include("../includes/connect.php");
$db = new database();
$conn = $db->connect();

//  Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $terms = isset($_POST['terms']) ? 1 : 0;

    if (!$terms) {
        echo "<script>alert('You must agree to the Terms & Conditions'); window.history.back();</script>";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match'); window.history.back();</script>";
        exit;
    }

    if (strlen($name) < 3 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        echo "<script>alert('Invalid input'); window.history.back();</script>";
        exit;
    }

    //  Check if email already exists
    $sqlCheck = "SELECT id FROM users WHERE email = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $email);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        echo "<script>alert('This email is already registered. Please login instead.'); window.history.back();</script>";
        exit;
    }
    $stmtCheck->close();

    // hashed password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (name, email, mobile, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql) or die(mysqli_error($conn));
    $stmt->bind_param("ssss", $name, $email, $mobile, $hashedPassword);

    if ($stmt->execute()) {
        echo "<div id='successAlert'>
                Registration successful! Please login.
              </div>
              <script>
                setTimeout(function(){
                    window.location.href = 'index.php';
                }, 1000);
              </script>";
        exit;
    } else {
        echo "<script>
            alert('Error: " . addslashes($stmt->error) . "');
            window.history.back();
          </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Account</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
    /* Body background only */
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #26667F, #DDF4E7);
        margin: 0;
    }

    /* Wrapper to center form below header */
    .content-wrapper {
        display: flex;
        justify-content: center;
        padding-top: 80px;
        /* space for fixed header */
        padding-bottom: 50px;
    }

    /* Form Container */
    .register-box {
        background: #fff;
        padding: 40px 35px;
        border-radius: 12px;
        width: 400px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    .register-box h2 {
        color: #26667F;
        margin-bottom: 30px;
        font-weight: 600;
    }

    /* Form Fields */
    .register-box form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .register-box .input-group {
        position: relative;
    }

    .register-box input {
        width: 85%;
        padding: 12px 40px 12px 10px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 15px;
        transition: 0.3s;
    }

    .register-box input:focus {
        border-color: #26667F;
        outline: none;
        box-shadow: 0 0 5px rgba(0, 115, 230, 0.3);
    }

    .register-box .input-group i {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #888;
    }

    .register-box .terms {
        font-size: 16px;
        text-align: left;
        margin-right: 10px;
    }

    .register-box .terms input {
        width: auto;
        margin-right: 5px;
        vertical-align: middle;
    }

    .register-box p.error {
        color: red;
        font-size: 13px;
        margin: 0;
        text-align: center;
    }

    .register-box button {
        background: #26667F;
        color: #fff;
        padding: 12px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
    }

    .register-box button:hover {
        background: #124170;
    }

    .register-box a {
        font-size: 14px;
        color: #26667F;
        text-decoration: none;
        display: block;
        margin-top: 12px;
    }

    #successAlert {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: #4CAF50;
        color: #fff;
        padding: 10px 20px;
        border-radius: 5px;
        font-family: Arial, sans-serif;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 9999;
    }

    @media (max-width: 450px) {
        .register-box {
            width: 90%;
            padding: 30px 20px;
        }

        .register-box input {
            width: 100%;
        }
    }
    </style>
</head>

<body>

    <!--  Include header.php -->
    <?php include "header.php"; ?>

    <!-- Form Content -->
    <div class="content-wrapper">
        <div class="register-box">
            <h2>Create Your Seat Reservations Account</h2>
            <form id="registerForm" action="register.php" method="POST" onsubmit="return validateForm()">
                <div class="input-group">
                    <input type="text" name="name" id="name" placeholder="Full Name" required>
                    <i class="fa fa-user"></i>
                </div>
                <div class="input-group">
                    <input type="email" name="email" id="email" placeholder="Email" required>
                    <i class="fa fa-envelope"></i>
                </div>
                <div class="input-group">
                    <input type="text" name="mobile" id="mobile" placeholder="Phone Number" required>
                    <i class="fa fa-phone"></i>
                </div>
                <div class="input-group">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <i class="fa fa-eye" onclick="togglePassword('password', this)"></i>
                </div>
                <div class="input-group">
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password"
                        required>
                    <i class="fa fa-eye" onclick="togglePassword('confirm_password', this)"></i>
                </div>
                <div class="terms">
                    <label><input type="checkbox" name="terms" required> I agree to <a href="#">Terms &
                            Conditions</a></label>
                </div>
                <p id="errorMsg" class="error"></p>
                <button type="submit">Register</button>
            </form>
            <a href="index.php">Already have an account? Login here</a>
        </div>
    </div>

    <script>
    function togglePassword(id, icon) {
        const input = document.getElementById(id);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }

    function validateForm() {
        const name = document.getElementById("name").value.trim();
        const email = document.getElementById("email").value.trim();
        const mobile = document.getElementById("mobile").value.trim();
        const password = document.getElementById("password").value.trim();
        const confirmPassword = document.getElementById("confirm_password").value.trim();
        const errorMsg = document.getElementById("errorMsg");

        if (name.length < 3) {
            errorMsg.textContent = "Full name must be at least 3 characters.";
            return false;
        }
        if (!email.includes("@") || !email.includes(".")) {
            errorMsg.textContent = "Enter a valid email.";
            return false;
        }
        if (mobile.length < 10) {
            errorMsg.textContent = "Enter a valid phone number.";
            return false;
        }
        if (password.length < 6) {
            errorMsg.textContent = "Password must be at least 6 characters.";
            return false;
        }
        if (password !== confirmPassword) {
            errorMsg.textContent = "Passwords do not match.";
            return false;
        }
        errorMsg.textContent = "";
        return true;
    }
    </script>

</body>

</html>