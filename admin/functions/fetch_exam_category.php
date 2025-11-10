
<?php
// include "../../connection.php";

// $res = mysqli_query($link, "SELECT * FROM exam_category ORDER BY id DESC");
// $data = [];

// while ($row = mysqli_fetch_assoc($res)) {
//     $data[] = [
//         'category' => $row['category'],
//         'year' => $row['year'],
//         'exam_time_in_minutes' => $row['exam_time_in_minutes'],
//         'price' => $row['price'],
//         'book_cover' => $row['book_cover'],
//         'id' => $row['id'], // Store id directly
//     ];
// }

// header('Content-Type: application/json');
// header('X-Content-Type-Options: nosniff');
// echo json_encode($data);
?>

<?php
include "../../superbase/config.php";

// Fetch all exam categories from Supabase sorted by ID descending
$response = fetchData('exam_category?order=id.desc');

$data = [];

if (is_array($response) && !isset($response['error'])) {
    foreach ($response as $row) {
        $data[] = [
            'category' => $row['category'] ?? '',
            'year' => $row['year'] ?? '',
            'exam_time_in_minutes' => $row['exam_time_in_minutes'] ?? '',
            'price' => $row['price'] ?? '',
            'book_cover' => $row['book_cover'] ?? '',
            'id' => $row['id'] ?? '',
        ];
    }
}

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
echo json_encode($data);
?> 


