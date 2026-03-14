<?php
session_start();
if (isset($_POST['timerValue'])) {
    $_SESSION['current_timer'] = $_POST['timerValue']; // Store the timer value in a session variable
}
?>