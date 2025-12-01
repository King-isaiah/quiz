<?php 
session_start();
include "header.php";
// include "../connection.php";
include "../superbase/config.php";



?>
<style>
    .button {
            background-color: green; /* Button color */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: darkgreen; /* Darker shade on hover */
        }
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Blurred background */
            z-index: 1000; /* On top of other content */
            justify-content: center;
            align-items: flex-start; /* Align at the top */
            padding-top: 50px; /* Space from the top */
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            width: 300px; /* Width of modal */
            margin: auto; /* Center horizontally */
        }
</style>

<div class="breadcrumbs">
    <div class="page-header">
        <div class="page-title" style="display: flex; justify-content:space-between" >
            <h1>All Exam Results</h1>
            <div>
                <button class="button" id="openModal">Select Category for Countdown</button>
                
            </div>
        </div>
    </div>    
</div>
<div class="modal" id="modal">
    <div class="modal-content">   
        <button id="closeModal">x</button>     
        <p>Select your category for the countdown.</p>
        <!-- the two bellow is for connection with local host -->
        <?php  
            // $sql = "SELECT id, category FROM exam_category"; 
            // $result = $link->query($sql);

            // // Start the dropdown
            // echo '<select id="exam_category" name="exam_category" class="form-control" required>';
            // echo '<option value="">Select Exam Category</option>';
            // if ($result->num_rows > 0) {
            //     while ($row = $result->fetch_assoc()) {
            //         echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['category']) . '</option>';
            //     }
            // } else {
            //     echo '<option value="">No categories available</option>';
            // }
            // echo '</select>';
        ?>
        <?php          
            // $sql = "SELECT category FROM exam_category WHERE countDown = 'active'";
            // $result = $link->query($sql);

            // // Initialize variable for the active category
            // $activeCategory = '';

            // if ($result->num_rows > 0) {
            //     $row = $result->fetch_assoc();
            //     $activeCategory = htmlspecialchars($row['category']);
            // }
        ?>
        <!-- the two bellow are connection with superbase -->
        <?php  
            // Fetch exam categories from Supabase
            $response = fetchData('exam_category');
            
            echo '<select id="exam_category" name="exam_category" class="form-control" required>';
            echo '<option value="">Select Exam Category</option>';
            
            if (is_array($response) && count($response) > 0 && !isset($response['error'])) {
                foreach ($response as $row) {
                    echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['category']) . '</option>';
                }
            } else {
                echo '<option value="">No categories available</option>';
            }
            echo '</select>';
        ?>
        <?php          
            $response = fetchData('exam_category?countDown=eq.active');
            $response =universalFetch('exam_category', ['countDown' => 'active']);
            
            $activeCategory = '';

            if (is_array($response) && count($response) > 0 && !isset($response['error'])) {
                $activeCategory = htmlspecialchars($response[0]['category']);
            }
        ?>


        <input type="text" value="<?php echo $activeCategory; ?>" readonly class="form-control" />
        <button id="submitCountdown">Submit</button>




    </div>
</div>

<div class="content mt-3">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <center>
                            <h1>Exam Categories</h1>
                        </center>

                        <table id="examTable" class="table table-bordered display" style="width:100%">
                            <thead>
                                <tr style="background-color: #006df0; color: white;">
                                    <th scope="col">Exam Name</th>
                                    <th scope="col">Exam Year</th>
                                    <th scope="col">Exam Time</th>
                                    <th scope="col">Exam Price</th>
                                    <!-- <th scope="col">Book Cover</th> -->
                                    <th scope="col">Edit</th>
                                    <th scope="col">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                
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
        const openModalButton = document.getElementById("openModal");
        const modal = document.getElementById("modal");
        const closeModalButton = document.getElementById("closeModal");

        openModalButton.onclick = function() {
            modal.style.display = "flex"; // Show modal
        }

        closeModalButton.onclick = function() {
            modal.style.display = "none"; // Hide modal
        }

        
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }

        document.getElementById("submitCountdown").onclick = function() {
            var selectedId = document.getElementById("exam_category").value;
            var selected_category = document.getElementById("exam_category");

            if (selectedId) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "functions/update_countdown.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        alert(xhr.responseText); 
                    }
                };
                // xhr.send("selected_id=" + selectedId, "selected_category" + selected_category);
                xhr.send("selected_id=" + selectedId + "&selected_category=" + encodeURIComponent(selected_category.options[selected_category.selectedIndex].text));
            } else {
                alert("Please select a category.");
            }
        };

        $(document).ready(function() {
            $('#examTable').DataTable({
                "ajax": {
                    "url": "functions/fetch_exam_category.php",
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "category" },
                    { "data": "year" },
                    { "data": "exam_time_in_minutes" },
                    { "data": "price" },
                    
                    {
                        "data": null, // Set to null to allow custom rendering
                        "render": function(data, type, row) {
                            return '<a href="edit_exam.php?id=' + row.id + '">Edit</a>';
                        }                  
                    },                 
                    {
                        "data": null, 
                        "render": function(data, type, row) {
                            return '<a href="delete.php?id=' + row.id + '&category=' + row.category + '">Deleted</a>'
                        }                  
                    }                  
                ]
            });
        });
    </script>
</div>

<?php include "footer.php"; ?>