<?php 
    session_start();
    include "header.php";
    // include "../connection.php"; // Local connection - KEEPING FOR REFERENCE 
?>
<?php
    // Check database preference from cookie
    $useLocal = isset($_COOKIE['useLocalDB']) && $_COOKIE['useLocalDB'] === 'true';
    
    // Initialize the target date variable with default value
    $targetDate = null; 
    $hasActiveExam = false;

    if ($useLocal) {
        // LOCAL MYSQL CONNECTION - COMMENTED BUT KEPT FOR REFERENCE
        /*
        $sql = "SELECT start_date FROM exam_category WHERE countDown = 'active'";
        $result = $link->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $targetDate = $row['start_date']; 
            $hasActiveExam = true;
        }
        */
        
        // For now, we'll use Supabase by default since local is commented
        $response = universalFetch('exam_category', ['countDown' => 'active'], ['start_date']);
        
        if (is_array($response) && count($response) > 0 && !isset($response['error'])) {
            $targetDate = $response[0]['start_date'] ?? null;
            $hasActiveExam = true;
        }
        
    } else {
        // SUPABASE CONNECTION
        $response = universalFetch('exam_category', ['countDown' => 'active'], ['start_date']);
        
        if (is_array($response) && count($response) > 0 && !isset($response['error'])) {
            $targetDate = $response[0]['start_date'] ?? null;
            $hasActiveExam = true;
        }
    }

    // Set default target date if no active exam found
    if (!$hasActiveExam || empty($targetDate)) {
        $targetDate = '2025-04-05 12:59:59'; // Default fallback date
        $hasActiveExam = false;
    }

    $targetTimestamp = strtotime($targetDate) * 1000; 
?>
<div class="dashboard-front-view">
    
    <div class="image-countdown">
        <h1 style="color: green;">COUNT DOWN TO BEGIN THE EXAM</h1>
        <div id="countdown">
            <?php if (!$hasActiveExam): ?>
                <p style="color: orange; font-size: 18px;">No exam set for timer</p>
            <?php endif; ?>
        </div>
        
    </div>
</div>
<?php include "footer.php"?>

<script>
    // Get the target timestamp from PHP
    let targetTimestamp = <?php echo $targetTimestamp; ?>;
    let hasActiveExam = <?php echo $hasActiveExam ? 'true' : 'false'; ?>;

    function updateCountdown() {
        // If no active exam, don't run the countdown
        if (!hasActiveExam) {
            document.getElementById("countdown").innerHTML = "<p style='color: orange; font-size: 18px;'>No exam set for timer</p>";
            return;
        }

        // Get the current timestamp in milliseconds
        const now = new Date().getTime();

        // Calculate the difference
        const distance = targetTimestamp - now;

        // Time calculations
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        const milliseconds = Math.floor(distance % 1000);

        // Display the result
        document.getElementById("countdown").innerHTML = 
            `${days}d ${hours}h ${minutes}m ${seconds}s `;
            // the bellow is with mili seconds
            // `${days}d ${hours}h ${minutes}m ${seconds}s ${milliseconds}ms`;

        // If the countdown is finished
        if (distance < 0) {
            clearInterval(timer); // Stop the countdown
                       
            document.getElementById("countdown").innerHTML = `The countdown has ended. Exam Has already Began</a>`;
             
           
        }
    }

    // Only start the countdown if there's an active exam
    let timer;
    if (hasActiveExam) {
        // Update the countdown every millisecond
        timer = setInterval(updateCountdown, 1);
    } else {
        document.getElementById("countdown").innerHTML = "<p style='color: orange; font-size: 18px;'>No exam set for timer</p>";
    }
</script>