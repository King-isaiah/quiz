<?php
    session_start();
    // if(!isset($_SESSION["end_time"])){
    //     echo "00:00:00:00";
    // }else{
    //     $time1=gmdate("H:i:s",strtotime($_SESSION["end_time"]) - strtotime(date("Y-m-d H:i:s")));
    //     if(strtotime($_SESSION["end_time"]) > strtotime(date("Y-m-d H:i:s"))){
           

    //         echo $time1;
    //     }
    //     else{
    //         echo "00:00:00:00";
    //     }
    // }



// the bellow are experiments 
    // Countdown Logic
    if (!isset($_SESSION["end_time"])) {
        echo "00:00:00:000"; // No countdown available
    } else {
        $endTime = strtotime($_SESSION["end_time"]);
        $currentTime = microtime(true); // Get current timestamp with microseconds
    
        // Calculate remaining time in seconds
        $remainingTime = $endTime - $currentTime;
    
        if ($remainingTime > 0) {
            // Calculate hours, minutes, seconds, and milliseconds
            $hours = floor($remainingTime / 3600);
            $minutes = floor(($remainingTime % 3600) / 60);
            $seconds = floor($remainingTime % 60);          
            
            $milliseconds = ($remainingTime - floor($remainingTime)) * 1000; 
    
            // Format output
            // echo sprintf('%02d:%02d:%02d.%03d', $hours, $minutes, $seconds, round($milliseconds));
            echo sprintf('%02d:%02d:%02d.%03d', $hours, $minutes, $seconds, round($milliseconds));
            
        } else {
            // Countdown has reached zero
            echo "00:00:01:000"; // Display countdown complete
            // unset($_SESSION["end_time"]); // Optionally clear end_time if needed
        }
    }
?>