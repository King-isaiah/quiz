<?php session_start()?>
<?php include "header.php"?>

<?php 
    // include "../connection.php";
    // if(!isset($_SESSION["admin"])){
    //     ?>
         <script type="text/javascript"> 
    //         window.location="index.php";
    //     </script>
    /    <?php
    // } 
    // $id= $_GET["id"];
    // $res = mysqli_query($link, "select * from exam_category where id=$id");
    // while($row=mysqli_fetch_array($res)){
    //     $exam_category = $row["category"];
    //     $exam_time = $row["exam_time_in_minutes"];
    //     $exam_year = $row["year"];
    //     $exam_price = $row["price"];
    // }

?>
<!-- superbase connection -->
<?php 
    include "../superbase/config.php";
  
    
    if(!isset($_SESSION["admin"])){
        ?>
        <script type="text/javascript"> 
            window.location="index.php";
        </script>
        <?php
    } 
    
    $id = $_GET["id"];
    
    
    $response = fetchData("exam_category?id=eq.$id");
    
    // Initialize variables
    $exam_category = "";
    $exam_time = "";
    $exam_year = "";
    $exam_price = "";
    
    if (is_array($response) && count($response) > 0 && !isset($response['error'])) {
        $row = $response[0];
        $exam_category = $row["category"] ?? "";
        $exam_time = $row["exam_time_in_minutes"] ?? "";
        $exam_year = $row["year"] ?? "";
        $exam_price = $row["price"] ?? "";
    }
?>
        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Edit Exam</h1>
                    </div>
                </div>
            </div>
            
        </div>

        <div class="content mt-3">
            <div class="animated fadeIn">


                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                           
                            <form name="form1" action="" method="post">
                                <div class="card-body">
                                
                                    <div class="col-lg-6">
                                        <div class="card">
                                            <div class="card-header"><strong>Edit Exam</strong></div>
                                            <div class="card-body card-block">
                                                <div class="form-group">
                                                    <label for="company" class=" form-control-label">New Exam Category</label>
                                                    <input type="text" name="examname" placeholder="Add Exam Category" class="form-control" value="<?php echo $exam_category; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="vat" class=" form-control-label">
                                                    Exam Time IN minutes
                                                    </label>
                                                    <input type="text" name="examtime" placeholder="Add Exam Time In Minutes" class="form-control"  value="<?php echo $exam_time; ?>">
                                                </div>             
                                                <div class="form-group">
                                                    <label for="vat" class=" form-control-label"> Year Of Exam</label>
                                                    <input type="text" name="year" placeholder="Add Exam Time In Minutes" class="form-control"  value="<?php echo $exam_year; ?>">
                                                </div>             
                                                <div class="form-group">
                                                    <label for="vat" class=" form-control-label">Amount/Price Of Exam</label>
                                                    <input type="text" name="price" placeholder="Add Exam Time In Minutes" class="form-control"  value="<?php echo $exam_price; ?>">
                                                </div>             
                                                <div class="form-group">                                                
                                                    <input type="submit" name="submit11" value="Update Exam" class="btn btn-success" >
                                                </div>             
                                            </div>    
                                                
                                        </div>
                                    </div>

                                    
                                </div>

                                </div>
                            </form>
                        </div> <!-- .card -->
                    </div>
                    <!--/.col-->                          
                                    
                                    

                </div><!-- .animated -->
            </div><!-- .content -->
<?php 
    // if(isset($_POST["submit11"])){
    //     mysqli_query($link,"update exam_category set category='$_POST[examname]',
    //     exam_time_in_minutes='$_POST[examtime]',year='$_POST[year]',price='$_POST[price]' where id=$id")
    //       or die(mysqli_error($link));
    //      ?>
          <script type="text/javascript">
           
    //         window.location= "edit_exam_category.php";
    //      </script>
         <?php

    // }

    // if(isset($_POST["submit11"])){
    //     $updateData = [
    //         'category' => $_POST['examname'],
    //         'exam_time_in_minutes' => $_POST['examtime'],
    //         'year' => $_POST['year'],
    //         'price' => $_POST['price']
    //     ];
        
    
    //     $result = updateData('exam_category', $id, $updateData);
        
    //     if (isset($result['error'])) {
        
    //         die("Error updating category: " . $result['error']);
    //     }
    //     ?>
         <script type="text/javascript">
    //         window.location = "edit_exam_category.php";
    //     </script>
         <?php
    // }


     
  

    

if(isset($_POST["submit11"])){
    $updateData = [
        'category' => $_POST['examname'],
        'exam_time_in_minutes' => $_POST['examtime'],
        'year' => $_POST['year'],
        'price' => $_POST['price']
    ];
    
    $result = updateData('exam_category', $id, $updateData);
    
    if (isset($result['error'])) {
        $errorMessage = extractSupabaseErrorMessage($result['error']);
        ?>
        <script type="text/javascript">
            // Wait for the page to fully load and header.js to be available
            window.addEventListener('load', function() {
                console.log('Page loaded, checking for showToast...');
                console.log('showToast available:', typeof showToast !== 'undefined');
                
                if (typeof showToast !== 'undefined') {
                    console.log('Calling showToast with error message');
                    showToast("<?php echo addslashes($errorMessage); ?>", 'error');
                } else {
                    console.log('showToast not available, using alert');
                    alert("Error: <?php echo addslashes($errorMessage); ?>");
                }
            });
        </script>
        <?php
    } else {
        ?>
        <script type="text/javascript">
            window.addEventListener('load', function() {
                console.log('Page loaded, showing success toast...');
                
                if (typeof showToast !== 'undefined') {
                    showToast("Exam updated successfully!", 'success');
                    setTimeout(function() {
                        window.location = "edit_exam_category.php";
                    }, 1500);
                } else {
                    window.location = "edit_exam_category.php";
                }
            });
        </script>
        <?php
    }
}
?>

<?php include "footer.php"?>