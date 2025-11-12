<?php
session_start();
// include_once "connection.php";
include_once "../../superbase/config.php";

$email = mysqli_real_escape_string($link, $_POST['email']);
$password = mysqli_real_escape_string($link, $_POST['password']);


// if(!empty($email) && !empty($password)){   
//     $sql = mysqli_query($link, "SELECT * FROM registration WHERE email = '{$email}'");
    
//     if(mysqli_num_rows($sql) > 0){ 
//         $row = mysqli_fetch_assoc($sql);
//         if (!password_verify($password, $row['password'])) {
//             $error_message = 'Invalid password.';
//         }else{
//             $status = "Active now";
//             $current = $row['unique_id'];
//             $_SESSION['userssname'] = $row['username']; 
            
//             $sql2= mysqli_query($link, " UPDATE registration SET status = '$status' WHERE unique_id = '$current'");
            
//             if($sql2){
//                 $_SESSION['unique_id'] = $row['unique_id']; //using this session we used user unique_id in other php file
//                 $_SESSION['img'] = $row['img']; 
//                 echo "success";
//             }else{
//                 echo'your syntax is wrong';
//             }
            
//         }
       
//     }else{
//         echo"Email or password is incorrect";
//     }
    
   
// }else{
//     echo "All input fields are required!";
// }

if (!empty($email) && !empty($password)) {
    // Fetch the user from Supabase using fetchData
    $response = fetchData('registration?email=eq.' . urlencode($email));

    // Check for errors or if user exists
    if (isset($response[0])) { 
        $row = $response[0];

        // Verify the password
        if (!password_verify($password, $row['password'])) {
            $error_message = 'Invalid password.';
        } else {
            $status = "Active now";
            $current = $row['unique_id'];
            $_SESSION['userssname'] = $row['username']; // Keep this unchanged

            // Prepare data for updating user status
            $updateData = ['status' => $status];

            // Perform the update using the updateData function
            $updateResponse = updateData('registration', $current, $updateData);

            // Check if the update was successful
            if (isset($updateResponse['error'])) {
                echo 'Your syntax is wrong';
            } else {
                $_SESSION['unique_id'] = $row['unique_id']; // Keep this unchanged
                $_SESSION['img'] = $row['img']; 
                echo "success"; // Keep this unchanged
            }
        }
    } else {
        echo "Email or password is incorrect";
    }
}else{
    echo "All input fields are required!";
}



?>