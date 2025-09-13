<?php
session_start();
require __DIR__ . '../vendor/autoload.php';
include("includes/connect.php"); // DB connection

$fb = new \Facebook\Facebook([
    'app_id' => '3745788745729946',
    'app_secret' => '20af2363e2957f483bb502e3b6717d00',
    'default_graph_version' => 'v19.0',
]);

$helper = $fb->getRedirectLoginHelper();

try {
    $accessToken = $helper->getAccessToken();
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    die('Graph error: ' . $e->getMessage());
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    die('SDK error: ' . $e->getMessage());
}

if (!isset($accessToken)) {
    die("No access token, login failed.");
}

// Store access token in session
$_SESSION['facebook_access_token'] = (string) $accessToken;

try {
    $response = $fb->get('/me?fields=id,name,email', $accessToken);
    $user = $response->getGraphUser();

    $fb_id = $user->getId();
    $name = $user->getName();
    $email = $user->getEmail();

    $conn = (new database())->connect();

    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE facebook_id=? LIMIT 1");
    $stmt->bind_param("s", $fb_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['name'];
        $_SESSION['user_email'] = $row['email'];
    } else {
        // New user, insert into DB
        $stmt = $conn->prepare("INSERT INTO users (name, email, facebook_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $fb_id);
        $stmt->execute();

        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
    }

    // Redirect to dashboard
    header("Location: dashboard.php");
    exit;

} catch (Facebook\Exceptions\FacebookResponseException $e) {
    die('Graph error: ' . $e->getMessage());
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    die('SDK error: ' . $e->getMessage());
}
?>