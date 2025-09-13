<?php

// Clear cookies
setcookie("user_id", "", time() - 3600, "/");
setcookie("user_email", "", time() - 3600, "/");

header("Location: ../log/index.php");
exit;

?>