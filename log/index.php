<?php
session_start();
include("../includes/connect.php"); // DB connection
$message = "";

// Google API
require_once '../vendor/autoload.php';

$client = new Google_Client();
$clientID = getenv('GOOGLE_CLIENT_ID');
$clientSecret = getenv('GOOGLE_CLIENT_SECRET');

$client->setRedirectUri("http://localhost/seat_reservation_system/google-callback.php");
$client->addScope("email");
$client->addScope("profile");

$login_url = $client->createAuthUrl();

$fb = new \Facebook\Facebook([
    'app_id' => '3745788745729946',          // Replace with your App ID
    'app_secret' => '20af2363e2957f483bb502e3b6717d00',  // Replace with your App Secret
    'default_graph_version' => 'v19.0',
]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email']; // optional permissions
$loginUrl = $helper->getLoginUrl('https://abcd1234.ngrok.io/seat_reservation_system/facebook-callback.php', $permissions);


//  Normal login
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']) ? "1" : "0";

    if (!empty($email) && !empty($password)) {
        $conn = (new database())->connect();
        $email = mysqli_real_escape_string($conn, $email);

        $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                //  Remember Me
                if ($remember === "1") {
                    setcookie("user_name", $user['name'], time() + (86400 * 30), "/");
                    setcookie("user_email", $user['email'], time() + (86400 * 30), "/");
                }

                header("Location: ../dashboard.php");
                exit;
            } else {
                $message = "Incorrect email or password!";
            }
        } else {
            $message = "Incorrect email or password!";
        }
    } else {
        $message = "Please fill in all fields!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Reservation | Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #26667F, #DDF4E7);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .login-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 360px;
            animation: fadeIn 1s ease-in-out;
            text-align: center;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-box h2 {
            margin-bottom: 15px;
            color: #333;
        }

        .login-box form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .login-box input,
        .login-box button,
        .google-btn {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        .login-box input {
            border: 1px solid #ccc;
            transition: all 0.3s ease;
        }

        .login-box input:focus {
            border-color: #26667F;
            outline: none;
            box-shadow: 0 0 5px rgba(56, 130, 152, 0.5);
        }

        .login-box button {
            border: none;
            background: #26667F;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .login-box button:hover {
            background: #124170;
        }

        /* Google Button */
        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            color: #444;
            border: 1px solid #ccc;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .google-btn img {
            width: 20px;
            margin-right: 10px;
        }

        .google-btn:hover {
            background: #f7f7f7;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        }

        /* Error Message */
        .error {
            color: red;
            margin-bottom: 10px;
        }

        .register-link {
            margin-top: 20px;
            font-size: 14px;
        }

        .register-link a {
            color: #26667F;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Remember Me Styling */
        .remember-me {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #333;
            cursor: pointer;
            user-select: none;
            margin-top: -5px;
        }

        .remember-me input {
            display: none;
            /* hide default checkbox */
        }

        .checkmark {
            width: 18px;
            height: 18px;
            border: 2px solid #26667F;
            border-radius: 4px;
            margin-right: 8px;
            position: relative;
            transition: all 0.3s ease;
        }

        /* Tick when checked */
        .remember-me input:checked+.checkmark {
            background-color: #26667F;
            border-color: #26667F;
        }

        .remember-me input:checked+.checkmark::after {
            content: "✔";
            position: absolute;
            top: -2px;
            left: 2px;
            font-size: 14px;
            color: white;
        }
    </style>
</head>

<body>
    <!-- ✅ Include header -->
    <?php include "header.php"; ?>

    <div class="login-container">
        <div class="login-box">
            <h2>Login to the Seat Reservation</h2>
            <?php if ($message != "") {
                echo "<p class='error'>$message</p>";
            } ?><br>

            <form method="POST" onsubmit="return validateForm();">
                <input type="email" name="email" id="email" placeholder="Email" required>
                <input type="password" name="password" id="password" placeholder="Password" required>

                <!-- Remember Me Checkbox -->
                <label class="remember-me">
                    <input type="checkbox" name="remember" value="1">
                    <span class="checkmark"></span>
                    Remember Me
                </label>

                <button type="submit">Login with Password</button>
            </form>

            <div style="text-align:center; margin-top:15px;">
                <a class="google-btn" href="<?php echo htmlspecialchars($login_url); ?>">
                    <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google logo">
                    Sign in with Google
                </a>
            </div>

            <div style="text-align:center; margin-top:15px;">
                <a class="google-btn" href="<?php echo htmlspecialchars($loginUrl); ?>"
                    style="background:wihte; color:#333; border:2px; border-color: #333;">
                    <img src="https://www.svgrepo.com/show/475647/facebook-color.svg" alt="Facebook logo"
                        style="width:20px; margin-right:10px;">
                    Sign in with Facebook
                </a>
            </div>

            <div class="register-link">
                Don't have an account? <a href="register.php">Register here</a><br>
                <a href="../change_password.php">Forgot Password</a>
            </div>
        </div>
    </div>

    <script>
        function validateForm() {
            let email = document.getElementById("email").value.trim();
            let password = document.getElementById("password").value.trim();

            if (email === "" || password === "") {
                alert("Please fill in both fields!");
                return false;
            }
            if (password.length < 6) {
                alert("Password must be at least 6 characters long!");
                return false;
            }
            return true;
        }
    </script>
</body>

</html>