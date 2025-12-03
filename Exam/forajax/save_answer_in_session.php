<?php
    session_start();
    // $questionno=$_GET["questionno"];
    // $value1=$_GET["value1"];
    // $_SESSION["answer"][$questionno]=$value1;

 
    
    // Initialize the answer array if it doesn't exist
    if (!isset($_SESSION["answer"])) {
        $_SESSION["answer"] = array();
    }
    
    $questionno = $_GET["questionno"];
    $value1 = $_GET["value1"];
    
    // Store answer
    $_SESSION["answer"][$questionno] = $value1;
?>