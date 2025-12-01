<?php 
    // session_start();
    // include_once "../../connection.php";
    // $examname = mysqli_real_escape_string($link, $_POST['examname']);
    // $year = mysqli_real_escape_string($link, $_POST['year']);
    // $examtime = mysqli_real_escape_string($link, $_POST['examtime']);
    // $price = mysqli_real_escape_string($link, $_POST['price']);
    // $start_date = mysqli_real_escape_string($link, $_POST['start_date']);
    // $end_date = mysqli_real_escape_string($link, $_POST['end_date']);
    // // $image = mysqli_real_escape_string($link, $_POST['images']);
    
    // // echo $image;
   
    

    // if(!empty($examname)  && !empty($year) && !empty($examtime) && !empty($price)){
    //     if(isset($_FILES['images'])){  
    //         $img_name = $_FILES['images']['name']; 
    //         $tmp_name = $_FILES['images']['tmp_name']; 

    //         // lets explode image name and get last extension like jpg an png
    //         $img_explode = explode('.', $img_name);
    //         $img_ext = end($img_explode); 

    //         $extensions = ['png', 'jpeg', 'jpg']; 
    //         // if(in_array($img_ext, $extensions) === true){ 
    //             $time = time();
    //             $new_img_name = $time.$img_name;
    //             // if(move_uploaded_file($tmp_name, "../img/$new_img_name")){  
                
                  
                   
    //                 $sql2 = mysqli_query($link, "INSERT INTO exam_category (category, year, exam_time_in_minutes, price, book_cover, start_date, end_date) 
    //                                     VALUES ('{$examname}', {$year}, '{$examtime}', '{$price}', '{$new_img_name}', '{$start_date}','{$end_date}')");
    //                 if($sql2){  //if these data inserted
    //                     $sql3 = mysqli_query($link, "SELECT * FROM exam_category WHERE category = '{$examname}' ");
    //                         if(mysqli_num_rows($sql3) > 0){
    //                             $row = mysqli_fetch_assoc($sql3);
    //                             // $_SESSION['examName'] = $row['category']; 
    //                             // $_SESSION['examYear'] = $row['year'];                                                               
    //                             echo "success";
                                
    //                         }
    //                 }else{
    //                     echo "Something went wrong";
    //                 }
    //             // }
    //         // }else{
    //             // echo "Please select an Image file - jpeg, jpg, png!";
    //         // }
    //     }else{
    //         echo "Please select an Image file!";
    //     }
    // }else{
    //     echo "All input fields are required!";
               
    // }
?>
<?php 
    session_start();
    // include_once "connection.php";
    include_once "../../superbase/config.php";

    $examname = $_POST['examname'];
    $year = $_POST['year'];
    $examtime = $_POST['examtime'];
    $price = $_POST['price'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if(!empty($examname) && !empty($year) && !empty($examtime) && !empty($price)){
        if(isset($_FILES['images'])){  
            $img_name = $_FILES['images']['name']; 
            $tmp_name = $_FILES['images']['tmp_name']; 

            // Process image file
            $img_explode = explode('.', $img_name);
            $img_ext = end($img_explode); 

            $extensions = ['png', 'jpeg', 'jpg']; 
            
            $time = time();
            $new_img_name = $time . $img_name;
            
            // Prepare data for Supabase
            $examData = [
                'category' => $examname,
                'year' => (int)$year,
                'exam_time_in_minutes' => $examtime,
                'price' => $price,
                'book_cover' => $new_img_name,
                'start_date' => $start_date,
                'end_date' => $end_date
            ];

            // Insert into Supabase using createData function
            $response = createData('exam_category', $examData);
            
            // Debug: Log the response
            error_log("Supabase insert response: " . print_r($response, true));

            // Check if response has error
            if (isset($response['error'])) {
                echo json_encode(array(
                    "message" => "Database connection failed. Please try again.",
                    "success" => false
                ));
            }
            // Check if insert was successful
            else if (is_array($response) && count($response) > 0) {
                // Successfully inserted - verify by fetching the inserted record
                $verifyResponse = fetchData('exam_category?category=eq.' . urlencode($examname));
                
                if (is_array($verifyResponse) && count($verifyResponse) > 0) {
                    $row = $verifyResponse[0];
                    
                    // Move uploaded file after successful database insertion
                    if(move_uploaded_file($tmp_name, "../img/$new_img_name")){
                        echo json_encode(array(
                            "message" => "Exam category created successfully!",
                            "success" => true
                        ));
                    } else {
                        // Database success but file upload failed
                        echo json_encode(array(
                            "message" => "Exam created but image upload failed",
                            "success" => true
                        ));
                    }
                } else {
                    echo json_encode(array(
                        "message" => "Exam created but verification failed",
                        "success" => true
                    ));
                }
            } else {
                echo json_encode(array(
                    "message" => "Failed to create exam category",
                    "success" => false
                ));
            }
        } else {
            echo json_encode(array(
                "message" => "Please select an Image file!",
                "success" => false
            ));
        }
    } else {
        echo json_encode(array(
            "message" => "All input fields are required!",
            "success" => false
        ));
    }
?>