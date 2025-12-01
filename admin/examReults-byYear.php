<?php 
session_start();
include "header.php";
// include "../connection.php";
include "../superbase/config.php"; // Supabase connection
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
                            <h1>Exam Results By Year</h1>
                            <form action="" method="post">
                                <div style="padding-left: 30em; display: flex; align-items: center; gap: 10px;"> 
                                    <?php
                                        function generateCategorySelect($link) {
                                            // Check database preference from cookie
                                            $useLocal = isset($_COOKIE['useLocalDB']) && $_COOKIE['useLocalDB'] === 'true';
                                            
                                            if ($useLocal) {
                                                // Use Local MySQL
                                                // $res = mysqli_query($link, "SELECT * FROM exam_category");

                                                // if (!$res) {
                                                //     die("Database query failed: " . mysqli_error($link));
                                                // }
                                                $currentYear = date("Y");
                                                
                                                $selectHTML = '<select name="examSubject" id="category" style="padding: 10px; border-radius: 4px;">';
                                                if (!isset($_SESSION['exam_subject'])) {
                                                    $selectHTML .= "<option value=''>{$currentYear}  Select year</option>";
                                                } else {
                                                    $selectHTML .= "<option value=''>Select Year</option>";
                                                }
                                                // while ($row = mysqli_fetch_assoc($res)) {
                                                //     $id = $row['id']; 
                                                //     $year = $row['year']; 
                                                //     $name = $row['category'];                                        
                                                //     $selectHTML .= "<option value=\"$name\">$year</option>";                                       
                                                // }

                                                // mysqli_free_result($res);
                                            } else {
                                                // Use Supabase
                                                $response = fetchData('exam_category');
                                                
                                                $currentYear = date("Y");
                                                
                                                $selectHTML = '<select name="examSubject" id="category" style="padding: 10px; border-radius: 4px;">';
                                                if (!isset($_SESSION['exam_subject'])) {
                                                    $selectHTML .= "<option value=''>{$currentYear} Select Year</option>";
                                                } else {
                                                    $selectHTML .= "<option value=''>Select Year</option>";
                                                }
                                                
                                                if (is_array($response) && count($response) > 0 && !isset($response['error'])) {
                                                    foreach ($response as $row) {
                                                        $id = $row['id']; 
                                                        $year = $row['year'] ?? $row['category']; 
                                                        $name = $row['category'];                                        
                                                        $selectHTML .= "<option value=\"$name\">$year</option>";                                       
                                                    }
                                                } else {
                                                    $selectHTML .= "<option value=''>No categories available</option>";
                                                }
                                            }

                                            $selectHTML .= '</select>';
                                            return $selectHTML;
                                        }
                                    
                                        // echo generateCategorySelect($link); 
                                    ?>
                                    <button type="button" id="searchButton" onclick="searchByCategory()" 
                                    style="background-color: #006df0; color: white; border: 1px solid #0056b3; padding: 10px 15px; border-radius: 4px; 
                                    cursor: pointer;">
                                        Search
                                    </button>
                                </div>
                                <?php                                     
                                    // Display the selected category if it exists
                                    if (isset($_SESSION['exam_subject'])) {
                                        echo "<p id='selectedCategoryDisplay'>Selected Exam: " . htmlspecialchars($_SESSION['exam_subject']) . "</p>";
                                    } else {
                                        echo "<p id='selectedCategoryDisplay'>Pls select a year.</p>";
                                    }
                                    
                                    // Display current database mode
                                    $useLocal = isset($_COOKIE['useLocalDB']) && $_COOKIE['useLocalDB'] === 'true';
                                    $dbMode = $useLocal ? 'Local MySQL' : 'Supabase';
                                    echo "<p style='color: #666; font-size: 12px; margin-top: 5px;'>Current Database: <strong>$dbMode</strong></p>";
                                ?>
                            </form>
                        </center>

                        <table id="examTable" class="table table-bordered display" style="width:100%">
                            <thead>
                                <tr style="background-color: #006df0; color: white;">
                                    <th>Username</th>
                                    <th>Exam Name</th>
                                    <th>Total Questions</th>
                                    <th>Correct Answers</th>
                                    <th>Wrong Answers</th>
                                    <th>Time Submitted</th>
                                    <th>Time Finished</th>
                                    <th>Minutes Spent</th>
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
</div>
   
    <script src="js/jquery-3.6.0.min.js"></script>
    <script>
        
        function searchByCategory() {            
            const exam = document.getElementById('category').value;
            alert(exam);

            if (!exam) {
                alert("Please choose an exam.");
                return;
            } 

            fetch('functions/fetch_exams_byYear.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ exam: exam })                     
            })           
            .then(response => {
                // alert('to the first then');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json(); 
            })
            .then(data => {
                // alert('to the 2nd then. dat recieved');
                console.log("Data received:", data);
                
                let table;
                if ($.fn.DataTable.isDataTable('#examTable')) {
                    table = $('#examTable').DataTable();
                    table.clear(); 
                } else {
                    table = $('#examTable').DataTable(); 
                }
               
                if (data && data.length > 0) {
                    data.forEach(record => {
                        table.row.add([
                            record.username || 'N/A',
                            record.exam_type || 'N/A',
                            record.total_question || '0',
                            record.correct_answer || '0',
                            record.wrong_answer || '0',
                            record.exam_time || 'N/A',
                            record.time_finished || 'N/A',
                            record.mins_spent || '0'
                        ]);
                    });
                } else {
                    // No results found
                    table.row.add([
                        'No Result yet', '', '', '', '', '', '', ''
                    ]);
                }

                // Redraw the table after adding all rows
                table.draw();
            })
            .catch(error => {
                console.error("There was a problem with the fetch operation:", error);
                alert('Error fetching data. Please try again.');
            });
        }
       
    </script>
</div>

<?php include "footer.php"; ?>