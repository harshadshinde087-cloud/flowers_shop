<?php
// Standalone logout page - no dependencies
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to home page
header("Location: index_simple.php");
exit();
?>
