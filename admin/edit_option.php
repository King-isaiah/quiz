<?php 
    include "header.php";
    // include "../connection.php";
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

    // Handle form submission for text-based update
    if (isset($_POST["submit1"])) {
        if ($useLocal) {
            // LOCAL MYSQL UPDATE
            $updateResult = mysqli_query($link, "UPDATE questions SET question='$_POST[question]',opt1='$_POST[opt1]',
            opt2='$_POST[opt2]',opt3='$_POST[opt3]',opt4='$_POST[opt4]',answer='$_POST[answer]' WHERE id=$id");
            
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
            // SUPABASE UPDATE
            $updateData = [
                'question' => $_POST['question'],
                'opt1' => $_POST['opt1'],
                'opt2' => $_POST['opt2'],
                'opt3' => $_POST['opt3'],
                'opt4' => $_POST['opt4'],
                'answer' => $_POST['answer']
            ];
            
            $result = updateData('questions', $id, $updateData);
            
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
        }
    }

    // Handle form submission for image-based update
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
                $result = updateData('questions', $id, $updateData);
                
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
    <title>Update Questions</title>
</head>
<body>
    <?php echo $showToastScript; ?>

    <div class="breadcrumbs">
        <div class="col-sm-4">
            <div class="page-header float-left">
                <div class="page-title">
                    <h1>Update Questions</h1>
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
                            <!-- Text-based Question Update Form -->
                            <div class="col-lg-12">
                                <form name="form1" action="" method="post" enctype="multipart/form-data" onsubmit="showSpinner()">
                                    <div class="card">
                                        <div class="card-header"><strong>Update Questions with texts</strong></div>
                                        <div class="card-body card-block">
                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Question</label>
                                                <input type="text" name="question" placeholder="Add Question" class="form-control" value="<?php echo htmlspecialchars($question); ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Opt1</label>
                                                <input type="text" name="opt1" placeholder="Add Opt1" class="form-control" value="<?php echo htmlspecialchars($opt1); ?>">
                                            </div>         

                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Opt2</label>
                                                <input type="text" name="opt2" placeholder="Add Opt2" class="form-control" value="<?php echo htmlspecialchars($opt2); ?>">
                                            </div>  

                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Opt3</label>
                                                <input type="text" name="opt3" placeholder="Add Opt3" class="form-control" value="<?php echo htmlspecialchars($opt3); ?>">
                                            </div>  

                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Opt4</label>
                                                <input type="text" name="opt4" placeholder="Add Opt4" class="form-control" value="<?php echo htmlspecialchars($opt4); ?>">
                                            </div>  
                                            
                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Answer</label>
                                                <input type="text" name="answer" placeholder="Answer" class="form-control" value="<?php echo htmlspecialchars($answer); ?>">
                                            </div>  
                                            
                                            <div class="form-group">                                                
                                                <input type="submit" name="submit1" value="Update question" class="btn btn-success">
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
        if (e.target.matches('form[name="form1"], form[name="form2"]')) {
            showSpinner();
        }
    });
    </script>

    <?php include "footer.php"?>
</body>
</html>