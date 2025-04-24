<?php

session_start();    // Start session if not already started
session_unset();    // Remove all session variables
session_destroy();  // Destroy the session

header("Location: index.php");    // Redirect to home page
exit();    // Stop script execution


?>