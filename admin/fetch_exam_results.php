<?php
session_start();
// include "../connection.php";
include "../superbase/config.php";

header('Content-Type: application/json');

// Determine which connection to use
$useLocal = isset($_COOKIE['useLocalDB']) && $_COOKIE['useLocalDB'] === 'true';

// Get category filter if provided
$selected_category_id = $_GET['category_id'] ?? '';
$selected_category_name = '';

if ($selected_category_id) {
    if ($useLocal) {
        $res = mysqli_query($link, "SELECT category FROM exam_category WHERE id=$selected_category_id");
        if ($row = mysqli_fetch_array($res)) {
            $selected_category_name = $row['category'];
        }
    } else {
        $response = fetchData("exam_category?id=eq.$selected_category_id");
        if (is_array($response) && !isset($response['error']) && count($response) > 0) {
            $selected_category_name = $response[0]['category'] ?? '';
        }
    }
}

if ($useLocal) {
    // LOCAL MYSQL CONNECTION
    if ($selected_category_name) {
        $query = "SELECT * FROM exam_results WHERE exam_type = '$selected_category_name' ORDER BY id DESC";
    } else {
        $query = "SELECT * FROM exam_results ORDER BY id DESC";
    }
    
    $result = mysqli_query($link, $query);
    $data = [];
    
    while($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    echo json_encode($data);
} else {
    // SUPABASE CONNECTION
    if ($selected_category_name) {
        $response = fetchData("exam_results?exam_type=eq.$selected_category_name&order=id.desc");
    } else {
        $response = fetchData("exam_results?order=id.desc");
    }
    
    if (isset($response['error'])) {
        echo json_encode(['error' => $response['error']]);
    } else {
        echo json_encode($response);
    }
}
?>