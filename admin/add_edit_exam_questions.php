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
?>

<div class="breadcrumbs">
    <div class="col-sm-12">
        <div class="page-header float-left">
            <div class="page-title">
                <h1>Select Exam Categories for add and edit questions</h1>
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
                                            
                        <table id="examCategoriesTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Exam Name</th>
                                    <th scope="col">Exam Time</th>
                                    <th scope="col">Select</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php                            
                                $useLocal = isset($_COOKIE['useLocalDB']) && $_COOKIE['useLocalDB'] === 'true';
                                
                                if ($useLocal) {
                                    // LOCAL MYSQL CONNECTION
                                    $count = 0;
                                    $res = mysqli_query($link, "SELECT * FROM exam_category ORDER BY id DESC");
                                    
                                    if (!$res) {
                                        echo "<tr><td colspan='4' class='text-danger'>Error loading exam categories: " . mysqli_error($link) . "</td></tr>";
                                    } elseif (mysqli_num_rows($res) == 0) {
                                        echo "<tr><td colspan='4' class='text-muted'>No exam categories found.</td></tr>";
                                    } else {
                                        while ($row = mysqli_fetch_array($res)) {
                                            $count = $count + 1;
                                ?>
                                            <tr>
                                                <th scope="row"><?php echo $count; ?></th>
                                                <td><?php echo htmlspecialchars($row["category"]); ?></td>
                                                <td><?php echo htmlspecialchars($row["exam_time_in_minutes"]); ?></td>
                                                <td>
                                                    <a href="add_edit_questions.php?id=<?php echo $row["id"]; ?>" class="btn btn-primary btn-sm">Select</a>
                                                </td>
                                            </tr>
                                <?php
                                        }
                                    }
                                } else {
                                    // SUPABASE CONNECTION
                                    $response = fetchData("exam_category?order=id.desc");

                                    if (isset($response['error'])) {
                                        echo "<tr><td colspan='4' class='text-danger'>Error loading exam categories: " . htmlspecialchars($response['error']) . "</td></tr>";
                                    } elseif (empty($response)) {
                                        echo "<tr><td colspan='4' class='text-muted'>No exam categories found.</td></tr>";
                                    } else {
                                        $count = 0;
                                        foreach ($response as $row) {
                                            $count = $count + 1;
                                ?>
                                            <tr>
                                                <th scope="row"><?php echo $count; ?></th>
                                                <td><?php echo htmlspecialchars($row["category"] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($row["exam_time_in_minutes"] ?? ''); ?></td>
                                                <td>
                                                    <a href="add_edit_questions.php?id=<?php echo $row["id"]; ?>" class="btn btn-primary btn-sm">Select</a>
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
document.addEventListener('DOMContentLoaded', function() {
    setupGlobalTableComponents({
        tableId: 'examCategoriesTable',
        searchPlaceholder: 'Search exam categories...',
        recordsPerPage: 5,
        searchColumns: [1, 2] // Search in Exam Name (col 1) and Exam Time (col 2)
    });
});
</script>

<?php include "footer.php"; ?>