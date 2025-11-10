<?php 
    session_start();
    include_once "connection.php";
    include_once "../../superbase/config.php";
    $uname = mysqli_real_escape_string($link, $_POST['username']);
    $fname = mysqli_real_escape_string($link, $_POST['fname']);
    $lname = mysqli_real_escape_string($link, $_POST['lname']);
    $gender = mysqli_real_escape_string($link, $_POST['gender']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $contact = mysqli_real_escape_string($link, $_POST['phone_number']);
    $password = mysqli_real_escape_string($link, $_POST['password']);
                   
 
    // if(!empty($fname)  && !empty($lname) && !empty($email) && !empty($password)){
    //     if(filter_var($email, FILTER_VALIDATE_EMAIL)){ 
    //         // lets check that email already exist in database or not
    //         $sql = mysqli_query($link, "SELECT email FROM registration WHERE email = '{$email}'");
    //         if(mysqli_num_rows($sql) > 0){
    //             echo "$email - this email already exist";
    //         }else{
    //             $sqlU = mysqli_query($link, "SELECT username FROM registration WHERE username = '{$uname}'"); 
    //             if(mysqli_num_rows($sqlU) > 0){ 
    //                 echo "$uname - this Username already exist pls Use another";
    //             }else{
    //                 if(isset($_FILES['image'])){  
    //                     $img_name = $_FILES['image']['name']; 
    //                     $tmp_name = $_FILES['image']['tmp_name']; 
    
    //                     // lets explode image name and get last extension like jpg an png
    //                     $img_explode = explode('.', $img_name);
    //                     $img_ext = end($img_explode); 
    
    //                     $extensions = ['png', 'jpeg', 'jpg']; 
    //                     if(in_array($img_ext, $extensions) === true){ 
    //                         $time = time();
    //                         $new_img_name = $time.$img_name;
    //                         if(move_uploaded_file($tmp_name, "images/$new_img_name")){  
                            
    //                             $status = "Active now"; 
    //                             $random_id = rand(time(), 10000000);  
    //                             $hash = password_hash("$password", PASSWORD_BCRYPT);
                               
    //                             $sql2 = mysqli_query($link, "INSERT INTO registration (username,unique_id, firstname, lastname, gender, contact, email, password, img, status) 
    //                                                 VALUES ('{$fname}', {$random_id}, '{$fname}', '{$lname}', '{$gender}', '{$contact}', '{$email}', '{$hash}', '{$new_img_name}', '{$status}')");
    //                             if($sql2){  //if these data inserted
    //                                 $sql3 = mysqli_query($link, "SELECT * FROM registration WHERE email = '{$email}' ");
    //                                     if(mysqli_num_rows($sql3) > 0){
    //                                         $row = mysqli_fetch_assoc($sql3);
    //                                         $_SESSION['unique_id'] = $row['unique_id']; 
    //                                         $_SESSION['username'] = $row['username'];
    //                                         $_SESSION['lastname'] = $row['lastname'];
    //                                         $_SESSION['gender'] = $row['gender'];
    //                                         $_SESSION['funame'] = $row['firstname'] . ' ' . $row['lastname'];
    //                                         $_SESSION['firstname'] = $row['firstname'];
    //                                         $_SESSION['email'] = $row['email'];
    //                                         $_SESSION['contact'] = $row['contact'];
    //                                         $_SESSION['gender'] = $row['gender'];
    //                                         echo "success";
                                            
                                            
    //                                     }
    //                             }else{
    //                                 echo "Something went wrong";
    //                             }
    //                         }
    //                     }else{
    //                         echo "Please select an Image file - jpeg, jpg, png!";
    //                     }
    //                 }else{
    //                     echo "Please select an Image file!";
    //                 }
    //             }          
                
    //         }
    //     }else{
    //         echo "$email -this is not a valid email";
    //     }
    // }else{
    //     echo "All input feild are required!";
               
    // }

    if(!empty($gender)){
        if (!empty($fname) && !empty($lname) && !empty($email) && !empty($password)) {
            // Normalize email to lowercase
            $email = strtolower($email);

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // Check if the email already exists in the database
                $existingEmail = fetchData('registration?email=eq.' . urlencode($email));
                if (isset($existingEmail['error'])) {
                    echo "Error checking email: " . htmlspecialchars($existingEmail['error']);
                    return;
                }

                if (!empty($existingEmail)) {
                    echo "$email - this email already exists.";
                } else {
                    // Check if the username already exists
                    $existingUsername = fetchData('registration?username=eq.' . urlencode($uname));
                    if (isset($existingUsername['error'])) {
                        echo "Error checking username: " . htmlspecialchars($existingUsername['error']);
                        return;
                    }

                    if (!empty($existingUsername)) {
                        echo "$uname - this username already exists, please use another.";
                    } else {
                        if (isset($_FILES['image'])) {
                            $img_name = $_FILES['image']['name'];
                            $tmp_name = $_FILES['image']['tmp_name'];

                            // Explode image name and get the last extension like jpg and png
                            $img_explode = explode('.', $img_name);
                            $img_ext = end($img_explode);

                            $extensions = ['png', 'jpeg', 'jpg'];
                            if (in_array($img_ext, $extensions) === true) {
                                $time = time();
                                $new_img_name = $time . $img_name;
                                if (move_uploaded_file($tmp_name, "images/$new_img_name")) {
                                    $status = "Active now";
                                    $random_id = rand(time(), 10000000);
                                    $hash = password_hash($password, PASSWORD_BCRYPT);

                                    // Prepare data for insertion
                                    $data = [
                                        'username' => $uname,
                                        'unique_id' => $random_id,
                                        'firstname' => $fname,
                                        'lastname' => $lname,
                                        'gender' => $gender,
                                        'contact' => $contact,
                                        'email' => $email, 
                                        'password' => $hash,
                                        'img' => $new_img_name,
                                        'status' => $status
                                    ];

                                    // Log the data to be inserted
                                    error_log("Data to insert: " . print_r($data, true));

                                    // Insert new user into the registration table
                                    $insertResult = createData('registration', $data);

                                    // Check if the insertion was successful
                                    if (isset($insertResult['error'])) {
                                        echo "Error inserting data: " . htmlspecialchars($insertResult['error']);
                                    } else {
                                        echo "success: Succesfully inserted data: " ;
                                        // Fetch the newly created user to set session variables
                                        $newUser = fetchData('registration?email=eq.' . urlencode($email));
                                        if (isset($newUser['error'])) {
                                            echo "Error fetching new user data: " . htmlspecialchars($newUser['error']);
                                            return;
                                        }

                                        if (!empty($newUser)) {
                                            $row = $newUser[0]; // Get the first (and only) result
                                            $_SESSION['unique_id'] = $row['unique_id'];
                                            $_SESSION['username'] = $row['username'];
                                            $_SESSION['lastname'] = $row['lastname'];
                                            $_SESSION['gender'] = $row['gender'];
                                            $_SESSION['funame'] = $row['firstname'] . ' ' . $row['lastname'];
                                            $_SESSION['firstname'] = $row['firstname'];
                                            $_SESSION['email'] = $row['email'];
                                            $_SESSION['contact'] = $row['contact'];
                                            $_SESSION['gender'] = $row['gender'];
                                            echo "Registration successful!";
                                            echo"success";
                                        } else {
                                            echo "Error: Newly created user not found.";
                                        }
                                    }
                                } else {
                                    echo "Error moving the uploaded image.";
                                }
                            } else {
                                echo "Please select an image file - jpeg, jpg, png!";
                            }
                        } else {
                            echo "Please select an image file!";
                        }
                    }
                }
            } else {
                echo "$email - this is not a valid email.";
            }
        } else {
            echo "All input fields are required!";
        }
    }else {
            echo "Gender fields are required!";
        }


    
    
?>