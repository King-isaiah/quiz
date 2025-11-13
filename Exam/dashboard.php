<?php session_start(); ?>
<?php 
    include "header.php";
    // include "connection.php";
    include "../superbase/config.php"; // Added Supabase config
    $unique = $_SESSION['unique_id'];
    $examName =  $_SESSION['catsexam'];
   echo  $examName;
    
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
            window.location = "old_exam_results.php";
        </script>
        <?php
        exit();
    }
      // CLEAR SESSION CACHE WHEN STARTING NEW EXAM
    // unset($_SESSION['questions_order']);
    // unset($_SESSION['answer']);
?>

<div class="row" style="margin: 0px; padding:0px; margin-bottom: 50px;">

    <div class="col-lg-6 col-lg-push-3" style="min-height: 500px; background-color: white;">
        <!-- start editing -->
            <br>
            <div class="row">
            <br>
            <div class="col-lg-2 col-lg-push-10">
                <div id="current_que" style="float:left">0</div>
                <div style="float:left">/</div>
                <div id="total_que" style="float:left">0</div>
            </div>

            <div class="row" style="margin-top: 30px;">
                <div class="row">
                    <div class="col-lg-10 col-lg-push-1" style="min-height:300px;
                    background-color:white" id="load_questions"></div>
                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="col-lg-6 col-lg-push-3" style="min-height:50px">
                    <div class="col-lg-12 text-center">
                        <input type="button" class="btn btn-warning" value="Previous" onclick="load_previous();">&nbsp
                        <input type="button" class="btn btn-success" id='test' value="Next" onclick="load_next();">
                    </div>
                </div>
            </div>
            </div>
        <!-- editing ends here-->
    </div>

</div>

<script type="text/javascript">
    function load_total_que(){
        var xmlhttp=new XMLHttpRequest();
        xmlhttp.onreadystatechange=function(){
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200){
               document.getElementById("total_que").innerHTML=xmlhttp.responseText;
            }
        };
        xmlhttp.open("GET","forajax/load_total_que.php", true);
        xmlhttp.send(null);
    }
    
    var questionno="1";
    
    load_questions(questionno);

    
   

    function load_questions(questionno) {
        console.log(questionno);
        document.getElementById("current_que").innerHTML = questionno;

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                if (xmlhttp.responseText === "over") {
                    window.location.href = "result.php"; // Redirect if no questions left
                } else {
                    document.getElementById("load_questions").innerHTML = xmlhttp.responseText;
                    load_total_que(); // Call this if you need to update total questions display
                }
            }
        };

        xmlhttp.open("GET", "forajax/load_questions.php?questionno=" + questionno, true);
        xmlhttp.send(null);
    }


    function radioclick(radiovalue, questionno){
        var xmlhttp=new XMLHttpRequest();
        xmlhttp.onreadystatechange=function(){
            if(xmlhttp.readyState==4 && xmlhttp.status==200){
               
            }
        };
        xmlhttp.open("GET","forajax/save_answer_in_session.php?questionno="+ questionno +"&value1="+radiovalue, true);
        xmlhttp.send(null);
        
    }



    function load_previous(){
        if(questionno=="1"){
            load_questions(questionno);
        }else{
            questionno = eval(questionno)-1;
            load_questions(questionno);


            var xmlhttp=new XMLHttpRequest();
            xmlhttp.onreadystatechange=function(){
                if(xmlhttp.readyState == 4 && xmlhttp.status == 200){
                let log = xmlhttp.responseText;
                console.log('we testing questions'+log)
                    if(questionno != log){
                        console.log('we moving'+log)
                        document.getElementById("test").value= 'next'; 
                    }
                }
            };
            xmlhttp.open("GET","forajax/load_total_que.php", true);
            xmlhttp.send(null);
        }
    }

    function load_next(){
        questionno = eval(questionno)+1;
        load_questions(questionno);
        

        var xmlhttp=new XMLHttpRequest();
        xmlhttp.onreadystatechange=function(){
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200){
               let log = xmlhttp.responseText;
               console.log('total number of questions'+log)
                if(questionno == log ){
                    console.log('yes lets go'+log)
                    document.getElementById("test").value= 'submit'; 
                }
            }
        };
        xmlhttp.open("GET","forajax/load_total_que.php", true);
        xmlhttp.send(null);
        
       
    }
    console.log(questionno)
</script>
<?php include "footer.php"?>