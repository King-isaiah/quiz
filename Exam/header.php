
<?php
     if(!isset($_SESSION['unique_id'])){
        ?>
        <script type="text/javascript">
            window.location.href="../lumers/login.php";
        </script>
        <?php
    }
    
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Online Quiz System</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link type="image/x-icon" rel="icon" href="../img/logo2.png" />
    <link rel="stylesheet" href="css1/bootstrap.min.css">
    <link rel="stylesheet" href="css1/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../students/css/style.css">
    <link rel="stylesheet" href="../students/css/eshioze.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>



    <style>
        .header-top-wrapper {
    display: flex;             
    justify-content: space-between; 
    align-items: center;      
}

.header-top-menu {
    display: flex;            /* Stack the items in one line */
}

.header-top-menu .nav {
    display: flex;            /* Display links in one line */
}

.header-top-menu .nav li {
    margin-right: 20px;      /* Provide some spacing between links */
}

@media (max-width: 910px) {
    .header-top-wrapper {
        flex-wrap: nowrap;    /* Prevent the items from breaking onto a new line */
        overflow: hidden;      /* Ensure overflow is hidden */
    }

    .header-right-info {
        margin-left: auto;     /* Push username section to the right */
    }

    .header-right-info img {
        max-width: 30px;      /* Optional: Adjust image size */
        border-radius: 50%;    /* Optional: Make the image round */
    }
}
    </style>

</head>

<body>

    <div class="all-content-wrapper">
        
        <div class="header-advance-area header" id= "header">
            <!-- <div class="header-top-area">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="header-top-wraper">
                                <div class="row">
                                    <div class="col-lg-1 col-md-0 col-sm-1 col-xs-12">
                                        <div class="menu-switcher-pro">
                                            <button type="button" id="sidebarCollapse" class="btn bar-button-pro header-drl-controller-btn btn-info navbar-btn">
													<i class="educate-icon educate-nav"></i>
												</button>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12">
                                        <div class="header-top-menu tabl-d-n">
                                            <ul class="nav navbar-nav mai-top-nav">
                                            
                                                <li class="nav-item"><a href="old_exam_results.php" class="nav-link">Last Results</a>
                                                </li>
                                                <li class="nav-item"><a href="../students/Exam-Inventry.php" class="nav-link">Exam Inventory</a>
                                                </li>
                                                
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                        <div class="header-right-info">
                                            <ul class="nav navbar-nav mai-top-nav header-right-menu">


                                                <li class="nav-item">
                                                    <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle">
															<img src="../lumers/php/images/<?php echo $_SESSION['img']?>" alt="" />
															<span class="admin-name"><?php echo $_SESSION['userssname']?></span>
															<i class="fa fa-angle-down edu-icon edu-down-arrow"></i>
														</a>
                                                    <ul role="menu" class="dropdown-header-top author-log dropdown-menu animated zoomIn">
                                                        
                                                        </li>
                                                        <li><a href="logout.php"><span class="edu-icon edu-locked author-log-ic"></span>Log Out</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                                
                                            </ul>
                                        </div>
                                    </div>
                                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                                        <i class="fa fa-bars"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="header-top-area">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="header-top-wrapper">
                    <div class="header-top-menu tabl-d-n">
                        <ul class="nav navbar-nav mai-top-nav" id="main-menu">
                            <li class="nav-item"><a href="old_exam_results.php" class="nav-link">Last Results</a></li>
                            <li class="nav-item"><a href="../students/Exam-Inventry.php" class="nav-link">Exam Inventory</a></li>
                        </ul>
                    </div>

                    <div class="header-right-info">
                        <ul class="nav navbar-nav mai-top-nav header-right-menu">
                            <li class="nav-item">
                                <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle">
                                    <img src="../lumers/php/images/<?php echo $_SESSION['img']?>" alt=""/>
                                    <span class="admin-name"><?php echo $_SESSION['userssname']?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
            <!-- Mobile Menu start -->

            <!-- Mobile Menu end -->
            <div class="breadcome-area">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="breadcome-list">
                                <div class="row">

                                    <div class="col-lg-12 col-md-6 col-sm-6 col-xs-12 text-right">
                                        <ul class="breadcome-menu">
                                            <li>
                                                <div id="countdowntimer" style="display: block;"></div>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<script type="text/javascript">
    var timerInterval = setInterval(function(){
        timer();
    }, 1000);
    let current ;
  
    

    


function timer() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            var serverTime = xmlhttp.responseText; // Get the timer value from the server
            
            // Check if the timer is about to reach zero
            if (serverTime === "00:00:01:000") {
                clearInterval(timerInterval); // Stop the timer
                document.getElementById("countdowntimer").innerHTML = "00:00:00:000"; // Reset display to 00:00:00
                
                // Redirect to results page after sending
                window.location.href = "result.php"; 
            } else {
                // Update timer display
                document.getElementById("countdowntimer").innerHTML = serverTime; // Update the timer display
                console.log("Current timer:", serverTime); // Log the current timer value
                
                // Send the current timer value to PHP
                sendCurrentTimerToPHP(serverTime);
            }
        }
    };

    xmlhttp.open("GET", "forajax/load_timer.php", true);
    xmlhttp.send(null);
}

// Function to send the current timer value to PHP
function sendCurrentTimerToPHP(timerValue) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "save_timer.php", true); // Create a PHP file to handle the timer value
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("timerValue=" + encodeURIComponent(timerValue)); // Send timer value
}

</script>

