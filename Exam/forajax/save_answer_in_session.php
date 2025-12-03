<?php
    session_start();
    
    // Initialize the answer array if it doesn't exist
    if (!isset($_SESSION["answer"]) || !is_array($_SESSION["answer"])) {
        $_SESSION["answer"] = array();
    }
    
    $questionno = $_GET["questionno"];
    $value1 = $_GET["value1"];
    
    // Store answer
    $_SESSION["answer"][$questionno] = $value1;
?>