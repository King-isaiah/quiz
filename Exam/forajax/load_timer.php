<?php
session_start();

if (!isset($_SESSION["end_time"])) {
    echo "00:00:00:000";
} else {
    $endTime = strtotime($_SESSION["end_time"]);
    $currentTime = microtime(true);
    $remainingTime = $endTime - $currentTime;

    if ($remainingTime > 0) {
        // Use intval() to safely convert to integers
        $hours = floor(intval($remainingTime) / 3600);
        $minutes = floor((intval($remainingTime) % 3600) / 60);
        $seconds = floor(intval($remainingTime) % 60);
        
        // Handle milliseconds separately
        $milliseconds = ($remainingTime - floor($remainingTime)) * 1000;
        $milliseconds = round($milliseconds);

        echo sprintf('%02d:%02d:%02d.%03d', $hours, $minutes, $seconds, $milliseconds);
    } else {
        echo "00:00:00:000";
    }
}
?>