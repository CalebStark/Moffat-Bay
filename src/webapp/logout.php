<?php
session_start();  // Start the session to clear data
session_unset();  // Unset all session variables
session_destroy();  // Destroy the session completely
header("Location: login.php");  // Redirect to the login page
exit();
?>