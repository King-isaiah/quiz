<?php session_start()?>
<?php include "header.php"?>
<?php
    // include "../connection.php";
    include "../superbase/config.php";

    if(!isset($_SESSION["admin"])){
        ?>
        <script type="text/javascript"> 
            window.location="index.php";
        </script>
        <?php
    } 

    $id = $_GET["id"];
    $id1 = $_GET["id1"];
    
    // Determine which connection to use
    $useLocal = isset($_COOKIE['useLocalDB']) && $_COOKIE['useLocalDB'] === 'true';
    
    // Handle delete operation
    $showToastScript = '';
    
    if ($useLocal) {
        // LOCAL MYSQL CONNECTION
        $result = mysqli_query($link, "DELETE from questions where id=$id");
        
        if($result) {
            $showToastScript = "
                <script type='text/javascript'>
                    document.addEventListener('DOMContentLoaded', function() {
                        showToast('Question deleted successfully', 'success');
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
                        showToast('Error deleting question: $errorMsg', 'error');
                        setTimeout(function() {
                            window.location.href = 'add_edit_questions.php?id=$id1';
                        }, 2000);
                    });
                </script>
            ";
        }
    } else {
        // SUPABASE CONNECTION - Use the improved delete function
        if (function_exists('deleteDataImproved')) {
            // Use the improved function that takes column and value
            $result = deleteDataImproved('questions', 'id', $id);
        } else if (function_exists('deleteData')) {
            // Fallback to original function (just pass the ID)
            $result = deleteData('questions', $id);
        } else {
            // If neither function exists, show error
            $result = ['error' => 'Delete functions not found in config'];
        }
        
        if (isset($result['error'])) {
            $errorMessage = extractSupabaseErrorMessage($result['error']);
            $errorMsg = addslashes($errorMessage);
            $showToastScript = "
                <script type='text/javascript'>
                    document.addEventListener('DOMContentLoaded', function() {
                        showToast('Error deleting question: $errorMsg', 'error');
                        setTimeout(function() {
                            window.location.href = 'add_edit_questions.php?id=$id1';
                        }, 2000);
                    });
                </script>
            ";
        } else {
            // Check if deletion was successful by verifying the question no longer exists
            $checkResponse = fetchData("questions?id=eq.$id");
            
            if (isset($checkResponse['error'])) {
                $errorMsg = addslashes(extractSupabaseErrorMessage($checkResponse['error']));
                $showToastScript = "
                    <script type='text/javascript'>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast('Error verifying deletion: $errorMsg', 'error');
                            setTimeout(function() {
                                window.location.href = 'add_edit_questions.php?id=$id1';
                            }, 2000);
                        });
                    </script>
                ";
            } elseif (empty($checkResponse)) {
                // Success - question no longer exists
                $showToastScript = "
                    <script type='text/javascript'>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast('Question deleted successfully from Supabase', 'success');
                            setTimeout(function() {
                                window.location.href = 'add_edit_questions.php?id=$id1';
                            }, 1500);
                        });
                    </script>
                ";
            } else {
                // Question still exists - deletion failed
                $showToastScript = "
                    <script type='text/javascript'>
                        document.addEventListener('DOMContentLoaded', function() {
                            showToast('Deletion failed - question still exists in database', 'error');
                            setTimeout(function() {
                                window.location.href = 'add_edit_questions.php?id=$id1';
                            }, 2000);
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
    <title>Delete Question</title>
</head>
<body>
    <?php echo $showToastScript; ?>
    
    <div class="content mt-3">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Deleting question...</span>
                            </div>
                            <p class="mt-3">Deleting question, please wait...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show spinner immediately
        showSpinner();
    });
    </script>

    <?php include "footer.php"?>
</body>
</html>