<?php 

ob_start(); 
header('Content-Type: application/json'); 

session_start();
// include_once "../../connection.php"; 
include_once "../../superbase/config.php";

$response = []; 

// Check if required POST data exists
// if (!isset($_POST['$uname']) || !isset($_POST['lname'])) {
//     $response['success'] = false;
//     $response['message'] = "Dont fuck with me Required fields are missing";
//     echo json_encode($response);
//     exit;
// }

// Get POST data with proper validation
$uname = isset($_POST['username']) ? $_POST['username'] : '';
$lname = $_POST['lname'];
$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
$email = $_POST['email'];
$password = isset($_POST['password']) ? $_POST['password'] : '';
$contact = isset($_POST['phone_number']) ? $_POST['phone_number'] : '';



if (!empty($lname) && !empty($uname )) {    
    
        $emailCheck = fetchData('registration?email=eq.' . urlencode($email));
        if (count($emailCheck) > 0) { 
            $response['success'] = false;
            $response['message'] = "$email - this email already exists";
        } else {
            // Check if username exists in Supabase
            $usernameCheck = fetchData('registration?username=eq.' . urlencode($uname)); 
            if (count($usernameCheck) > 1) { 
                $response['success'] = false;
                $response['message'] = "$uname - this Username already exists, please use another";
            } else {
                if (isset($_FILES['image'])) {  
                    $img_name = $_FILES['image']['name']; 
                    $tmp_name = $_FILES['image']['tmp_name']; 

                    $img_explode = explode('.', $img_name);
                    $img_ext = end($img_explode); 

                    $extensions = ['png', 'jpeg', 'jpg']; 
                    $time = time();
                    $new_img_name = $time . $img_name;
                    
                    // if (move_uploaded_file($tmp_name, "../../lumers/php/images/$new_img_name")) {                              
                    //     $unique = $_SESSION['unique_id'];
                    //     $hash = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : '';

                        
                    //     $data = [
                    //         'username' => $uname,
                    //         'lastname' => $lname,
                    //         'gender' => $gender,
                    //         'contact' => $contact,
                    //         'email' => $email,
                           
                    //     ];
                        
                    //     // Only update password if provided
                    //     if (!empty($hash) && !empty($new_img_name)) {
                    //         $data['img'] = $new_img_name;
                    //         $data['password'] = $hash;
                    //     }

                    //     // Update the registration details in Supabase
                    //     // $updateResponse = updateDataUniquetId('registration', $unique, $data);
                    //     $updateResponse = updateDataWithoutId('registration', $unique, $data, 'unique_id'); 
                    //     if (isset($updateResponse['error'])) {
                    //         $response['success'] = false;
                    //         $response['message'] = "Something went wrong during update: " . $updateResponse['error'];
                    //     } else {
                    //         $response['success'] = true;
                    //         $response['message'] = "Profile updated successfully!";
                    //     }
                    // } else {
                    //     $response['success'] = false;
                    //     $response['message'] = "Failed to upload image file!";
                    // }
                    $unique = $_SESSION['unique_id'];
                    $hash = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : '';

                    $data = [
                        'username' => $uname,
                        'lastname' => $lname,
                        'gender' => $gender,
                        'contact' => $contact,
                        // 'email' => $email,
                    ];

                    // Handle password if provided
                    if (!empty($hash)) {
                        $data['password'] = $hash;
                    }

                    // Handle image ONLY if user selected one
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $img_name = $_FILES['image']['name']; 
                        $tmp_name = $_FILES['image']['tmp_name']; 
                        
                        $img_explode = explode('.', $img_name);
                        $img_ext = end($img_explode); 
                        
                        $extensions = ['png', 'jpeg', 'jpg']; 
                        $time = time();
                        $new_img_name = $time . $img_name;
                        
                        if (move_uploaded_file($tmp_name, "../../lumers/php/images/$new_img_name")) {
                            $data['img'] = $new_img_name;  // Add image to update data
                        } else {
                            $response['success'] = false;
                            $response['message'] = "Failed to upload image file!";
                            echo json_encode($response);
                            exit;
                        }
                    }

                    // Now update with the final data (with or without image)
                    $updateResponse = updateDataWithoutId('registration', $unique, $data, 'unique_id'); 
                    if (isset($updateResponse['error'])) {
                        $response['success'] = false;
                        $response['message'] = "Something went wrong during update: " . $updateResponse['error'];
                    } else {
                        $response['success'] = true;
                        $response['message'] = "Profile updated successfully!";
                    }
                } else {
                    $response['success'] = false;
                    $response['message'] = "Please select an Image file.";
                }
            }          
        }
    
} else {
    $response['success'] = false;
    $response['message'] = "Last name and email fields are required!";
}

// Clear any buffered output and send JSON
ob_end_clean();
echo json_encode($response);
exit;
?>