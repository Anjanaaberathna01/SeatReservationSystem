<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Header -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;400&display=swap');

body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
}

#blue_bar {
    background-color: #124170;
    padding: 10px 0;
}

#blue_bar .blue_container {
    width: 90%;
    max-width: 1000px;
    margin: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    position: relative;
}

#blue_bar h1 {
    color: white;
    font-size: 28px;
    margin: 0;
}

#blue_bar h1 a {
    color: white;
    text-decoration: none;
}

#blue_bar .nav-links {
    display: flex;
    align-items: center;
    gap: 15px;
    position: relative;
}

#blue_bar .nav-links a {
    color: white;
    text-decoration: none;
    font-size: 14px;
    padding: 5px 10px;
    border-radius: 4px;
    transition: background 0.3s;
    cursor: pointer;
}

#blue_bar .nav-links a:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

/* Disabled link */
#blue_bar .nav-links a.disabled {
    opacity: 0.5;
    pointer-events: none;
    cursor: default;
}

/* 🔹 Confirmation Box */
.logout-confirm {
    display: none;
    position: absolute;
    top: 40px;
    right: 0;
    background: white;
    color: #333;
    border: 1px solid #ccc;
    border-radius: 6px;
    padding: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    z-index: 100;
    font-size: 14px;
}

.logout-confirm p {
    margin: 0 0 8px 0;
    font-weight: 500;
}

.logout-confirm button {
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin: 0 4px;
    font-size: 13px;
}

.logout-confirm .yes-btn {
    background: #e74c3c;
    color: white;
}

.logout-confirm .yes-btn:hover {
    background: #c0392b;
}

.logout-confirm .no-btn {
    background: #ccc;
    color: black;
}

.logout-confirm .no-btn:hover {
    background: #aaa;
}

@media screen and (max-width: 500px) {
    #blue_bar .blue_container {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}
</style>

<div id="blue_bar">
    <div class="blue_container">
        <!-- Left: Website title -->
        <h1>
            <a href="dashboard.php">Seat Reservation</a>
        </h1>

        <!-- Right: Navigation -->
        <div class="nav-links">
            <?php if (isset($_SESSION['user_email'])): ?>
            <!-- Logged-in user -->
            <a href="dashboard.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="#" id="logout-link">Log Out</a>

            <!-- Confirmation Box -->
            <div class="logout-confirm" id="logout-confirm">
                <p>Are you sure?</p>
                <button class="yes-btn" onclick="window.location.href='logout.php'">Yes</button>
                <button class="no-btn" onclick="hideLogoutConfirm()">No</button>
            </div>
            <?php else: ?>
            <!-- Before login: Home disabled -->
            <a href="#" class="disabled">Home</a>
            <a href="index.php">Log In</a>
            <a href="register.php">Register</a>
            <a href="admin/index.php">Admin</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
const logoutLink = document.getElementById("logout-link");
const logoutConfirm = document.getElementById("logout-confirm");

if (logoutLink) {
    logoutLink.addEventListener("click", function(e) {
        e.preventDefault();
        logoutConfirm.style.display = "block";
    });
}

function hideLogoutConfirm() {
    logoutConfirm.style.display = "none";
}

// Close confirmation if clicked outside
document.addEventListener("click", function(event) {
    if (logoutConfirm && !logoutConfirm.contains(event.target) && event.target !== logoutLink) {
        logoutConfirm.style.display = "none";
    }
});
</script>