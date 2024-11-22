<?php    
 
    // All project functions should be placed here
    function con(){
        
        $conn = mysqli_connect("localhost", "root", "", "dct-ccs-finals");
        
        if($conn === false){
            die("Error: Could not connect " .  mysqli_connect_error());
        }

        return $conn;
    }
    // sanitize input
    function sanitize_input($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }