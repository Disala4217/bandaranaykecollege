<?php
session_start();
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session

header('Location: index.php'); // Redirect to the login page
exit();
