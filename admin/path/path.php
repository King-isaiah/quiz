<?php 
session_start();
include "../../superbase/config.php";

$editFormAction = $_SERVER['PHP_SELF'];

// Fetch users from registration table using Supabase
$users_response = fetchData('registration');
$row_username = is_array($users_response) && !isset($users_response['error']) ? $users_response : [];

// Fetch exam categories from Supabase ordered by year
$categories_response = universalFetch('exam_category', [], [], 'year');
$row_category = is_array($categories_response) && !isset($categories_response['error']) ? $categories_response : [];

?>
<head>
    <link rel="shortcut icon" href="../../img/logo2.png">
    <link rel="stylesheet" href="vendors/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">    
    <link rel="stylesheet" href="../css/path.css">
    
</head>
<body>
    <main class="" style="padding-top: 0;">
        <div class="overlay">
              
            <div class="module">
                <div class='top' onclick="remove()">
                    <i class="fa fa-times" > </i>
                </div>
                <form method="POST" enctype="text/plain" name="form_save" id="userForm" onsubmit="submitForm(event)">                  
                    <table class="responsive-table-single form-table" id="modal-table">
                        <tbody>
                            <tr colspan="2">
                                <td>
                                    <div class="user-info-section">
                                        <div class="form-group">
                                            <label>Students/Users</label>
                                            <select id="users" class="form-control" name="username" required>
                                                <option value="">Select Sub Group</option>
                                                <?php
                                                if (is_array($row_username) && count($row_username) > 0) {
                                                    foreach ($row_username as $user) {  
                                                ?>
                                                <option value="<?php echo htmlspecialchars($user['username'] ?? '') ?>">
                                                    <?php echo htmlspecialchars($user['username'] ?? '') ?></option>
                                                <?php
                                                    }
                                                } else {
                                                ?>
                                                <option value="">No users available</option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                                                            
                                        </div>
                                    </div>
                                    
                                    <div class="user-info-row">
                                        <div class="form-group">
                                            <label>Unique ID</label>
                                            <input type="text" id="unique_id" class="form-control" name="unique_id" readonly>
                                        </div>
                                
                                
                                        <div class="form-group">
                                            <label>Student Email</label>
                                            <input type="email" class="form-control"  id="student_email" name="student_email" readonly>
                                        </div>
                                    </div>
                                    
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="status-section">
                                        <div class="form-group">
                                            <label>Category/Exam</label>
                                            <select id="category" class="form-control" name="category" required>
                                                <option value="" >Select Sub Group</option>
                                                <?php
                                                if (is_array($row_category) && count($row_category) > 0) {
                                                    foreach ($row_category as $category) {  
                                                ?>
                                                <option value="<?php echo htmlspecialchars($category['category'] ?? '') ?>">
                                                    <?php echo htmlspecialchars($category['category'] ?? '') ?></option>
                                                <?php
                                                    }
                                                } else {
                                                ?>
                                                <option value="">No categories available</option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="success">Success</option>
                                                <option value="null">Null</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                            
                                    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                               
                    
                    <button type="submit" class='space-center submit-btn' onsubmit="submitForm(event)">Submit</button>
                </form>
            </div>
        </div>
       
    </main>
    <script src="../js/path.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        
       
    </script>
</body>