<?php
session_start();
include("../includes/connect.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $db = new database();
    $conn = $db->connect();

    $name = $_POST['admin_name'];
    $pass = $_POST['password'];

    // ✅ fetch only by username (not password)
    $stmt = $conn->prepare("SELECT * FROM admins WHERE admin_name=? LIMIT 1");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // ✅ check hashed password
        if (password_verify($pass, $row['password'])) {
            $_SESSION['admin_name'] = $name;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "❌ Invalid credentials";
        }
    } else {
        $error = "❌ Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background: linear-gradient(135deg, #5499cdff, #eef3f7);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* Header */
    .header {
        width: 100%;
    }

    .container {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .box {
        background: white;
        padding: 40px 30px;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 400px;
        text-align: center;
    }

    .box h2 {
        margin-bottom: 25px;
        color: #124170;
    }

    /* Form styling */
    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
        align-items: center;
    }

    input,
    button {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        font-size: 16px;
        padding: 12px;
        border-radius: 8px;
    }

    input {
        border: 1px solid #ccc;
    }

    #submit {
        background: #26667F;
        color: white;
        border: none;
        cursor: pointer;
        transition: background 0.3s, transform 0.2s;
    }

    #submit:hover {
        background: #124170;
        transform: scale(1.03);
    }

    #reset {
        background: #df3d3dff;
        color: white;
        border: none;
        cursor: pointer;
        transition: background 0.3s, transform 0.2s;
    }

    #reset:hover {
        background: #e75858ff;
        transform: scale(1.03);
    }

    .error {
        color: red;
        margin-bottom: 10px;
        text-align: center;
    }

    @media (max-width: 500px) {
        .box {
            padding: 30px 20px;
        }

        input,
        button {
            font-size: 14px;
            padding: 10px;
        }
    }
    </style>
</head>

<body>
    <!-- Include admin header -->
    <?php include("admin-header.php"); ?>

    <div class="container">
        <div class="box">
            <h2>Admin Login</h2>
            <?php if (!empty($error))
                echo "<div class='error'>$error</div>"; ?>
            <form method="post">
                <input type="text" name="admin_name" placeholder="Admin Name" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" id="submit">Login</button>
                <button type="reset" id="reset" onclick="window.location.href='../log/index.php'">Cancel</button>
            </form>
        </div>
    </div>
</body>

</html>