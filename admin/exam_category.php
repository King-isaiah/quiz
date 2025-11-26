<?php session_start()?>
<?php include "header.php"?>

<?php 
    // include "../connection.php";
    if(!isset($_SESSION["admin"])){
        ?>
        <script type="text/javascript"> 
            window.location="index.php";
        </script>
        <?php
    }
?>
        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Add Exam Categories</h1>
                    </div>
                </div>
            </div>
            
        </div>
      

       

        
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <section class="form signup">
                                <form name="form1" action="" method="post">
                                    <div class="card-body">
                                        <div class="error-txt"></div>
                                    
                                        <div class="col-lg-8">
                                            <div class="card">
                                                <div class="card-header"><strong>Add Exam Categories</strong></div>

                                                <div class="card-body card-block">
                                                    <div class="form-row">
                                                        <div class="form-group">
                                                            <label for="company" class="form-control-label">New Exam Category</label>
                                                            <input type="text" name="examname" placeholder="Add Exam Category" class="form-control" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="company" class="form-control-label">Year Of Exam Category</label>
                                                            <input type="text" name="year" placeholder="Add Year Of Exam Category" class="form-control" required>
                                                        </div>
                                                    </div>

                                                    <div class="form-row">
                                                        <div class="form-group">
                                                            <label for="vat" class="form-control-label">Exam Time IN minutes</label>
                                                            <input type="text" name="examtime" placeholder="Add Exam Time In Minutes" class="form-control" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="vat" class="form-control-label">Price To Pay</label>
                                                            <input type="text" name="price" placeholder="Add Amount For Exam" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-row">
                                                        <div class="form-group">
                                                            <label for="start_date" class="form-control-label">Start Date for Exam</label>
                                                            <input type="datetime-local" id="start_date" name="start_date" class="form-control" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="vat" class="form-control-label">End Date for Validity</label>
                                                            <input type="datetime-local" id="end_date" name="end_date" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    
                                                    
                                                    <div class="field image">
                                                        <label>Select profile Image</label>
                                                        <input type="file" name="images" required />
                                                    </div>
                                                    <div class="form-group button">
                                                        <input type="submit" name="submit11" value="Add Exam" class="btn btn-success">
                                                    </div>
                                                    
                                                </div>
                                                
                                            </div>
                                        </div>
                                            
                                        

                                        

                                        

                                    </div>
                                </form>
                            </section>
                            
                        </div> <!-- .card -->
                    </div>
                    <!--/.col-->                          
                                    
                                    

                </div><!-- .animated -->
            </div><!-- .content -->

 <script src="js/addCategory.js"></script>
 <script src="js/jquery-3.6.0.min.js"></script>
  

<?php include "footer.php"?>