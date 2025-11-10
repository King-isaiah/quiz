<?php 
    include "header.php";
?>
<?php
    $unique = $_SESSION['unique_id'];

    // Use the safe function that won't break existing code
    $result = safeFetchExamResultsWithCategory($unique);

    // Initialize examData array
    $examData = [];

    if (isset($result['error'])) {
        echo '<script>console.error("Database Error: ' . $result['error'] . '");</script>';
    } elseif (empty($result)) {
        echo '<script>console.warn("No exam results found for user ID: ' . $unique . '");</script>';
    } else {
        // DEBUG: Check structure
        echo '<script>console.log("Result structure: ", ' . json_encode($result) . ');</script>';
        
        foreach ($result as $row) {
            // Extract main data
            $examName = $row['exam_type'] ?? 'N/A';
            $totalQuestion = $row['total_question'] ?? 0;
            $correctQuestion = $row['correct_answer'] ?? 0;
            $wrongQuestion = $row['wrong_answer'] ?? 0;
            $examTime = $row['exam_time'] ?? '';
            $minSpent = $row['mins_spent'] ?? 0;
            
            // Extract category data - handle different possible structures
            $year = '';
            $price = 0;
            
            if (isset($row['exam_category']) && is_array($row['exam_category'])) {
                $year = $row['exam_category']['year'] ?? '';
                $price = $row['exam_category']['price'] ?? 0;
            }
            
            $examData[] = [
                'examName' => $examName,
                'totalQuestion' => $totalQuestion,
                'correctQuestion' => $correctQuestion,
                'wrongQuestion' => $wrongQuestion,
                'examTime' => $examTime,
                'minSpent' => $minSpent,
                'year' => $year,
                'price' => $price
            ];
            
            // Debug each processed row
            echo '<script>console.log("Processed exam: ' . $examName . '");</script>';
        }
        
        echo '<script>console.log("Successfully processed ' . count($examData) . ' exam results");</script>';
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Exam Participated</title>
    <link rel="stylesheet" href="css/dataTables.bootstrap5.css" /> 
    <link type="image/x-icon" rel="icon" href="images/bilicon.ico" />
    <link href="js/datatables/datatables.min.css" rel="stylesheet">
    <!-- Your existing styles remain unchanged -->
</head>
<body>
    <main class="main-content">            
        <form action="">
            <div class="exam-from">
                <table id="exam_inventory" class="display table stripe" style="width:100%">
                    <thead>
                        <tr>                         
                            <th>Exam Name</th> 
                            <th>Year Of Release</th>                                                               
                            <th>Date/Hour Participated</th>
                            <th>Total Questions</th>                                    
                            <th>Correct Answers</th>                                    
                            <th>Wrong Answers</th>               
                            <th>Amount Paid</th> 
                            <th>Time Spent</th>
                        </tr>
                    </thead>
                    <tbody> 
                        <?php if (!empty($examData)): ?>
                            <?php foreach ($examData as $exam): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($exam['examName']); ?></td>
                                    <td><?php echo htmlspecialchars($exam['year']); ?></td>
                                    <td><?php echo htmlspecialchars($exam['examTime']); ?></td>
                                    <td><?php echo htmlspecialchars($exam['totalQuestion']); ?></td>
                                    <td><?php echo htmlspecialchars($exam['correctQuestion']); ?></td>
                                    <td><?php echo htmlspecialchars($exam['wrongQuestion']); ?></td>
                                    <td>N<?php echo htmlspecialchars($exam['price']); ?></td>
                                    <td><?php echo htmlspecialchars($exam['minSpent']); ?> minutes</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">No exam results found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>                       
                </table>            
            </div>
        </form>
    </main>

    <!-- Your existing scripts remain unchanged -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#exam_inventory').DataTable({
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "language": {
                    "search": "Search exams:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                },
                "order": [[2, "desc"]],
                "responsive": true
            });
        });
    </script>
</body>
</html>

<?php include "footer.php"?>