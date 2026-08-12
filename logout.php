<?php
session_start();

// Clear session
$_SESSION = [];
session_destroy();

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// ✅ Clear Remember Me cookie
if (isset($_COOKIE['remember_user'])) {
    setcookie("remember_user", "", time() - 3600, "/");
}

// Optional: add logout message
session_start();
$_SESSION['logout_msg'] = "You have been logged out successfully.";

// Redirect back to homepage
header("Location: index.php");
exit;
?>
