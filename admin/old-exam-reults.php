<?php 
session_start();
include "header.php";
// include "../connection.php";
include "../superbase/config.php";
?>
<!-- 110 -->

<?php
    // Determine which connection to use
    $useLocal = isset($_COOKIE['useLocalDB']) && $_COOKIE['useLocalDB'] === 'true';
    
    // Get category_id from URL if exists
    $selected_category_id = $_GET['category_id'] ?? '';
    $selected_category_name = '';
    
    // Fetch all exam categories for dropdown
    $exam_categories = [];
    if ($useLocal) {
        // LOCAL MYSQL CONNECTION
        $res = mysqli_query($link, "SELECT * FROM exam_category ORDER BY category");
        while($row = mysqli_fetch_array($res)) {
            $exam_categories[] = $row;
        }
    } else {
        // SUPABASE CONNECTION
        $response = fetchData("exam_category?order=category.asc");
        if (is_array($response) && !isset($response['error'])) {
            $exam_categories = $response;
        }
    }
    
    // Get selected category name
    if ($selected_category_id) {
        if ($useLocal) {
            $res = mysqli_query($link, "SELECT category FROM exam_category WHERE id=$selected_category_id");
            if ($row = mysqli_fetch_array($res)) {
                $selected_category_name = $row['category'];
            }
        } else {
            $response = fetchData("exam_category?id=eq.$selected_category_id");
            if (is_array($response) && !isset($response['error']) && count($response) > 0) {
                $selected_category_name = $response[0]['category'] ?? '';
            }
        }
    }
?>

<div class="breadcrumbs">
    <div class="col-sm-12">
        <div class="page-header float-left">
            <div class="page-title d-flex justify-content-between align-items-center w-100">
                <h1 class="mb-0">All Exam Results</h1>
                <div class="form-group mb-0">
                    <label for="categoryFilter" class="form-label mr-2 mb-0">Filter by Exam Category:</label>
                    <select class="form-control d-inline-block" style="width: auto;" id="categoryFilter" onchange="filterResults()">
                        <option value="">All Categories</option>
                        <?php foreach ($exam_categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                <?php echo ($selected_category_id == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['category']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
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
                        <?php if ($selected_category_name): ?>
                            <div class="alert alert-info mb-4">
                                <strong>Currently viewing:</strong> <?php echo htmlspecialchars($selected_category_name); ?>
                                <a href="all_results.php" class="btn btn-sm btn-outline-secondary ml-2">Clear Filter</a>
                            </div>
                        <?php endif; ?>

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

    <script src="js/jquery-3.6.0.min.js"></script>
    <script>
        function filterResults() {
            const categoryId = document.getElementById('categoryFilter').value;
            let url = 'fetch_exam_results.php';
            
            if (categoryId) {
                url += '?category_id=' + categoryId;
            }
            
            // Reload DataTable with new URL
            $('#examTable').DataTable().ajax.url(url).load();
        }

        $(document).ready(function() {
            let ajaxUrl = 'fetch_exam_results.php';
            <?php if ($selected_category_id): ?>
                ajaxUrl += '?category_id=<?php echo $selected_category_id; ?>';
            <?php endif; ?>

            $('#examTable').DataTable({
                "ajax": {
                    "url": ajaxUrl,
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
                ],
                "pageLength": 5,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
            });
        });
    </script>
</div>

<?php include "footer.php"; ?>