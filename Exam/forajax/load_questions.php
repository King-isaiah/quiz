<?php
session_start();

include "../../superbase/config.php"; 

if (!isset($_SESSION['questions_order']) || empty($_SESSION['questions_order'])) {
    // Supabase
    $response = fetchData('questions?category=eq.' . urlencode($_SESSION['exam_category']));
    $questions = is_array($response) ? $response : [];
    
    // Shuffle questions to randomize order
    shuffle($questions);
    
    // Store the shuffled questions in session
    $_SESSION['questions_order'] = $questions;
    
    // Create a position-to-ID mapping (NEW CRITICAL PART)
    $_SESSION['position_to_id'] = [];
    foreach ($questions as $position => $question) {
        $_SESSION['position_to_id'][$position + 1] = $question['question_no'];
    }
}

// Get the question number from the request
$queno = intval($_GET['questionno']);
$question_order = $_SESSION['questions_order'];

// Calculate actual question index in shuffled order
$question_index = $queno - 1; 

// Validate and get the current question
if (isset($question_order[$question_index])) {
    $current_question = $question_order[$question_index];
    
    $question_no = $current_question["question_no"];
    $question = $current_question["question"];
    $opt1 = $current_question["opt1"];
    $opt2 = $current_question["opt2"];
    $opt3 = $current_question["opt3"];
    $opt4 = $current_question["opt4"];

    // Fetch the answer if available - USE $queno (position), not $question_no
    $ans = isset($_SESSION["answer"][$queno]) ? $_SESSION["answer"][$queno] : "";
    
    // Output the question and options
    echo "<br>
    <table>
        <tr>
            <td style='font-weight: bold; font-size:18px; padding-left:5px' colspan='2'>
                " . htmlspecialchars($question) . "
            </td>
        </tr>
    </table>";

    echo "<table style='margin-left:10px'>";
    $options = [$opt1, $opt2, $opt3, $opt4];
    
    foreach ($options as $index => $option) {
        // Escape the option for JavaScript
        $js_option = htmlspecialchars($option, ENT_QUOTES);
        
        echo "<tr>
                <td>
                    <input type='radio' name='r1' id='r{$index}' value='{$js_option}' 
                        onclick=\"radioclick('{$js_option}', {$queno})\" 
                        ". ($ans == $option ? 'checked' : '') . ">
                </td>
                <td style='padding-left: 10px;'>" . 
                    (strpos($option, 'images/') !== false ? 
                        "<img src='../admin/{$option}' height='30' width='30'>" : 
                        htmlspecialchars($option)
                    ) . 
                "</td>
              </tr>";
    }
    echo "</table>";
    
    // Debug output (optional - remove in production)
    echo "<!-- Debug: Position=$queno, DB ID=$question_no, User Answer=" . htmlspecialchars($ans) . " -->";
} else {
    echo "over"; 
}
?>