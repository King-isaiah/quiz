<?php
session_start();
include "../connection.php";
include "../../superbase/config.php"; // Added Supabase config

// Initialize or reset the questions_order in the session
if (!isset($_SESSION['questions_order']) || empty($_SESSION['questions_order'])) {
    // Comment out local MySQL connection and replace with Supabase
    /*
    // Fetch questions from the database
    $res = mysqli_query($link, "SELECT * FROM questions WHERE category='$_SESSION[exam_category]'");
    $questions = []; // Array to hold fetched questions

    while ($row = mysqli_fetch_assoc($res)) {
        $questions[] = $row; // Store questions in an array
    }
    */
    
    // Supabase equivalent
    $response = fetchData('questions?category=eq.' . urlencode($_SESSION['exam_category']));
    $questions = is_array($response) ? $response : [];
    
    // Shuffle questions to randomize order
    shuffle($questions);
    
    // Store the shuffled questions in session
    $_SESSION['questions_order'] = $questions;
}

// Get the question number from the request
$queno = intval($_GET['questionno']);
$question_order = $_SESSION['questions_order'];

// Calculate actual question index in shuffled order
$question_index = $queno - 1; // Convert to zero-based index

// Validate and get the current question
if (isset($question_order[$question_index])) {
    $current_question = $question_order[$question_index];
    
    $question_no = $current_question["question_no"];
    $question = $current_question["question"];
    $opt1 = $current_question["opt1"];
    $opt2 = $current_question["opt2"];
    $opt3 = $current_question["opt3"];
    $opt4 = $current_question["opt4"];

    // Fetch the answer if available
    $ans = isset($_SESSION["answer"][$queno]) ? $_SESSION["answer"][$queno] : "";

    // Output the question and options
    echo "<br>
    <table>
        <tr>
            <td style='font-weight: bold; font-size:18px; padding-left:5px' colspan='2'>
                " . $question . "
            </td>
        </tr>
    </table>";

    echo "<table style='margin-left:10px'>";
    $options = [$opt1, $opt2, $opt3, $opt4];
    foreach ($options as $index => $option) {
        echo "<tr>
                <td>
                    <input type='radio' name='r1' id='r{$index}' value='{$option}' 
                        onclick='radioclick(this.value, {$question_no})' ". ($ans == $option ? 'checked' : '') .">
                </td>
                <td style='padding-left: 10px;'>" . (strpos($option, 'images/') !== false ? "<img src='../admin/{$option}' height='30' width='30'>" : $option) . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "over"; // No question found
}
?>