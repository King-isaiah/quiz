<?php
    session_start();
    // include "../connection.php";
    include "../../superbase/config.php";
    $exam_category = $_GET["exam_category"];
    $_SESSION["exam_category"]=$exam_category;
    
    // Comment out local MySQL connection and replace with Supabase
    /*
    $res= mysqli_query($link,"SELECT * from exam_category where category='$exam_category'");
    while($row=mysqli_fetch_array($res)){
        $_SESSION["exam_time"]=$row["exam_time_in_minutes"];
    }
    */
    
    // Supabase equivalent
    $response = fetchData('exam_category?category=eq.' . urlencode($exam_category));
    if (is_array($response) && count($response) > 0) {
        $row = $response[0];
        $_SESSION["exam_time"] = $row["exam_time_in_minutes"];
    }
    
    $date=date("Y-m-d H:i:s");
    $_SESSION["end_time"]=date("Y-m-d H:i:s",strtotime($date."+$_SESSION[exam_time] minutes"));
    $_SESSION["exam_start"]="yes";
?>