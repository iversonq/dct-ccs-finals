<?php
    // Logout Code Here
    session_start();
    session_destroy(); 

    header("Cache-Control: no-store, no-cache, must-revalidate"); 
    header("Cache-Control: post-check=0, pre-check=0", false); 
    header("Pragma: no-cache");

    header("Location: ../index.php");
exit;
?>