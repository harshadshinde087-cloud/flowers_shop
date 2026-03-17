<?php
require_once 'includes/config.php';

// Destroy user session
session_unset();
session_destroy();

// Redirect to home page
redirect('index.php');
?>
