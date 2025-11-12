<?php

    // $link = mysqli_connect("localhost","root","");
    // mysqli_select_db($link,"online_quiz")


    // Use Railway's MySQL environment variables
    $db_host = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST');
    $db_user = getenv('MYSQLUSER') ?: getenv('MYSQL_USERNAME');
    $db_pass = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD'); 
    $db_name = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE');

    // Try different possible variable names Railway might use
    if (!$db_host) $db_host = 'localhost';
    if (!$db_user) $db_user = 'root';
    if (!$db_name) $db_name = 'railway';

    $link = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

    if (!$link) {
        die("Connection failed: " . mysqli_connect_error());
    }

    echo "Connected to MySQL successfully!";

?>