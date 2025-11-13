<?php session_start(); ?>
<?php
    include "header.php";
    // include "connection.php"; 
    include "../superbase/config.php"; // Added Supabase config
?>

<div class="row" style="margin: 0px; padding:0px; margin-bottom: 50px;">
    <div class="col-lg-6 col-lg-push-3" style="min-height: 500px; background-color: white;">
        <center>
            <h1>Old Exam Results</h1>
        </center>

        <!-- Search Form -->
        <div class="row" style="margin-bottom: 20px;">
            <div class="col-lg-8 col-lg-push-2">
                <form method="GET" action="">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search by exam type..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <div class="input-group-btn">
                            <button class="btn btn-primary" type="submit">Search</button>
                            <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                                <a href="old_exam_results.php" class="btn btn-default">Show All</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php
            // Pagination settings
            $results_per_page = 7;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $page = max(1, $page); // Ensure page is at least 1
            $offset = ($page - 1) * $results_per_page;

            // Search filter
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            
            // Supabase query - show all by default, filter if search is provided
            $baseQuery = 'exam_results?username=eq.' . $_SESSION['userssname'] . '&order=id.desc';
            
            // Add search filter only if provided
            if (!empty($search)) {
                $baseQuery .= '&exam_type=ilike.*' . urlencode($search) . '*';
            }
            
            // Get total count for pagination
            $countResponse = fetchData($baseQuery);
            $total_count = is_array($countResponse) ? count($countResponse) : 0;
            
            // Add pagination to query
            $query = $baseQuery . '&limit=' . $results_per_page . '&offset=' . $offset;
            $response = fetchData($query);
            $count = is_array($response) ? count($response) : 0;
            
            if($count == 0){
                ?>
                <center>
                    <?php if (!empty($search)): ?>
                        <h3>No exam results found for "<?php echo htmlspecialchars($search); ?>"</h3>
                        <p>Please try a different search term or <a href="old_exam_results.php">view all results</a></p>
                    <?php else: ?>
                        <h1>No Results Found</h1>
                        <p>You haven't taken any exams yet.</p>
                    <?php endif; ?>
                </center>
                <?php
            } else {
                // Show search results info
                if (!empty($search)) {
                    echo '<div class="alert alert-info text-center">';
                    echo 'Showing results for: "<strong>' . htmlspecialchars($search) . '</strong>"';
                    echo ' <a href="old_exam_results.php" class="btn btn-xs btn-default">Show All Results</a>';
                    echo '</div>';
                }
                
                echo "<table class='table table-bordered table-striped'>";
                echo "<tr style='background-color: #006df0; color: white;'>";
                echo "<th>Username</th>";
                echo "<th>Exam Name</th>";
                echo "<th>Total Questions</th>";
                echo "<th>Correct Answers</th>";
                echo "<th>Wrong Answers</th>";
                echo "<th>Date And Time</th>";
                echo "<th>Time Finished</th>";
                echo "</tr>";
                
                // Display results
                foreach($response as $row){
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["exam_type"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["total_question"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["correct_answer"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["wrong_answer"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["exam_time"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["time_finished"]) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";

                // Pagination
                $total_pages = ceil($total_count / $results_per_page);
                
                if ($total_pages > 1) {
                    echo '<div class="text-center">';
                    echo '<ul class="pagination">';
                    
                    // Previous button
                    if ($page > 1) {
                        $prev_url = "?page=" . ($page - 1);
                        if (!empty($search)) $prev_url .= "&search=" . urlencode($search);
                        echo '<li><a href="' . $prev_url . '">&laquo; Previous</a></li>';
                    }
                    
                    // Page numbers
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    for ($i = $start_page; $i <= $end_page; $i++) {
                        $page_url = "?page=" . $i;
                        if (!empty($search)) $page_url .= "&search=" . urlencode($search);
                        
                        if ($i == $page) {
                            echo '<li class="active"><a href="#">' . $i . '</a></li>';
                        } else {
                            echo '<li><a href="' . $page_url . '">' . $i . '</a></li>';
                        }
                    }
                    
                    // Next button
                    if ($page < $total_pages) {
                        $next_url = "?page=" . ($page + 1);
                        if (!empty($search)) $next_url .= "&search=" . urlencode($search);
                        echo '<li><a href="' . $next_url . '">Next &raquo;</a></li>';
                    }
                    
                    echo '</ul>';
                    echo '</div>';
                    
                    // Results count
                    echo '<div class="text-center" style="margin-top: 10px;">';
                    $start_result = ($page - 1) * $results_per_page + 1;
                    $end_result = min($page * $results_per_page, $total_count);
                    
                    if (!empty($search)) {
                        echo '<p>Showing ' . $start_result . ' to ' . $end_result . ' of ' . $total_count . ' results for "' . htmlspecialchars($search) . '"</p>';
                    } else {
                        echo '<p>Showing ' . $start_result . ' to ' . $end_result . ' of ' . $total_count . ' total results</p>';
                    }
                    echo '</div>';
                } else {
                    // Show total count when no pagination needed
                    echo '<div class="text-center">';
                    if (!empty($search)) {
                        echo '<p>Found ' . $total_count . ' results for "' . htmlspecialchars($search) . '"</p>';
                    } else {
                        echo '<p>Total of ' . $total_count . ' exam results</p>';
                    }
                    echo '</div>';
                }
            }
        ?>
    </div>
</div>

<?php include "footer.php"?>