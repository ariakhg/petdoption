<?php
session_start(); // Start the session

// Unset all session variables
$_SESSION = [];

// Clear all cookies
if (isset($_COOKIE['remember_email'])) {
    setcookie('remember_email', '', time() - 3600, '/');
}
if (isset($_COOKIE['remember_type'])) {
    setcookie('remember_type', '', time() - 3600, '/');
}

// Clear session
session_destroy();

// Redirect to the login page or home page
header("Location: ../login.php");
exit();
?>
