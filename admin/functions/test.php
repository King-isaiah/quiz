<?php 
session_start();
include "header.php";
// include "../connection.php";
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
                            <h1>Active Users</h1>
                        </center>

                        <form method="GET" action="">
                            <input type="text" id="search" name="search" placeholder="Search by username" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            
                        </form>
                        <table id="examTable" class="table table-bordered display" style="width:100%">
                            <thead>
                                <tr style="background-color: #006df0; color: white;">
                                    <th>SN</th>
                                    <th>Username</th>
                                    <th>Full_Name</th>
                                    <th>Unique_Id</th>
                                    <th>Status</th>                                    
                                </tr>
                            </thead>
                            <tbody>                               
                                <?php
                                    $limit = 5; // Number of records per page
                                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
                                    $offset = ($page - 1) * $limit; // Calculate offset

                                    // Get search term
                                    $searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($link, $_GET['search']) : '';

                                    // Build query
                                    $query = "SELECT * FROM registration WHERE username LIKE '%$searchTerm%' ORDER BY id DESC LIMIT $limit OFFSET $offset";
                                    $res = mysqli_query($link, $query);

                                    // Count total records for pagination
                                    $countQuery = mysqli_query($link, "SELECT COUNT(*) as total FROM registration WHERE username LIKE '%$searchTerm%'");
                                    $totalCount = mysqli_fetch_assoc($countQuery)['total'];
                                    $totalPages = ceil($totalCount / $limit); // Total number of pages
                                    $count = 0;

                                    // Display records
                                    while ($row = mysqli_fetch_assoc($res)) {
                                        ?>
                                        <tr>
                                            <th scope="row"><?php echo $count + 1; ?></th>
                                            <td><?php echo $row['username']; ?></td>
                                            <td><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></td>
                                            <td><?php echo $row['unique_id']; ?></td>
                                            <td><?php echo $row['status']; ?></td>
                                        </tr>
                                        <?php
                                        $count++;
                                    }
                                ?>
                                                                
                                
                              
                            </tbody>
                            

                            <div class="pagination">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($searchTerm); ?>">« Previous</a>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchTerm); ?>" <?php if ($i == $page) echo 'class="active"'; ?>>
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($searchTerm); ?>">Next »</a>
                                <?php endif; ?>
                            </div>
                        </table>

                    </div>
                </div> <!-- .card -->
            </div>
            <!--/.col-->
        </div><!-- .animated -->
    </div><!-- .content -->

<script>
    $(document).ready(function() {
        $('#search').on('input', function() {
            var query = $(this).val();
            
            $.ajax({
                url: 'search.php',
                method: 'GET',
                data: { query: query },
                success: function(data) {
                    var results = JSON.parse(data);
                    var tbody = $('tbody');
                    tbody.empty(); // Clear existing rows

                    results.forEach(function(row, index) {
                        tbody.append('<tr>' +
                            '<th scope="row">' + (index + 1) + '</th>' +
                            '<td>' + row.username + '</td>' +
                            '<td>' + row.firstname + ' ' + row.lastname + '</td>' +
                            '<td>' + row.unique_id + '</td>' +
                            '<td>' + row.status + '</td>' +
                        '</tr>');
                    });
                }
            });
        });
    });
</script>
