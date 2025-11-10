
<?php 
    session_start();
    include "header.php";
    include "../connection.php";
?>
        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>All Exam Results</h1>
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
                            <center>
                    <h1>Old Exam Results</h1>
                </center>
               

                <?php
                    $count=0;
                    $res= mysqli_query($link, "SELECT * from exam_results  order by id desc");
                    $count=mysqli_num_rows($res);
                    
                    if($count==0){
                        ?>
                         <center>
                            <h1>No Results Found</h1>
                        </center>
                        <?php
                    }else{
                        echo"<table class='table table-bordered'>";
                        echo"<tr style='background: color #006df0; '>";
                        echo "<th>"; echo "username"; echo"</th>";
                        echo "<th>"; echo "exam type"; echo"</th>";
                        echo "<th>"; echo "total question"; echo"</th>";
                        echo "<th>"; echo "correct answer"; echo"</th>";
                        echo "<th>"; echo "wrong answer"; echo"</th>";
                        echo "<th>"; echo "exam time"; echo"</th>";
                        echo"</tr>";

                        while($row=mysqli_fetch_array($res)){
                            echo"<tr>";
                            echo "<td>"; echo $row["username"]; echo"</td>";
                            echo "<td>"; echo $row["exam_type"]; echo"</td>";
                            echo "<td>"; echo $row["total_question"]; echo"</td>";
                            echo "<td>"; echo $row["correct_answer"]; echo"</td>";
                            echo "<td>"; echo $row["wrong_answer"]; echo"</td>";
                            echo "<td>"; echo $row["exam_time"]; echo"</td>";
                            echo"</tr>";
                        }
                        echo"</table>";
                    }
                ?>

                            </div>
                        </div> <!-- .card -->

                    </div>
                    <!--/.col-->

            
                    
                   

                                    

                                    

                                </div><!-- .animated -->
                            </div><!-- .content -->
<?php include "footer.php"?>






















<?php 
session_start();
include "header.php";
include "../connection.php";
// include "functions/set_category.php";
?>
<!-- 110 -->


<div class="breadcrumbs">
    <div class="col-sm-4">
        <div class="page-header float-left">
            <div class="page-title">
                <h1>All Exam Results</h1>
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
                        <center>
                            <h1>All Exam Results</h1>
                            <?php 
                                function generateCategorySelect($link) {
                                    $res = mysqli_query($link, "SELECT * FROM exam_category");

                                    if (!$res) {
                                        die("Database query failed: " . mysqli_error($link));
                                    }

                                    $selectHTML = '<select name="examSubject" id="category" onchange="setSelectedCategory()">';

                                    while ($row = mysqli_fetch_assoc($res)) {
                                        $id = $row['id']; 
                                        $name = $row['category'];
                                        // Set the value to the ID and wrap values in quotes
                                        $selectHTML .= "<option value=\"$name\" name='examSubject'>$name</option>";                                       
                                    }

                                    $selectHTML .= '</select>';

                                    // Free the result set
                                    mysqli_free_result($res);

                                    return $selectHTML;
                                }
                            ?>
                           <script>
                                function setSelectedCategory() {
                                    let category = document.getElementById('category').value; // This will now be the ID

                                    let xhr = new XMLHttpRequest();
                                    xhr.open("POST", "set_category.php", true);
                                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                                    xhr.onreadystatechange = function () {
                                        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                                            alert('Category set successfully: ' + category);
                                        }
                                    };
                                    xhr.send("examSubject=" + encodeURIComponent(category));
                                }
                            </script>
                            
                            <?php
                            // $_SESSION['exam_subject'] = 'php';
                    
                                // echo "<p>Selected Category ID: " . htmlspecialchars($_SESSION['exam_subject']) . "</p>";
                           
                            ?>

                        </center>

                        <table id="examTable" class="table table-bordered display" style="width:100%">
                            <form action="" method="post">
                                
                                <?php  generateCategorySelect($link); ?>
                            </form> 
                            <thead>
                                <tr style="background-color: #006df0; color: white;">
                                    <th>Username</th>
                                    <th>Exam Name</th>
                                    <th>Total Questions</th>
                                    <th>Correct Answers</th>
                                    <th>Wrong Answers</th>
                                    <th>Time Submitted</th>
                                    <th>Time_finished</th>
                                    <th>Min_Spent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be filled by DataTables -->
                            </tbody>
                        </table>

                    </div>
                </div> <!-- .card -->
            </div>
            <!--/.col-->
        </div><!-- .animated -->
    </div><!-- .content -->

   
    <script src="js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#examTable').DataTable({
                "ajax": {
                    "url": "functions/fetch_exams_byYear.php",
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "username" },
                    { "data": "exam_type" },
                    { "data": "total_question" },
                    { "data": "correct_answer" },
                    { "data": "wrong_answer" },
                    { "data": "exam_time" },
                    { "data": "time_finished" },
                    { "data": "mins_spent" }
                ]
            });
        });
    </script>
</div>

<?php include "footer.php"; ?>