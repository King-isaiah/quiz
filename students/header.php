<?php ob_start(); //session_start()?>
<?php 
   
    if(!isset($_SESSION['unique_id'])){
    
        header("Location: ../lumers/login.php");    
        exit();   
    }


    // $sessionTimeout = 10 * 60; 

    $sessionTimeout = 1 * 60 * 60; // 1 hours in seconds

    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time(); 
    }else {
        if (time() - $_SESSION['last_activity'] > $sessionTimeout) {            
            
            echo "Session expired due to inactivity.";            
            header("Location: php/logout.php");
            exit();
        }
    }

    // Update last activity time
    $_SESSION['last_activity'] = time();

   
?>
<?php 
    // include_once "php/config.php";
    include "../superbase/config.php";
    
    // $sql = mysqli_query($link, "SELECT * FROM registration WHERE unique_id = {$_SESSION['unique_id']}");
    // if(mysqli_num_rows($sql) > 0) {
    //     $row = mysqli_fetch_assoc($sql);
    // } 
    $response = fetchData('registration?unique_id=eq.' . $_SESSION['unique_id']);

    if (isset($response[0])) {
        $row = $response[0];
    } else {
        // Handle the case where the user is not found or an error occurred
        echo "User not found or an error occurred.";
    }
?>
<!doctype html>

<html class="no-js" lang="en">


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Students Portfolio</title>
    <meta name="description">
    <meta name="viewport" content="width=device-width, initial-scale=1">

   

    <link type="image/x-icon" rel="icon" href="../img/logo2.png" />
    <link rel="stylesheet" href="vendors/bootstrap/dist/css/bootstrap.min.css">
  

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/eshioze.css">
    <link rel="stylesheet" href="css/header.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
   
    <style>
       
        .toastify{padding:12px 20px;color:#fff;display:inline-block;box-shadow:0 3px 6px -1px rgba(0,0,0,.12),0 10px 36px -4px rgba(77,96,232,.3);background:-webkit-linear-gradient(315deg,#73a5ff,#5477f5);background:linear-gradient(135deg,#73a5ff,#5477f5);position:fixed;opacity:0;transition:all .4s cubic-bezier(.215, .61, .355, 1);border-radius:2px;cursor:pointer;text-decoration:none;max-width:calc(50% - 20px);z-index:2147483647}.toastify.on{opacity:1}.toast-close{background:0 0;border:0;color:#fff;cursor:pointer;font-family:inherit;font-size:1em;opacity:.4;padding:0 5px}.toastify-right{right:15px}.toastify-left{left:15px}.toastify-top{top:-150px}.toastify-bottom{bottom:-150px}.toastify-rounded{border-radius:25px}.toastify-avatar{width:1.5em;height:1.5em;margin:-7px 5px;border-radius:2px}.toastify-center{margin-left:auto;margin-right:auto;left:0;right:0;max-width:fit-content;max-width:-moz-fit-content}@media only screen and (max-width:360px){.toastify-left,.toastify-right{margin-left:auto;margin-right:auto;left:0;right:0;max-width:fit-content}}
        /*# sourceMappingURL=/sm/cb4335d1b03e933ed85cb59fffa60cf51f07567ed09831438c60f59afd166464.map */
    </style>


</head>

<body>
    <!-- Left Panel -->

    <aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">

            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <div class="navbar-brand" style="display: flex; color: #f1f2f7;"> 
            
                    <img class="lumos"  src="../img/logo1.png">                    
                </div>
                
             
            </div>

            <div id="main-menu" class="main-menu collapse navbar-collapse" style="background-color: #228B22;">
                <ul class="nav navbar-nav" >
                  
                    <li>
                        <a href="dashboard.php" style="font-size: 12px;"> <i class="menu-icon fa-solid fa-address-card"></i>Dashboard</a>
                    </li>
                    <!-- <li>
                       <a href="book.php" style="font-size: 12px;"> <i class="menu-icon fa-solid fa-book-open"></i>Book</a>
                    </li> -->
                    <li>
                       <a href="Exam-Participated.php" style="font-size: 12px;"> <i class="menu-icon fa-solid fa-book-open"></i>Exam Participated</a>
                    </li>
                    <li>
                       <a href="Exam-Board.php" style="font-size: 12px;"> <i class="menu-icon fa-solid fa fa-dashboard"></i>Exam-Board</a>
                    </li>
                    <li>
                       <a href="Account-Settings.php" style="font-size: 12px;"> <i class="menu-icon fa-solid  fa-regular fa-user"></i>Profile</a>
                    </li>
                    <li>
                       <a href="Exam-Inventry.php" style="font-size: 12px;"> <i class="menu-icon fa-solid fa-book-open-reader"></i>Exam-Inventary</a>
                       
                    </li>
                    <li>                                                
                        <a href="php/logout.php" style="font-size: 12px;"><i class="menu-icon fa fa-close"></i>Logout</a>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </aside><!-- /#left-panel -->

    <!-- Left Panel -->

    <!-- Right Panel -->

    <div id="right-panel" class="right-panel">

        <!-- Header-->
        <header id="header" class="header">

            <div class="header-menu">

                <div class="col-sm-7">
                   
                </div>

                <div class="col-sm-5">
               
                    <div class="user-area dropdown float-right">
                        <i class="fa-solid fa-message" style="margin-right: 1em;  margin-top:1em;"></i>
                        <i class="fa-solid fa-bell" style="margin-right: 1em; margin-top:1em;"></i>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="user-avatar profileHeader rounded-circle" src="../lumers/php/images/<?php echo $row['img'] ?>" alt="">
                            
                        </a>

                        <div class="user-menu dropdown-menu">
                            <a class="nav-link" href="php/logout.php"><i class="fa fa-power-off"></i> Logout</a>
                        </div>
                    </div>

                  

                </div>
            </div>

        </header><!-- /header -->
        <!-- Header-->
        <script src="../constant/serverurl.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
            // Get the current URL
            const currentPage = window.location.pathname.split("/").pop();

            // Get all menu items
            const menuItems = document.querySelectorAll("#main-menu .nav li a");

            // Loop through the menu items and add 'active' class to the current page
            menuItems.forEach(item => {
                const href = item.getAttribute("href");
                if (href === currentPage) {
                    item.classList.add("actives"); // Add 'active' class to the current menu item
                }
            });
        });
        </script>