
<?php 
session_start();
include_once "../../connection.php";

header('Content-Type: application/json'); 

$uname = mysqli_real_escape_string($link, $_POST['username']);    
$lname = mysqli_real_escape_string($link, $_POST['lname']);
$gender = mysqli_real_escape_string($link, $_POST['gender']);
$email = mysqli_real_escape_string($link, $_POST['email']);
$contact = mysqli_real_escape_string($link, $_POST['phone_number']);

$response = []; 


// if (!empty($lname) && !empty($email)) {    
//     if (filter_var($email, FILTER_VALIDATE_EMAIL)) {        
//         $sql = mysqli_query($link, "SELECT email FROM registration WHERE email = '{$email}'");
//         if (mysqli_num_rows($sql) > 1) { 
//             $response['success'] = false;
//             $response['message'] = "$email - this email already exists";
//         } else {
//             $sqlU = mysqli_query($link, "SELECT username FROM registration WHERE username = '{$uname}'"); 
//             if (mysqli_num_rows($sqlU) > 1) { 
//                 $response['success'] = false;
//                 $response['message'] = "$uname - this Username already exists, please use another";
//             } else {
//                 if (isset($_FILES['image'])) {  
//                     $img_name = $_FILES['image']['name']; 
//                     $tmp_name = $_FILES['image']['tmp_name']; 

//                     $img_explode = explode('.', $img_name);
//                     $img_ext = end($img_explode); 

//                     $extensions = ['png', 'jpeg', 'jpg']; 
//                     if (in_array($img_ext, $extensions) === true) { 
//                         $time = time();
//                         $new_img_name = $time.$img_name;
//                         if (move_uploaded_file($tmp_name, "../../lumers/php/images/$new_img_name")) {                              
//                             $unique = $_SESSION['unique_id'];

//                             // Update the registration details
//                             $sql2 = mysqli_query($link, "UPDATE registration SET username = '{$uname}', lastname = '{$lname}', 
//                             gender = '{$gender}', contact = '{$contact}', email = '{$email}', img = '{$new_img_name}' WHERE unique_id ='{$unique}'");
//                             if ($sql2) {  // if data inserted
//                                 $response['success'] = true;
//                                 $response['message'] = "success";
//                             } else {
//                                 $response['success'] = false;
//                                 $response['message'] = "Something went wrong during update";
//                             }
//                         } else {
//                             $response['success'] = false;
//                             $response['message'] = "Failed to upload image file!";
//                         }
//                     } else {
//                         $response['success'] = false;
//                         $response['message'] = "Please select an Image file - jpeg, jpg, png!";
//                     }
//                 } else {
//                     $response['success'] = false;
//                     $response['message'] = "Please select an Image file!";
//                 }
//             }          
//         }
//     } else {
//         $response['success'] = false;
//         $response['message'] = "$email - this is not a valid email";
//     }
// } else {
//     $response['success'] = false;
//     $response['message'] = "All input fields are required!";
// }

if (!empty($lname) && !empty($email)) {    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {        
        // Check if email exists in Supabase
        $emailCheck = fetchData('registration?email=eq.' . urlencode($email));
        if (count($emailCheck) > 0) { 
            $response['success'] = false;
            $response['message'] = "$email - this email already exists";
        } else {
            // Check if username exists in Supabase
            $usernameCheck = fetchData('registration?username=eq.' . urlencode($uname)); 
            if (count($usernameCheck) > 0) { 
                $response['success'] = false;
                $response['message'] = "$uname - this Username already exists, please use another";
            } else {
                if (isset($_FILES['image'])) {  
                    $img_name = $_FILES['image']['name']; 
                    $tmp_name = $_FILES['image']['tmp_name']; 

                    $img_explode = explode('.', $img_name);
                    $img_ext = end($img_explode); 

                    $extensions = ['png', 'jpeg', 'jpg']; 
                    if (in_array($img_ext, $extensions) === true) { 
                        $time = time();
                        $new_img_name = $time . $img_name;
                        if (move_uploaded_file($tmp_name, "../../lumers/php/images/$new_img_name")) {                              
                            $unique = $_SESSION['unique_id'];

                            // Prepare data for update
                            $data = [
                                'username' => $uname,
                                'lastname' => $lname,
                                'gender' => $gender,
                                'contact' => $contact,
                                'email' => $email,
                                'img' => $new_img_name
                            ];

                            // Update the registration details in Supabase
                            $updateResponse = updateData('registration', $unique, $data);
                            if (isset($updateResponse['error'])) {
                                $response['success'] = false;
                                $response['message'] = "Something went wrong during update: " . $updateResponse['error'];
                            } else {
                                $response['success'] = true;
                                $response['message'] = "success";
                            }
                        } else {
                            $response['success'] = false;
                            $response['message'] = "Failed to upload image file!";
                        }
                    } else {
                        $response['success'] = false;
                        $response['message'] = "Please select an Image file - jpeg, jpg, png!";
                    }
                } else {
                    $response['success'] = false;
                    $response['message'] = "Please select an Image file!";
                }
            }          
        }
    } else {
        $response['success'] = false;
        $response['message'] = "$email - this is not a valid email";
    }
} else {
    $response['success'] = false;
    $response['message'] = "All input fields are required!";
}

echo json_encode($response);
?>