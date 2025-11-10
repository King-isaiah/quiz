<?php
// session_start();
// include "../../connection.php";

// $res = mysqli_query($link, "SELECT * FROM registration ORDER BY id DESC");
// $data = [];

// while ($row = mysqli_fetch_assoc($res)) {
//     $data[] = [
//         'username' => $row['username'],
//         'fullName' => $row['firstname'] . ' '. $row['lastname'],
//         'unique_id' => $row['unique_id'],
//         'status' => $row['status']
        
//     ];
// }

// header('Content-Type: application/json');
// header('X-Content-Type-Options: nosniff');
// echo json_encode($data);
?>
<?php
// session_start();
include "../../connection.php";
include "../../superbase/config.php"; // Add Supabase config

// Determine which connection to use
$useLocal = isset($_COOKIE['useLocalDB']) && $_COOKIE['useLocalDB'] === 'true';

if ($useLocal) {
    // LOCAL MYSQL CONNECTION - your existing working code
    $res = mysqli_query($link, "SELECT * FROM registration ORDER BY id DESC");
    $data = [];

    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = [
            'username' => $row['username'],
            'fullName' => $row['firstname'] . ' '. $row['lastname'],
            'unique_id' => $row['unique_id'],
            'status' => $row['status']
        ];
    }

    header('Content-Type: application/json');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($data);
} else {
    // SUPABASE CONNECTION
    $response = fetchData("registration?order=id.desc");
    
    if (isset($response['error'])) {
        http_response_code(500);
        echo json_encode(['error' => 'Supabase error: ' . $response['error']]);
    } else {
        // Format the data to match your expected structure
        $formattedData = [];
        foreach ($response as $row) {
            $formattedData[] = [
                'username' => $row['username'] ?? '',
                'fullName' => ($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''),
                'unique_id' => $row['unique_id'] ?? '',
                'status' => $row['status'] ?? 'active'
            ];
        }
        
        header('Content-Type: application/json');
        header('X-Content-Type-Options: nosniff');
        echo json_encode($formattedData);
    }
}
?>