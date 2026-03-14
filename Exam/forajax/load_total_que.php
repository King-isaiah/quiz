<?php 
    session_start();
    // include "../connection.php";
    include "../../superbase/config.php";
    $total_que=0;
    
    // Comment out local MySQL connection and replace with Supabase
    /*
    $res1=mysqli_query($link,"select * from questions where 
    category='$_SESSION[exam_category]'");
    $total_que=mysqli_num_rows($res1);
    */
    
    // Supabase equivalent
    $response = fetchData('questions?category=eq.' . urlencode($_SESSION['exam_category']));
    $total_que = is_array($response) ? count($response) : 0;
    
    echo $total_que;
?>