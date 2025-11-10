<?php 
    include "header.php";
    include "../connection.php";
    include "../superbase/config.php";

    // Determine which connection to use
    $useLocal = isset($_COOKIE['useLocalDB']) && $_COOKIE['useLocalDB'] === 'true';

    $id = $_GET["id"];
    $id1 = $_GET["id1"];

    $question = "";
    $opt1 = "";
    $opt2 = "";
    $opt3 = "";
    $opt4 = "";
    $answer = "";

    // Handle form submissions FIRST before any HTML output
    $showToastScript = '';

    // Fetch question data
    if ($useLocal) {
        // LOCAL MYSQL CONNECTION
        $res = mysqli_query($link, "select * from questions where id=$id");
        while($row = mysqli_fetch_array($res)){
            $question = $row["question"];
            $opt1 = $row["opt1"];
            $opt2 = $row["opt2"];
            $opt3 = $row["opt3"];
            $opt4 = $row["opt4"];
            $answer = $row["answer"];
        }
    } else {
        // SUPABASE CONNECTION
        $response = fetchData("questions?id=eq.$id");
        if (is_array($response) && !isset($response['error']) && count($response) > 0) {
            $question = $response[0]["question"] ?? "";
            $opt1 = $response[0]["opt1"] ?? "";
            $opt2 = $response[0]["opt2"] ?? "";
            $opt3 = $response[0]["opt3"] ?? "";
            $opt4 = $response[0]["opt4"] ?? "";
            $answer = $response[0]["answer"] ?? "";
        }
    }

    // Handle form submission
    if (isset($_POST["submit2"])) {
        if ($useLocal) {
            // LOCAL MYSQL CONNECTION
            $tm = md5(time());
            $updateSuccess = true;
            $errorMessage = "";

            // Function to handle file upload for MySQL
            function uploadFileLocal($file, $tm, $link, $option, $id) {
                if ($file["name"] != "") {
                    $dst = "./opt_images/" . $tm . $file["name"];
                    $dst_db = "opt_images/" . $tm . $file["name"];
                    if (move_uploaded_file($file["tmp_name"], $dst)) {
                        mysqli_query($link, "UPDATE questions SET $option='$dst_db' WHERE id=$id") or die(mysqli_error($link));
                        return ['success' => true];
                    } else {
                        return ['success' => false, 'message' => "Error uploading file: " . $file["error"]];
                    }
                }
                return ['success' => true]; // No file to upload is not an error
            }

            // Upload each file
            $uploadResults = [];
            $uploadResults['opt1'] = uploadFileLocal($_FILES["fopt1"], $tm, $link, 'opt1', $id);
            $uploadResults['opt2'] = uploadFileLocal($_FILES["fopt2"], $tm, $link, 'opt2', $id);
            $uploadResults['opt3'] = uploadFileLocal($_FILES["fopt3"], $tm, $link, 'opt3', $id);
            $uploadResults['opt4'] = uploadFileLocal($_FILES["fopt4"], $tm, $link, 'opt4', $id);
            $uploadResults['answer'] = uploadFileLocal($_FILES["fanswer"], $tm, $link, 'answer', $id);

            // Check if all uploads were successful
            $allUploadsSuccessful = true;
            $uploadErrors = [];
            
            foreach ($uploadResults as $field => $result) {
                if (!$result['success']) {
                    $allUploadsSuccessful = false;
                    $uploadErrors[] = ucfirst($field) . ': ' . $result['message'];
                }
            }

            if ($allUploadsSuccessful) {
                // Update question text
                $updateResult = mysqli_query($link, "UPDATE questions SET question='$_POST[fquestion]' WHERE id=$id");
                if($updateResult) {
                    $showToastScript = "
                        <script type='text/javascript'>
                            document.addEventListener('DOMContentLoaded', function() {
                                showToast('Question updated successfully', 'success');
                                setTimeout(function() {
                                    window.location.href = 'add_edit_questions.php?id=$id1';
                                }, 1500);
                            });
                        </script>
                    ";
                } else {
                    $errorMsg = addslashes(mysqli_error($link));
                    $showToastScript = "
                        <script type='text/javascript'>
                            document.addEventListener('DOMContentLoaded', function() {
                                showToast('Error updating question: $errorMsg', 'error');
                            });
                        </script>
                    ";
                }
            } else {
                $errorMessage = "File upload errors: " . implode("; ", $uploadErrors);
                $errorMsg = addslashes($errorMessage);
                $showToastScript = "
                    <script type='text/javascript'>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast('$errorMsg', 'error');
                        });
                    </script>
                ";
            }
        } else {
            // SUPABASE CONNECTION
            $tm = md5(time());
            
            // Function to handle file upload for Supabase
            function uploadFileSupabase($file, $tm) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                
                if ($file['name'] == "") {
                    return ['success' => true, 'path' => null]; // No file to upload
                }
                
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    return [
                        'success' => false,
                        'message' => 'File upload error',
                        'path' => null
                    ];
                }
                
                if (!in_array($file['type'], $allowedTypes)) {
                    return [
                        'success' => false,
                        'message' => 'Invalid file type. Only JPEG, PNG, and GIF images are allowed.',
                        'path' => null
                    ];
                }

                $filename = basename($file["name"]);
                $destination = "./opt_images/" . $tm . $filename;
                
                if (!is_dir("./opt_images")) {
                    if (!mkdir("./opt_images", 0755, true)) {
                        return [
                            'success' => false,
                            'message' => 'Failed to create upload directory.',
                            'path' => null
                        ];
                    }
                }
                
                if (!is_writable("./opt_images")) {
                    return [
                        'success' => false,
                        'message' => 'Upload directory is not writable.',
                        'path' => null
                    ];
                }

                if (move_uploaded_file($file["tmp_name"], $destination)) {
                    return [
                        'success' => true,
                        'message' => 'File uploaded successfully',
                        'path' => "opt_images/" . $tm . $filename
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Failed to move uploaded file.',
                        'path' => null
                    ];
                }
            }

            // Upload files and collect results
            $uploadResults = [];
            $uploadResults['opt1'] = uploadFileSupabase($_FILES["fopt1"], $tm);
            $uploadResults['opt2'] = uploadFileSupabase($_FILES["fopt2"], $tm);
            $uploadResults['opt3'] = uploadFileSupabase($_FILES["fopt3"], $tm);
            $uploadResults['opt4'] = uploadFileSupabase($_FILES["fopt4"], $tm);
            $uploadResults['answer'] = uploadFileSupabase($_FILES["fanswer"], $tm);

            // Check upload results
            $allUploadsSuccessful = true;
            $uploadErrors = [];
            
            foreach ($uploadResults as $field => $result) {
                if (!$result['success']) {
                    $allUploadsSuccessful = false;
                    $uploadErrors[] = ucfirst($field) . ': ' . $result['message'];
                }
            }

            if ($allUploadsSuccessful) {
                // Prepare update data for Supabase
                $updateData = [
                    'question' => $_POST['fquestion']
                ];
                
                // Add file paths to update data if files were uploaded
                if ($uploadResults['opt1']['path']) $updateData['opt1'] = $uploadResults['opt1']['path'];
                if ($uploadResults['opt2']['path']) $updateData['opt2'] = $uploadResults['opt2']['path'];
                if ($uploadResults['opt3']['path']) $updateData['opt3'] = $uploadResults['opt3']['path'];
                if ($uploadResults['opt4']['path']) $updateData['opt4'] = $uploadResults['opt4']['path'];
                if ($uploadResults['answer']['path']) $updateData['answer'] = $uploadResults['answer']['path'];
                
                // Update question in Supabase
                // $result = updateData('questions', $updateData, "id=eq.$id");
                $result = updateData('questions', $id, $updateData);;
                // updateData('questions', $id, $updateData);
                
                if (isset($result['error'])) {
                    $errorMessage = extractSupabaseErrorMessage($result['error']);
                    $errorMsg = addslashes($errorMessage);
                    $showToastScript = "
                        <script type='text/javascript'>
                            document.addEventListener('DOMContentLoaded', function() {
                                showToast('Error updating question: $errorMsg', 'error');
                            });
                        </script>
                    ";
                } else {
                    $showToastScript = "
                        <script type='text/javascript'>
                            document.addEventListener('DOMContentLoaded', function() {
                                showToast('Question updated successfully in Supabase', 'success');
                                setTimeout(function() {
                                    window.location.href = 'add_edit_questions.php?id=$id1';
                                }, 1500);
                            });
                        </script>
                    ";
                }
            } else {
                $errorMessage = "File upload errors: " . implode("; ", $uploadErrors);
                $errorMsg = addslashes($errorMessage);
                $showToastScript = "
                    <script type='text/javascript'>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast('$errorMsg', 'error');
                        });
                    </script>
                ";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Question Images</title>
</head>
<body>
    <?php echo $showToastScript; ?>

    <div class="breadcrumbs">
        <div class="col-sm-4">
            <div class="page-header float-left">
                <div class="page-title">
                    <h1>Update Question Images</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content mt-3">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-lg-12">
                                <form name="form1" action="" method="post" enctype="multipart/form-data" onsubmit="showSpinner()">
                                    <div class="card">
                                        <div class="card-header"><strong>Update Questions with images</strong></div>
                                        <div class="card-body card-block">
                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Question</label>
                                                <input type="text" name="fquestion" placeholder="Add Question" class="form-control" value="<?php echo htmlspecialchars($question); ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <img src="<?php echo $opt1; ?>" height="50" width="50" style="border: 1px solid #ddd; padding: 2px;">
                                                <label for="company" class=" form-control-label">Update Opt1</label>
                                                <input type="file" name="fopt1" class="form-control" style="padding-bottom: 35px;" accept="image/jpeg,image/png,image/gif">
                                            </div>         

                                            <div class="form-group">
                                                <img src="<?php echo $opt2; ?>" height="50" width="50" style="border: 1px solid #ddd; padding: 2px;">
                                                <label for="company" class=" form-control-label">Update Opt2</label>
                                                <input type="file" name="fopt2" class="form-control" style="padding-bottom: 35px;" accept="image/jpeg,image/png,image/gif">
                                            </div>  

                                            <div class="form-group">
                                                <img src="<?php echo $opt3; ?>" height="50" width="50" style="border: 1px solid #ddd; padding: 2px;">
                                                <label for="company" class=" form-control-label">Update Opt3</label>
                                                <input type="file" name="fopt3" class="form-control" style="padding-bottom: 35px;" accept="image/jpeg,image/png,image/gif">
                                            </div>  

                                            <div class="form-group">
                                                <img src="<?php echo $opt4; ?>" height="50" width="50" style="border: 1px solid #ddd; padding: 2px;">
                                                <label for="company" class=" form-control-label">Update Opt4</label>
                                                <input type="file" name="fopt4" class="form-control" style="padding-bottom: 35px;" accept="image/jpeg,image/png,image/gif">
                                            </div>  
                                            
                                            <div class="form-group">
                                                <img src="<?php echo $answer; ?>" height="50" width="50" style="border: 1px solid #ddd; padding: 2px;">
                                                <label for="company" class=" form-control-label">Update Answer</label>
                                                <input type="file" name="fanswer" class="form-control" style="padding-bottom: 35px;" accept="image/jpeg,image/png,image/gif">
                                            </div>  
                                            
                                            <div class="form-group">                                                
                                                <input type="submit" name="submit2" value="Update question" class="btn btn-success">
                                            </div>             
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hide spinner when page is fully loaded
        hideSpinner();
    });

    // Add form submission handler to show spinner
    document.addEventListener('submit', function(e) {
        if (e.target.matches('form[name="form1"]')) {
            showSpinner();
        }
    });
    </script>

    <?php include "footer.php"?>
</body>
</html>