<?php
session_start(); // Start the session

// Destroy all session data
session_destroy(); 

// Redirect the user to the login page
header("Location: ../public/login_index.php"); 
exit(); // Ensure no further code is executed
?>