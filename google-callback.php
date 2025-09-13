<?php
require_once 'vendor/autoload.php';
include("includes/connect.php");
session_start();

$client = new Google_Client();
$clientID = getenv('GOOGLE_CLIENT_ID');
$clientSecret = getenv('GOOGLE_CLIENT_SECRET');

$client->setRedirectUri("http://localhost/seat_reservation_system/google-callback.php");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $oauth = new Google_Service_Oauth2($client);
    $google_user = $oauth->userinfo->get();

    $email = $google_user->email;
    $name = $google_user->name;

    $conn = (new database())->connect();

    // Check if user exists
    $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
    } else {
        // Insert new user if not exists
        $query = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '')";
        mysqli_query($conn, $query);
        $user_id = mysqli_insert_id($conn);
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
    }

    header("Location: dashboard.php");
    exit;
} else {
    echo "Login failed!";
}