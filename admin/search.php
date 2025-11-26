<?php
    // include "../connection.php";

    $searchTerm = isset($_GET['query']) ? mysqli_real_escape_string($link, $_GET['query']) : '';

    $query = "SELECT * FROM registration WHERE username LIKE '%$searchTerm%' ORDER BY id DESC";
    $res = mysqli_query($link, $query);

    $results = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $results[] = $row;
    }

    echo json_encode($results);
?>