<?php session_start()?>
<?php include "header.php"; ?>

<?php 
    include "../connection.php";
    include "../superbase/config.php";
    
    if(!isset($_SESSION["admin"])){
        ?>
        <script type="text/javascript"> 
            window.location="index.php";
        </script>
        <?php
    }

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
                <h1 class="mb-0">Exam Questions Management</h1>
                <div class="form-group mb-0">
                    <label for="categoryFilter" class="form-label mr-2 mb-0">Filter by Exam Category:</label>
                    <select class="form-control d-inline-block" style="width: auto;" id="categoryFilter" onchange="filterQuestions()">
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
                                <a href="exam-questions.php" class="btn btn-sm btn-outline-secondary ml-2">Clear Filter</a>
                            </div>
                        <?php endif; ?>

                        <table class="table table-bordered" id="examQuestionsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Question</th>
                                    <th>Option 1</th>
                                    <th>Option 2</th>
                                    <th>Option 3</th>
                                    <th>Option 4</th>
                                    <th>Answer</th>
                                    <th>Category</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Build query based on selected category
                                if ($selected_category_id) {
                                    // Get the category name for filtering
                                    if ($useLocal) {
                                        $cat_res = mysqli_query($link, "SELECT category FROM exam_category WHERE id=$selected_category_id");
                                        if ($cat_row = mysqli_fetch_array($cat_res)) {
                                            $filter_category = $cat_row['category'];
                                            $where_condition = "q.category='$filter_category'";
                                        } else {
                                            $where_condition = "1=0"; // No results if category not found
                                        }
                                    } else {
                                        $cat_response = fetchData("exam_category?id=eq.$selected_category_id");
                                        if (is_array($cat_response) && !empty($cat_response)) {
                                            $filter_category = $cat_response[0]['category'] ?? '';
                                            $where_condition = "q.category='$filter_category'";
                                        } else {
                                            $where_condition = "1=0";
                                        }
                                    }
                                } else {
                                    $where_condition = "1=1";
                                }
                                
                                if ($useLocal) {
                                    // LOCAL MYSQL CONNECTION
                                    $count = 0;
                                    $query = "SELECT q.*, ec.category as category_name 
                                              FROM questions q 
                                              LEFT JOIN exam_category ec ON q.category = ec.category 
                                              WHERE $where_condition 
                                              ORDER BY ec.category, q.id";
                                    $res = mysqli_query($link, $query);
                                    
                                    if (!$res) {
                                        echo "<tr><td colspan='9' class='text-danger'>Error loading questions: " . mysqli_error($link) . "</td></tr>";
                                    } elseif (mysqli_num_rows($res) == 0) {
                                        echo "<tr><td colspan='9' class='text-muted'>No questions found.</td></tr>";
                                    } else {
                                        while ($row = mysqli_fetch_array($res)) {
                                            $count++;
                                ?>
                                            <tr>
                                                <td><?php echo $count; ?></td>
                                                <td><?php echo htmlspecialchars($row['question']); ?></td>
                                                <td>
                                                    <?php if (strpos($row['opt1'], 'opt_images/') !== false): ?>
                                                        <img src="<?php echo $row['opt1']; ?>" height="30" width="30" class="img-thumbnail">
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($row['opt1']); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (strpos($row['opt2'], 'opt_images/') !== false): ?>
                                                        <img src="<?php echo $row['opt2']; ?>" height="30" width="30" class="img-thumbnail">
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($row['opt2']); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (strpos($row['opt3'], 'opt_images/') !== false): ?>
                                                        <img src="<?php echo $row['opt3']; ?>" height="30" width="30" class="img-thumbnail">
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($row['opt3']); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (strpos($row['opt4'], 'opt_images/') !== false): ?>
                                                        <img src="<?php echo $row['opt4']; ?>" height="30" width="30" class="img-thumbnail">
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($row['opt4']); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (strpos($row['answer'], 'opt_images/') !== false): ?>
                                                        <img src="<?php echo $row['answer']; ?>" height="30" width="30" class="img-thumbnail">
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($row['answer']); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                                <td>
                                                    <?php if (strpos($row['opt4'], 'opt_images/') !== false): ?>
                                                        <a href="edit_option_images.php?id=<?php echo $row['id']; ?>&id1=<?php echo $selected_category_id ?: ''; ?>&return_to=exam-questions" class="btn btn-sm btn-warning">Edit</a>
                                                    <?php else: ?>
                                                        <a href="edit_option.php?id=<?php echo $row['id']; ?>&id1=<?php echo $selected_category_id ?: ''; ?>&return_to=exam-questions" class="btn btn-sm btn-warning">Edit</a>
                                                    <?php endif; ?>
                                                    <a href="delete_option.php?id=<?php echo $row['id']; ?>&id1=<?php echo $selected_category_id ?: ''; ?>&return_to=exam-questions" class="btn btn-sm btn-danger">Delete</a>
                                                </td>
                                            </tr>
                                <?php
                                        }
                                    }
                                } else {
                                    // SUPABASE CONNECTION
                                    if ($selected_category_id) {
                                        // For Supabase, we need to use the category name for filtering
                                        $category_name_response = fetchData("exam_category?id=eq.$selected_category_id");
                                        if (is_array($category_name_response) && !empty($category_name_response)) {
                                            $category_name = $category_name_response[0]['category'] ?? '';
                                            $response = fetchData("questions?category=eq.$category_name&order=id.asc");
                                        } else {
                                            $response = ['error' => 'Category not found'];
                                        }
                                    } else {
                                        $response = fetchData("questions?order=category.asc,id.asc");
                                    }

                                    if (isset($response['error'])) {
                                        echo "<tr><td colspan='9' class='text-danger'>Error loading questions: " . htmlspecialchars($response['error']) . "</td></tr>";
                                    } elseif (empty($response)) {
                                        echo "<tr><td colspan='9' class='text-muted'>No questions found.</td></tr>";
                                    } else {
                                        $count = 0;
                                        foreach ($response as $row) {
                                            $count++;
                                            // For Supabase, get category name from the category field itself
                                            $category_name = $row['category'] ?? 'Unknown';
                                ?>
                                            <tr>
                                                <td><?php echo $count; ?></td>
                                                <td><?php echo htmlspecialchars($row['question'] ?? ''); ?></td>
                                                <td>
                                                    <?php if (isset($row['opt1']) && strpos($row['opt1'], 'opt_images/') !== false): ?>
                                                        <img src="<?php echo $row['opt1']; ?>" height="30" width="30" class="img-thumbnail">
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($row['opt1'] ?? ''); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (isset($row['opt2']) && strpos($row['opt2'], 'opt_images/') !== false): ?>
                                                        <img src="<?php echo $row['opt2']; ?>" height="30" width="30" class="img-thumbnail">
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($row['opt2'] ?? ''); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (isset($row['opt3']) && strpos($row['opt3'], 'opt_images/') !== false): ?>
                                                        <img src="<?php echo $row['opt3']; ?>" height="30" width="30" class="img-thumbnail">
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($row['opt3'] ?? ''); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (isset($row['opt4']) && strpos($row['opt4'], 'opt_images/') !== false): ?>
                                                        <img src="<?php echo $row['opt4']; ?>" height="30" width="30" class="img-thumbnail">
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($row['opt4'] ?? ''); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (isset($row['answer']) && strpos($row['answer'], 'opt_images/') !== false): ?>
                                                        <img src="<?php echo $row['answer']; ?>" height="30" width="30" class="img-thumbnail">
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($row['answer'] ?? ''); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($category_name); ?></td>
                                                <td>
                                                    <?php if (isset($row['opt4']) && strpos($row['opt4'], 'opt_images/') !== false): ?>
                                                        <a href="edit_option_images.php?id=<?php echo $row['id']; ?>&id1=<?php echo $selected_category_id ?: ''; ?>&return_to=exam-questions" class="btn btn-sm btn-warning">Edit</a>
                                                    <?php else: ?>
                                                        <a href="edit_option.php?id=<?php echo $row['id']; ?>&id1=<?php echo $selected_category_id ?: ''; ?>&return_to=exam-questions" class="btn btn-sm btn-warning">Edit</a>
                                                    <?php endif; ?>
                                                    <a href="delete_option.php?id=<?php echo $row['id']; ?>&id1=<?php echo $selected_category_id ?: ''; ?>&return_to=exam-questions" class="btn btn-sm btn-danger">Delete</a>
                                                </td>
                                            </tr>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filterQuestions() {
    const categoryId = document.getElementById('categoryFilter').value;
    if (categoryId) {
        window.location.href = 'exam-questions.php?category_id=' + categoryId;
    } else {
        window.location.href = 'exam-questions.php';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    setupGlobalTableComponents({
        tableId: 'examQuestionsTable',
        searchPlaceholder: 'Search questions...',
        recordsPerPage: 3,
        searchColumns: [1, 2, 3, 4, 5, 7] // Search in question, options, and category
    });
});
</script>

<?php include "footer.php"; ?>