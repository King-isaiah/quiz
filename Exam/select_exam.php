<?php 
    include "header.php";
    include "connection.php";
    include "../superbase/config.php";
?>
<?php

if (isset($_GET['category'])) {
    $_SESSION['catsexam'] = $_GET['category'];
    $_SESSION['timeexam'] = $_GET['exam-minutes'];
}
$unique = $_SESSION['unique_id'];
$examName = $_SESSION['catsexam'];
//  echo  $examName;
 

// Comment out local MySQL connection and replace with Supabase
/*
$check = mysqli_query($link, "SELECT * FROM exam_results WHERE unique_id = '$unique' && exam_type = '$examName' ");
$numRows = mysqli_num_rows($check);
*/

// Supabase equivalent
$response = fetchData('exam_results?unique_id=eq.' . $unique . '&exam_type=eq.' . urlencode($examName));
$numRows = is_array($response) ? count($response) : 0;

// echo  $numRows; 
if ($numRows > 0) {    
    ?>
    <script>
        window.location = "result.php"
    </script>
    <?php
}

?>

<div class="row" style="margin: 0px; padding:0px; margin-bottom: 50px;">
    
    <div class="col-lg-6 col-lg-push-3" style="min-height: 500px; background-color: white;">
        <div class="col-lg-6 col-lg-push-3" style="min-height: 500px; width: 70%;  background-color: white;">
            <h1 style="color:black;">Exam Instructions</h1>
                                    
            <li>READ THE INSTRUCTIONS CLEARLY</li>
            <li>Failure to  submit your exam before the timer ends would lead to submission with out exam finishing</li>
            <li>Do not attempt to log out after you have clicked the start exam</li>
            <li>pay attention to the number of questions at the top right corner of above the questions to know how many questions you have</li>
            <li>once you reach the last question the next buttton would submit your exams</li>
            <li>The counter or countdown counts from hours to minutes to seconds to mili seconds</li>
            <li>once you start the exam do not make efffort to leave the page as the page would stil 
                consider you writing the exam and would mark you after your time runs out</li>
            <li>Once you are done with your exams and have been directed to the reult page do not for any reason refresh your page</li>
        
        </div>
        <?php 
            $examCategory = $_SESSION['catsexam'];
            
            // Comment out local MySQL connection and replace with Supabase
            /*
            $res=mysqli_query($link,"SELECT * from exam_category WHERE category = '$examCategory'");
            while($row=mysqli_fetch_array($res)){
                ?>
                <input type="button" class="btn btn-success form-control"
                value="start exam" 
                style="margin-top:10px; background-color:blue; color:white"
                onclick="set_exam_type_session('<?php echo $row['category'] ?>')"> </input>
                <?php
            }
            */
            
            // Supabase equivalent
            $response = fetchData('exam_category?category=eq.' . urlencode($examCategory));
            if (is_array($response)) {
                foreach ($response as $row) {
                    ?>
                    <input type="button" class="btn btn-success form-control"
                    value="start exam" 
                    style="margin-top:10px; background-color:blue; color:white"
                    onclick="set_exam_type_session('<?php echo $row['category'] ?>')"> </input>
                    <?php
                }
            }
        ?>
    </div>

</div>
<?php include "footer.php"?>
<script type="text/javascript">
    function set_exam_type_session(exam_category){
        var xmlhttp=new XMLHttpRequest();
        xmlhttp.onreadystatechange=function(){
            if(xmlhttp.readyState==4 && xmlhttp.status==200){
                window.location="dashboard.php";
            }
        };
        xmlhttp.open("GET","forajax/set_exam_type_session.php?exam_category="+ exam_category, true);
        xmlhttp.send(null);
    }
</script>