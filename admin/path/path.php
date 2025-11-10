<?php 
session_start();

include('../../connection.php');

$editFormAction = $_SERVER['PHP_SELF'];


$query_username = mysqli_query($link, "SELECT id, username FROM registration") or die(mysqli_error($mysqli));
$row_username = mysqli_fetch_assoc($query_username);

$query_category = mysqli_query($link, "SELECT id, category FROM exam_category ORDER BY year ASC") or die(mysqli_error($mysqli));
$row_category = mysqli_fetch_assoc($query_category);


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
                                                    do {  
                                                ?>
                                                <option value="<?php echo $row_username['username']?>">
                                                    <?php echo $row_username['username']?></option>
                                                <?php
                                                    } 
                                                    while ($row_username = mysqli_fetch_assoc($query_username));
                                                    $rows = mysqli_num_rows($query_username);
                                                    if($rows > 0) {
                                                        mysqli_data_seek($query_username, 0);
                                                        $row_username = mysqli_fetch_assoc($query_username);
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
                                                    do {  
                                                ?>
                                                <option value="<?php echo $row_category['category']?>">
                                                    <?php echo $row_category['category']?></option>
                                                <?php
                                                } while ($row_category = mysqli_fetch_assoc($query_category));
                                                $rows = mysqli_num_rows($query_category);
                                                if($rows > 0) {
                                                    mysqli_data_seek($query_category, 0);
                                                    $row_category = mysqli_fetch_assoc($query_category);
                                                }?>
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


