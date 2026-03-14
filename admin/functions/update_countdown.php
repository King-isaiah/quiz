<?php
    // include "../../connection.php";

    // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //     $selectedId = $_POST['selected_id'];
    //     $selectedCategory = $_POST['selected_category'];

    //     $countSql = "SELECT COUNT(*) as count FROM exam_category WHERE countDown = 'active'";
    //     $countResult = $link->query($countSql);
    //     $countRow = $countResult->fetch_assoc();
    //     $count = $countRow['count'];

    // if ($count <= 0) {
    //         // Update selected category
    //         $updateSql = "UPDATE exam_category SET countDown = 'active' WHERE id = ?";
    //         $stmt = $link->prepare($updateSql);
    //         $stmt->bind_param("i", $selectedId);
    //         $stmt->execute();
    //         $stmt->close();
    //     } else {
    //         // Update currently active category to inactive
    //         $inactiveSql = "UPDATE exam_category SET countDown = 'inactive' WHERE countDown = 'active'";
    //         $link->query($inactiveSql);

    //         // Update the selected category to active
    //         $updateSql = "UPDATE exam_category SET countDown = 'active' WHERE id = ?";
    //         $stmt = $link->prepare($updateSql);
    //         $stmt->bind_param("i", $selectedId);
    //         $stmt->execute();
    //         $stmt->close();
    //     }

    //     echo $selectedCategory . " Countdown updated successfully.";
    // } else {
    //     echo "Invalid request.";
    // }
?>
<?php
    include "../../superbase/config.php";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $selectedId = $_POST['selected_id'];
        $selectedCategory = $_POST['selected_category'];

        // Check if there's any active countdown using Supabase
        $response = fetchData('exam_category?countDown=eq.active');
        
        if (isset($response['error'])) {
            echo "Database error: " . $response['error'];
            exit;
        }

        $count = is_array($response) ? count($response) : 0;

        if ($count <= 0) {
            // Update selected category to active using Supabase
            $updateData = ['countDown' => 'active'];
            $result = updateData('exam_category', $selectedId, $updateData);
            
            if (isset($result['error'])) {
                echo "Error updating category: " . $result['error'];
            } else {
                echo $selectedCategory . " Countdown activated successfully.";
            }
        } else {
            // Get the currently active category ID
            $activeCategoryId = $response[0]['id'];
            
            // Update currently active category to inactive using Supabase
            $inactiveData = ['countDown' => 'inactive'];
            $inactiveResult = updateData('exam_category', $activeCategoryId, $inactiveData);
            
            if (isset($inactiveResult['error'])) {
                echo "Error deactivating current category: " . $inactiveResult['error'];
                exit;
            }

            // Update the selected category to active using Supabase
            $updateData = ['countDown' => 'active'];
            $result = updateData('exam_category', $selectedId, $updateData);
            
            if (isset($result['error'])) {
                echo "Error activating new category: " . $result['error'];
            } else {
                echo $selectedCategory . " Countdown updated successfully.";
            }
        }
    } else {
        echo "Invalid request.";
    }
?>