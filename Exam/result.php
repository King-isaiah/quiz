<?php session_start(); ?>
<?php 
// include "connection.php"; 
include "header.php";
include "../superbase/config.php"; // Added Supabase config

if (!isset($_SESSION['userssname'])) {
    exit; 
    ?>
    <script type='text/javascript'>window.location.href='login.php';</script>";
    <?php
}

// Initialize score counters
$correct = 0;
$wrong = 0;

$currentTimer = isset($_SESSION['current_timer']) ? $_SESSION['current_timer'] : '00:00:00.000'; 
$fullTimeMinutes = isset($_SESSION['timeexam']) ? (int)$_SESSION['timeexam'] : 0; // Total full time in minutes

// Convert full time to total seconds
$fullTimeSeconds = $fullTimeMinutes * 60;

// Convert current timer to total seconds
list($hours, $minutes, $seconds) = explode(':', $currentTimer);
$totalCurrentSeconds = ($hours * 3600) + ($minutes * 60) + $seconds; // Total current seconds

$totalSecondsSpent = $fullTimeSeconds - $totalCurrentSeconds;

// Ensure the result is not negative
if ($totalSecondsSpent < 0) {
    $totalSecondsSpent = 0; // This ensures you don't go negative
}

$minutesSpent = floor($totalSecondsSpent / 60);
// $remainingSeconds = floor($totalSecondsSpent % 60);
$remainingSeconds = floor(intval($totalSecondsSpent) % 60);
$millisecondsSpent = round(($totalSecondsSpent - floor($totalSecondsSpent)) * 1000);

$timeSpentFormatted = sprintf("%d mins %d seconds %d milliseconds", $minutesSpent, $remainingSeconds, $millisecondsSpent);

try {
    // Calculate results only if answers are set
    if (isset($_SESSION["answer"]) && is_array($_SESSION["answer"])) {
        // Get the position-to-ID mapping from session
        if (!isset($_SESSION['position_to_id'])) {
            // If mapping doesn't exist, create it from questions_order
            if (isset($_SESSION['questions_order']) && is_array($_SESSION['questions_order'])) {
                $_SESSION['position_to_id'] = [];
                foreach ($_SESSION['questions_order'] as $position => $question) {
                    $_SESSION['position_to_id'][$position + 1] = $question['question_no'];
                }
            }
        }
        
        // Check each saved answer using the mapping
        foreach ($_SESSION["answer"] as $position => $userAnswer) {
            // Get the actual database question ID for this position
            if (isset($_SESSION['position_to_id'][$position])) {
                $dbQuestionId = $_SESSION['position_to_id'][$position];
                
                // Fetch correct answer from database using the actual ID
                $response = fetchData('questions?category=eq.' . urlencode($_SESSION['exam_category']) . 
                                      '&question_no=eq.' . $dbQuestionId);
                
                if (is_array($response) && count($response) > 0) {
                    $correctAnswer = $response[0]['answer'];
                    
                    if ($correctAnswer == $userAnswer) {
                        $correct++;
                    } else {
                        $wrong++;
                    }
                } else {
                    $wrong++; // Question not found in database
                }
            } else {
                $wrong++; // Position mapping not found
            }
        }
        
        // Also count unanswered questions
        if (isset($_SESSION['position_to_id'])) {
            $totalQuestions = count($_SESSION['position_to_id']);
            $answeredQuestions = count($_SESSION["answer"]);
            $unanswered = $totalQuestions - $answeredQuestions;
            
            // Only add unanswered if there are more questions than answered
            if ($unanswered > 0) {
                $wrong += $unanswered;
            }
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage()); // Log the error for debugging
    ?>
        <div class='alert alert-danger'>Something went wrong while calculating your results. Please try again later.</div>
    <?php
    exit; // Stop execution
}

// Count total questions
// Use the mapping if it exists, otherwise fetch from database
if (isset($_SESSION['position_to_id'])) {
    $count = count($_SESSION['position_to_id']);
} else {
    // Fallback: Supabase equivalent
    $response = fetchData('questions?category=eq.' . urlencode($_SESSION['exam_category']));
    $count = is_array($response) ? count($response) : 0;
}

// Display results
$wrong = $count - $correct; // Calculate wrong answers
echo "<br><br>";
echo "<div class='row' style='margin: 0px; padding:0px; margin-bottom: 50px;'>";
echo "<div class='col-lg-6 col-lg-push-3' style='min-height: 500px; background-color: white;'>";
echo "<center>";
echo "Total Questions = " . htmlspecialchars($count) . "<br>";
echo "Correct Answers = " . htmlspecialchars($correct) . "<br>";
echo "Wrong Answers = " . htmlspecialchars($wrong) . "<br>";
echo "Exam Time In Minutes= " .  htmlspecialchars($fullTimeMinutes) . "<br>";
echo "Minutes Left = " .  htmlspecialchars($currentTimer) . "<br>";
echo "Minutes Spent = " .  htmlspecialchars($timeSpentFormatted) . "<br>";
echo "</center>";
echo "</div></div>";

// Insert results into the database
if (isset($_SESSION["exam_start"])) {
    $date = date("Y-m-d H:i:s");
    $users = $_SESSION['userssname'];
    $unique = $_SESSION['unique_id'];
    
    // Comment out local MySQL connection and replace with Supabase
    /*
    if ($stmt = mysqli_prepare($link, "INSERT INTO exam_results (username,unique_id, exam_type, total_question, correct_answer, wrong_answer, exam_time, time_finished,mins_spent)
     VALUES (?,?, ?, ?, ?, ?, ?, ?,?)")) {
        mysqli_stmt_bind_param($stmt, "sisiiisss", $users,$unique, $_SESSION['exam_category'], $count, $correct, $wrong, $date, $currentTimer,$timeSpentFormatted);
        
        if (!mysqli_stmt_execute($stmt)) {
            // Handle the error
            ?>
                <div class='alert alert-danger'>Error saving results: <?php " . mysqli_error($link) . "?> </div>
            
            <?php
        }
        mysqli_stmt_close($stmt);
    */
    
    // Supabase equivalent
    $insertData = [
        'username' => $users,
        'unique_id' => $unique,
        'exam_type' => $_SESSION['exam_category'],
        'total_question' => $count,
        'correct_answer' => $correct,
        'wrong_answer' => $wrong,
        'exam_time' => $date,
        'time_finished' => $currentTimer,
        'mins_spent' => $timeSpentFormatted
    ];
    
    $result = createData('exam_results', $insertData);
    
    if (isset($result['error'])) {
        ?>
        <div class='alert alert-danger'>Error saving results: <?php echo htmlspecialchars($result['error']); ?></div>
        <?php
    }
}


// CLEAR SESSION CACHE AFTER EXAM COMPLETION
unset($_SESSION['questions_order']);
unset($_SESSION['position_to_id']);  // ADD THIS LINE
unset($_SESSION['answer']);

// Clear session variables
if (isset($_SESSION["exam_start"])) {
    unset($_SESSION["exam_start"]);
}
// Clear session variables
if (isset($_SESSION["exam_start"])) {
    unset($_SESSION["exam_start"]);
}

// Reset the timer in the session
if (isset($_SESSION["end_time"])) {
    unset($_SESSION["end_time"]); // Clear the end time to prevent further counting
}

include "footer.php";
?>