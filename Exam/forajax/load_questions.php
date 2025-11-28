<?php 
session_start();
include "../header.php";
include "../../superbase/config.php";

if (!isset($_SESSION['userssname'])) {
    echo "<script type='text/javascript'>window.location.href='login.php';</script>";
    exit;
}

// Initialize score counters
$correct = 0;
$wrong = 0;

$currentTimer = isset($_SESSION['current_timer']) ? $_SESSION['current_timer'] : '00:00:00.000'; 
$fullTimeMinutes = isset($_SESSION['timeexam']) ? (int)$_SESSION['timeexam'] : 0;

// FIX: Properly parse the current timer
$timeParts = explode(':', $currentTimer);
$millisecondsPart = 0;

if (count($timeParts) >= 3) {
    // Handle milliseconds if present
    $secondsParts = explode('.', $timeParts[2]);
    $hours = intval($timeParts[0]);
    $minutes = intval($timeParts[1]);
    $seconds = intval($secondsParts[0]);
    $millisecondsPart = isset($secondsParts[1]) ? intval($secondsParts[1]) : 0;
    
    $totalCurrentSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;
} else {
    $totalCurrentSeconds = 0;
}

// Convert full time to total seconds (FIXED: ensure integer multiplication)
$fullTimeSeconds = $fullTimeMinutes * 60;

// Calculate time spent (ensure no negative values)
$totalSecondsSpent = max(0, $fullTimeSeconds - $totalCurrentSeconds);

$minutesSpent = floor($totalSecondsSpent / 60);
$remainingSeconds = floor($totalSecondsSpent % 60);
$millisecondsSpent = $millisecondsPart; // Use the milliseconds from timer

$timeSpentFormatted = sprintf("%d mins %d seconds %d milliseconds", $minutesSpent, $remainingSeconds, $millisecondsSpent);

// Rest of your result calculation code remains the same...
try {
    if (isset($_SESSION["answer"]) && is_array($_SESSION["answer"])) {
        for ($i = 1; $i <= count($_SESSION["answer"]); $i++) {
            $answer = "";
            
            $response = fetchData('questions?category=eq.' . urlencode($_SESSION['exam_category']) . '&question_no=eq.' . $i);
            if (is_array($response) && count($response) > 0) {
                $answer = $response[0]['answer'];
            }

            if (isset($_SESSION["answer"][$i])) {
                if ($answer == $_SESSION["answer"][$i]) {
                    $correct++;
                } else {
                    $wrong++;
                }
            } else {
                $wrong++;
            }
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "<div class='alert alert-danger'>Something went wrong while calculating your results. Please try again later.</div>";
    exit;
}

// Count total questions
$response = fetchData('questions?category=eq.' . urlencode($_SESSION['exam_category']));
$count = is_array($response) ? count($response) : 0;

// Display results
echo "<br><br>";
echo "<div class='row' style='margin: 0px; padding:0px; margin-bottom: 50px;'>";
echo "<div class='col-lg-6 col-lg-push-3' style='min-height: 500px; background-color: white;'>";
echo "<center>";
echo "Total Questions = " . htmlspecialchars($count) . "<br>";
echo "Correct Answers = " . htmlspecialchars($correct) . "<br>";
echo "Wrong Answers = " . htmlspecialchars($wrong) . "<br>";
echo "Exam Time In Minutes = " . htmlspecialchars($fullTimeMinutes) . "<br>";
echo "Time Left = " . htmlspecialchars($currentTimer) . "<br>";
echo "Time Spent = " . htmlspecialchars($timeSpentFormatted) . "<br>";
echo "</center>";
echo "</div></div>";

// Insert results into the database
if (isset($_SESSION["exam_start"])) {
    $date = date("Y-m-d H:i:s");
    $users = $_SESSION['userssname'];
    $unique = $_SESSION['unique_id'];
    
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
        echo "<div class='alert alert-danger'>Error saving results: " . htmlspecialchars($result['error']) . "</div>";
    }
}

// Clear session data
unset($_SESSION['questions_order']);
unset($_SESSION['answer']);
if (isset($_SESSION["exam_start"])) {
    unset($_SESSION["exam_start"]);
}
if (isset($_SESSION["end_time"])) {
    unset($_SESSION["end_time"]);
}

// include "footer.php";
?>