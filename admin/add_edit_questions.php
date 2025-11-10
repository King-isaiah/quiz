<?php session_start()?>
<?php include "header.php"?>
<?php 
    include "../connection.php";
    include "../superbase/config.php";
    
    if(!isset($_SESSION["admin"])){
        ?>
        <script type="text/javascript"> 
            window.location="index.php";
        </script>
        <?php
    } 

    $id=$_GET["id"];
    $exam_category="";
    
    // Determine which connection to use
    $useLocal = isset($_COOKIE['useLocalDB']) && $_COOKIE['useLocalDB'] === 'true';
    
    if ($useLocal) {
        // LOCAL MYSQL CONNECTION
        $res = mysqli_query($link,"select * from exam_category where id=$id");
        while($row=mysqli_fetch_array($res)){
            $exam_category=$row["category"];
        }
    } else {
        // SUPABASE CONNECTION
        $response = fetchData("exam_category?id=eq.$id");
        if (is_array($response) && !isset($response['error']) && count($response) > 0) {
            $exam_category = $response[0]["category"] ?? "";
        }
    }

    // Handle form submissions FIRST before any HTML output
    $showToastScript = '';
    
    // Handle text-based question submission
    if(isset($_POST["submit11"])){
        $useLocal = isset($_COOKIE['useLocalDB']) && $_COOKIE['useLocalDB'] === 'true';
        
        if ($useLocal) {
            // LOCAL MYSQL CONNECTION
            $loop=0;
            $count=0;
            $res=mysqli_query($link,"SELECT * from questions where category='$exam_category' order by id asc") or die(mysqli_error($link));

            $count =mysqli_num_rows($res);
            if($count==0){
                // No existing questions
            } else {
                while($row = mysqli_fetch_array($res)){
                    $loop=$loop+1;
                    mysqli_query($link,"update questions set question_no='$loop' where id=$row[id]");
                }
            }

            $loop = $loop+1;
            $result = mysqli_query($link,"insert into questions values(NULL,'$loop',
            '$_POST[question]','$_POST[opt1]','$_POST[opt2]','$_POST[opt3]',
            '$_POST[opt4]','$_POST[answer]','$exam_category')");
            
            if($result) {
                $showToastScript = "
                    <script type='text/javascript'>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast('Question added successfully', 'success');
                            setTimeout(function() {
                                window.location.href = window.location.href;
                            }, 1500);
                        });
                    </script>
                ";
            } else {
                $errorMsg = addslashes(mysqli_error($link));
                $showToastScript = "
                    <script type='text/javascript'>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast('Error adding question: $errorMsg', 'error');
                        });
                    </script>
                ";
            }
        } else {
            // SUPABASE CONNECTION
            $response = fetchData("questions?category=eq.$exam_category&order=question_no.asc");
            $loop = 0;
            
            if (is_array($response) && !isset($response['error'])) {
                $loop = count($response);
            }
            
            $loop = $loop + 1;
            
            $questionData = [
                'question_no' => $loop,
                'question' => $_POST['question'],
                'opt1' => $_POST['opt1'],
                'opt2' => $_POST['opt2'],
                'opt3' => $_POST['opt3'],
                'opt4' => $_POST['opt4'],
                'answer' => $_POST['answer'],
                'category' => $exam_category
            ];
            
            $result = createData('questions', $questionData);
            
            if (isset($result['error'])) {
                $errorMessage = extractSupabaseErrorMessage($result['error']);
                $errorMsg = addslashes($errorMessage);
                $showToastScript = "
                    <script type='text/javascript'>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast('Error adding question: $errorMsg', 'error');
                        });
                    </script>
                ";
            } else {
                $showToastScript = "
                    <script type='text/javascript'>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast('Question added successfully', 'success');
                            setTimeout(function() {
                                window.location.href = window.location.href;
                            }, 1500);
                        });
                    </script>
                ";
            }
        }
    }

    // Handle image-based question submission
    if (isset($_POST["submit2"])) {
        $useLocal = isset($_COOKIE['useLocalDB']) && $_COOKIE['useLocalDB'] === 'true';
        
        // Define uploadFile function
        function uploadFile($file, $tm) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            
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
        
        if ($useLocal) {
            // LOCAL MYSQL CONNECTION
            $loop = 0;
            $count = 0;

            $res = mysqli_query($link, "SELECT * FROM questions WHERE category='$exam_category' ORDER BY id ASC");
            $count = mysqli_num_rows($res);

            if ($count > 0) {
                while ($row = mysqli_fetch_array($res)) {
                    $loop++;
                    mysqli_query($link, "UPDATE questions SET question_no='$loop' WHERE id={$row['id']}");
                }
            }

            $loop++;
            $tm = md5(time());

            // Upload files
            $uploadResults = [];
            $uploadResults['opt1'] = uploadFile($_FILES["fopt1"], $tm);
            $uploadResults['opt2'] = uploadFile($_FILES["fopt2"], $tm);
            $uploadResults['opt3'] = uploadFile($_FILES["fopt3"], $tm);
            $uploadResults['opt4'] = uploadFile($_FILES["fopt4"], $tm);
            $uploadResults['answer'] = uploadFile($_FILES["fanswer"], $tm);

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
                $dst_db1 = $uploadResults['opt1']['path'];
                $dst_db2 = $uploadResults['opt2']['path'];
                $dst_db3 = $uploadResults['opt3']['path'];
                $dst_db4 = $uploadResults['opt4']['path'];
                $dst_db5 = $uploadResults['answer']['path'];
                
                $query = "INSERT into questions values(NULL,'$loop','$_POST[fquestion]','$dst_db1','$dst_db2','$dst_db3','$dst_db4','$dst_db5','$exam_category')";
                $result = mysqli_query($link, $query);
                
                if($result) {
                    $showToastScript = "
                        <script type='text/javascript'>
                            document.addEventListener('DOMContentLoaded', function() {
                                showToast('Question with images added successfully', 'success');
                                setTimeout(function() {
                                    window.location.href = window.location.href;
                                }, 1500);
                            });
                        </script>
                    ";
                } else {
                    $errorMsg = addslashes(mysqli_error($link));
                    $showToastScript = "
                        <script type='text/javascript'>
                            document.addEventListener('DOMContentLoaded', function() {
                                showToast('Error adding question to database: $errorMsg', 'error');
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
            $loop = 0;
            
            $response = fetchData("questions?category=eq.$exam_category&order=question_no.asc");
            if (is_array($response) && !isset($response['error'])) {
                $loop = count($response);
            }
            $loop++;

            $tm = md5(time());

            // Upload files
            $uploadResults = [];
            $uploadResults['opt1'] = uploadFile($_FILES["fopt1"], $tm);
            $uploadResults['opt2'] = uploadFile($_FILES["fopt2"], $tm);
            $uploadResults['opt3'] = uploadFile($_FILES["fopt3"], $tm);
            $uploadResults['opt4'] = uploadFile($_FILES["fopt4"], $tm);
            $uploadResults['answer'] = uploadFile($_FILES["fanswer"], $tm);

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
                $dst_db1 = $uploadResults['opt1']['path'];
                $dst_db2 = $uploadResults['opt2']['path'];
                $dst_db3 = $uploadResults['opt3']['path'];
                $dst_db4 = $uploadResults['opt4']['path'];
                $dst_db5 = $uploadResults['answer']['path'];
                
                $questionData = [
                    'question_no' => $loop,
                    'question' => $_POST['fquestion'],
                    'opt1' => $dst_db1,
                    'opt2' => $dst_db2,
                    'opt3' => $dst_db3,
                    'opt4' => $dst_db4,
                    'answer' => $dst_db5,
                    'category' => $exam_category
                ];
                
                $result = createData('questions', $questionData);
                
                if (isset($result['error'])) {
                    $errorMessage = extractSupabaseErrorMessage($result['error']);
                    $errorMsg = addslashes($errorMessage);
                    $showToastScript = "
                        <script type='text/javascript'>
                            document.addEventListener('DOMContentLoaded', function() {
                                showToast('Error adding question: $errorMsg', 'error');
                            });
                        </script>
                    ";
                } else {
                    $showToastScript = "
                        <script type='text/javascript'>
                            document.addEventListener('DOMContentLoaded', function() {
                                showToast('Question with images added successfully to Supabase', 'success');
                                setTimeout(function() {
                                    window.location.href = window.location.href;
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
    <title>Add Questions</title>
</head>
<body>
    <?php echo $showToastScript; ?>

    <div class="breadcrumbs">
        <div class="col-sm-4">
            <div class="page-header float-left">
                <div class="page-title">
                    <h1>Add questions inside <?php echo "<font color='red'>". $exam_category." </font>"?></h1>
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
                            <div class="col-lg-6">
                                <form name="form1" action="" method="post" enctype="multipart/form-data" onsubmit="showSpinner()">
                                    <div class="card">
                                        <div class="card-header"><strong>Add New Questions with texts</strong></div>
                                        <div class="card-body card-block">
                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Question</label>
                                                <input type="text" name="question" placeholder="Add Question" class="form-control" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Opt1</label>
                                                <input type="text" name="opt1" placeholder="Add Opt1" class="form-control" required>
                                            </div>         

                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Opt2</label>
                                                <input type="text" name="opt2" placeholder="Add Opt2" class="form-control" required>
                                            </div>  

                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Opt3</label>
                                                <input type="text" name="opt3" placeholder="Add Opt3" class="form-control" required>
                                            </div>  

                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Opt4</label>
                                                <input type="text" name="opt4" placeholder="Add Opt4" class="form-control" required>
                                            </div>  
                                            
                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Answer</label>
                                                <input type="text" name="answer" placeholder="Answer" class="form-control" required>
                                            </div>  
                                            
                                            <div class="form-group">                                                
                                                <input type="submit" name="submit11" value="Add question" class="btn btn-success" >
                                            </div>             
                                        </div>    
                                    </div>
                                </form>
                            </div>

                            <div class="col-lg-6">
                                <form name="form2" action="" method="post" enctype="multipart/form-data" onsubmit="showSpinner()">
                                    <div class="card">
                                        <div class="card-header"><strong>Add New Questions with images</strong></div>
                                        <div class="card-body card-block">
                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Question</label>
                                                <input type="text" name="fquestion" placeholder="Add Question" class="form-control" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Opt1</label>
                                                <input type="file" name="fopt1" class="form-control" style="padding-bottom: 35px;" accept="image/jpeg,image/png,image/gif" required>
                                            </div>         

                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Opt2</label>
                                                <input type="file" name="fopt2" class="form-control"  style="padding-bottom: 35px;" accept="image/jpeg,image/png,image/gif" required>
                                            </div>  

                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Opt3</label>
                                                <input type="file" name="fopt3" class="form-control"  style="padding-bottom: 35px;" accept="image/jpeg,image/png,image/gif" required>
                                            </div>  

                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Opt4</label>
                                                <input type="file" name="fopt4" class="form-control"  style="padding-bottom: 35px;" accept="image/jpeg,image/png,image/gif" required>
                                            </div>  
                                            
                                            <div class="form-group">
                                                <label for="company" class=" form-control-label">Add Answer</label>
                                                <input type="file" name="fanswer" class="form-control"  style="padding-bottom: 35px;" accept="image/jpeg,image/png,image/gif" required>
                                            </div>  
                                            
                                            <div class="form-group">                                                
                                                <input type="submit" name="submit2" value="Add question" class="btn btn-success" >
                                            </div>             
                                        </div>    
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered" id="questionsTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Questions</th>
                                        <th>Opt1</th>
                                        <th>Opt2</th>
                                        <th>Opt3</th>
                                        <th>Opt4</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if ($useLocal) {
                                    // LOCAL MYSQL CONNECTION
                                    $res = mysqli_query($link,"SELECT * from questions where category='$exam_category' order by question_no asc");
                                    while($row=mysqli_fetch_array($res)) {
                                        displayQuestionRow($row, $id);
                                    }
                                } else {
                                    // SUPABASE CONNECTION
                                    $response = fetchData("questions?category=eq.$exam_category&order=question_no.asc");
                                    if (isset($response['error'])) {
                                        echo "<tr><td colspan='8' class='text-danger'>Error loading questions: " . htmlspecialchars($response['error']) . "</td></tr>";
                                    } elseif (empty($response)) {
                                        echo "<tr><td colspan='8' class='text-muted'>No questions found.</td></tr>";
                                    } else {
                                        foreach ($response as $row) {
                                            displayQuestionRow($row, $id);
                                        }
                                    }
                                }
                                
                                // Function to display question row (reusable for both connections)
                                function displayQuestionRow($row, $categoryId) {
                                    echo "<tr>";
                                    echo "<td>"; echo $row['question_no'] ?? ''; echo"</td>";
                                    echo "<td>"; echo $row['question'] ?? ''; echo"</td>";
                                    echo "<td>"; 
                                        if(isset($row["opt1"]) && strpos($row["opt1"],'opt_images/')!==false){
                                            ?>
                                            <img src="<?php echo $row["opt1"];?>" height="50" width="50">
                                            <?php
                                        } else {
                                            echo $row['opt1'] ?? ''; 
                                        }
                                    echo"</td>";
                                    echo "<td>"; 
                                        if(isset($row["opt2"]) && strpos($row["opt2"],'opt_images/')!==false){
                                            ?>
                                            <img src="<?php echo $row["opt2"];?>" height="50" width="50">
                                            <?php
                                        } else {
                                            echo $row['opt2'] ?? ''; 
                                        }
                                    echo"</td>";
                                    echo "<td>"; 
                                        if(isset($row["opt3"]) && strpos($row["opt3"],'opt_images/')!==false){
                                            ?>
                                            <img src="<?php echo $row["opt3"];?>" height="50" width="50">
                                            <?php
                                        } else {
                                            echo $row['opt3'] ?? ''; 
                                        }
                                    echo"</td>";
                                    echo "<td>"; 
                                        if(isset($row["opt4"]) && strpos($row["opt4"],'opt_images/')!==false){
                                            ?>
                                            <img src="<?php echo $row["opt4"];?>" height="50" width="50">
                                            <?php
                                        } else {
                                            echo $row['opt4'] ?? ''; 
                                        }
                                    echo"</td>";
                                    echo "<td>"; 
                                        if(isset($row["opt4"]) && strpos($row["opt4"],'opt_images/')!==false){
                                            ?>
                                            <a href="edit_option_images.php?id=<?php echo $row["id"]; ?>&id1=<?php echo $categoryId; ?> ">Edit</a>
                                            <?php
                                        } else {
                                            ?>
                                            <a href="edit_option.php?id=<?php echo $row["id"]; ?>&id1=<?php echo $categoryId; ?>" >Edit</a>
                                            <?php
                                        }
                                    echo"</td>";
                                    echo"<td>";
                                    ?>
                                        <a href="delete_option.php?id=<?php echo $row["id"];?>&id1=<?php echo $categoryId; ?>" onclick="return confirm('Are you sure you want to delete this question?')">Delete</a>
                                    <?php
                                    echo"</td>";
                                    echo "</tr>";
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>                    
                    </div>
                </div>
            </div>                     -->


                        <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered" id="questionsTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Questions</th>
                                        <th>Opt1</th>
                                        <th>Opt2</th>
                                        <th>Opt3</th>
                                        <th>Opt4</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $totalQuestions = 0;
                                $displayedQuestions = 0;
                                $showViewMore = false;
                                
                                if ($useLocal) {
                                    // LOCAL MYSQL CONNECTION - Get total count
                                    $count_res = mysqli_query($link,"SELECT COUNT(*) as total from questions where category='$exam_category'");
                                    $count_row = mysqli_fetch_array($count_res);
                                    $totalQuestions = $count_row['total'];
                                    
                                    // Get latest 3 questions in descending order
                                    $res = mysqli_query($link,"SELECT * from questions where category='$exam_category' order by id desc LIMIT 3");
                                    while($row=mysqli_fetch_array($res)) {
                                        displayQuestionRow($row, $id);
                                        $displayedQuestions++;
                                    }
                                } else {
                                    // SUPABASE CONNECTION - Get total count
                                    $count_response = fetchData("questions?category=eq.$exam_category&select=id");
                                    if (is_array($count_response) && !isset($count_response['error'])) {
                                        $totalQuestions = count($count_response);
                                    }
                                    
                                    // Get latest 3 questions in descending order
                                    $response = fetchData("questions?category=eq.$exam_category&order=id.desc&limit=3");
                                    if (isset($response['error'])) {
                                        echo "<tr><td colspan='8' class='text-danger'>Error loading questions: " . htmlspecialchars($response['error']) . "</td></tr>";
                                    } elseif (empty($response)) {
                                        echo "<tr><td colspan='8' class='text-muted'>No questions found.</td></tr>";
                                    } else {
                                        foreach ($response as $row) {
                                            displayQuestionRow($row, $id);
                                            $displayedQuestions++;
                                        }
                                    }
                                }
                                
                                // Check if we need to show "View More" link
                                $showViewMore = ($totalQuestions > 3);
                                
                                
                                ?>
                                </tbody>
                            </table>
                            
                            <?php if ($showViewMore): ?>
                            <div class="text-center mt-3">
                                <button type="button" class="btn btn-outline-primary" onclick="viewAllQuestions('<?php echo $id; ?>', '<?php echo htmlspecialchars($exam_category, ENT_QUOTES); ?>')">
                                    Click here to see all <?php echo $totalQuestions; ?> questions in <?php echo htmlspecialchars($exam_category); ?>
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>                    
                    </div>
                </div>
            </div>
        </div><!-- .animated -->
    </div><!-- .content -->

    <script>
        
function viewAllQuestions(categoryId, categoryName) {
    // Redirect to exam-questions.php with the category filter
    window.location.href = 'exam-questions.php?category_id=' + categoryId;
}

document.addEventListener('DOMContentLoaded', function() {
    setupGlobalTableComponents({
        tableId: 'questionsTable',
        searchPlaceholder: 'Search questions...',
        recordsPerPage: 10,
        searchColumns: [1, 2, 3, 4, 5] 
    });
    
    hideSpinner();
});

// Add form submission handlers to show spinner
document.addEventListener('submit', function(e) {
    if (e.target.matches('form[name="form1"], form[name="form2"]')) {
        showSpinner();
    }
});

    document.addEventListener('DOMContentLoaded', function() {
        setupGlobalTableComponents({
            tableId: 'questionsTable',
            searchPlaceholder: 'Search questions...',
            recordsPerPage: 10,
            searchColumns: [1, 2, 3, 4, 5] 
        });
        
        
        hideSpinner();
    });

    // Add form submission handlers to show spinner
    document.addEventListener('submit', function(e) {
        if (e.target.matches('form[name="form1"], form[name="form2"]')) {
            showSpinner();
        }
    });
    </script>

    <?php include "footer.php"?>
</body>
</html>
<?php
