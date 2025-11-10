<?php 
session_start();
include "header.php";
include "../connection.php";
include "../superbase/config.php"; // Add Supabase config
?>
<!-- 110 -->

<?php
    // Determine which connection to use
    $useLocal = isset($_COOKIE['useLocalDB']) && $_COOKIE['useLocalDB'] === 'true';
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
                            <h1>Users Status</h1>
                        </center>

                        <table id="examTable" class="table table-bordered display" style="width:100%">
                            <thead>
                                <tr style="background-color: #006df0; color: white;">
                                    <th>Username</th>
                                    <th>Full_Name</th>
                                    <th>Unique_Id</th>
                                    <th>Status</th>   
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
          
        </div>
    </div>

   
    <script src="js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#examTable').DataTable({
                "ajax": {
                    "url": "functions/active-users.php",
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "username" },
                    { "data": "fullName" },
                    { "data": "unique_id" },
                    { "data": "status" }
                    
                ]
            });
        });
    </script>
</div>

<?php include "footer.php"; ?>