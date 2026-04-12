<?php
// Start the session
session_start();

// Destroy all session data (clears login info)
session_destroy();

// Redirect back to login page
header("Location: login.php");
exit();
?>