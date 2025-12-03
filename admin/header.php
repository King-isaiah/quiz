<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="apple-icon.png">
    <link rel="shortcut icon" href="../img/logo2.png">

    <link rel="stylesheet" href="vendors/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>   
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
</head>
<body>
    <!-- Left Panel -->
    <aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">
            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="./">Admin Panel</a>
                <a class="navbar-brand hidden" href="./"><img src="images/logo2.png" alt="Logo"></a>
            </div>

            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="exam_category.php"> <i class="menu-icon fa-solid fa-book-open"></i>Add and Edit Exam </a>
                    </li>
                    <li>
                        <a href="edit_exam_category.php"> <i class="menu-icon fa fa-folder"></i>Exam Category </a>
                    </li>
                    <li>
                       <a href="add_edit_exam_questions.php"> <i class="menu-icon fa fa-book"></i>Add and Edit Questions</a>
                    </li>
                    <li>
                       <a href="exam-questions.php"> <i class="menu-icon fa fa-question"></i>All Exam Questions</a>
                    </li>
                    <li>
                       <a href="old-exam-reults.php"> <i class="menu-icon fa fa-clipboard-check"></i>All Exam Results</a>
                    </li>
                    <li>
                       <a href="examReults-byYear.php"> <i class="menu-icon fa fa-calendar"></i>Results by year</a>
                    </li>
                    <li>
                       <a href="payments.php"> <i class="menu-icon fas fa-credit-card"></i>Payment Records</a>
                    </li>
                    <li>
                       <a href="active-users.php"> <i class="menu-icon fa-solid fa-regular fa-user"></i>Active Users</a>
                    </li>
                    <li>
                        <a href="logout.php"> <i class="menu-icon fa fa-close"></i>LogOut </a>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </aside><!-- /#left-panel -->

    <!-- Left Panel -->

    <!-- Right Panel -->
    <div id="right-panel" class="right-panel">
        <!-- Spinner Overlay -->
        <div class="spinner-overlay" id="spinnerOverlay">
            <div class="spinner"></div>
        </div>

        <!-- Toast Container -->
        <div class="toast-container" id="toastContainer"></div>
        
        <!-- Header-->
        <header id="header" class="header">
            <div class="header-menu">
                <!-- toggle header incase you need to bring it back -->
                <div class="col-sm-7">                    
                    <!-- <div class="global-db-toggle" id="globalDbToggle" style="display: block !important;">
                        <div class="db-toggle-container d-flex align-items-center">
                            <label class="db-toggle-switch mb-0">
                                <input type="checkbox" id="globalDatabaseToggle">
                                <span class="db-toggle-slider db-toggle-round"></span>
                            </label>
                            <span id="globalDatabaseStatus" class="ml-2"></span>
                        </div>
                    </div> -->
                </div>

                <div class="col-sm-5">
                    <div class="user-area dropdown float-right">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="user-avatar rounded-circle" src="../img/logo2.png" alt="User Avatar">
                        </a>
                        <div class="user-menu dropdown-menu">
                            <a class="nav-link" href="logout.php"><i class="fa fa-power-off"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </header><!-- /header -->

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Global Search Container (now in main content, hidden by default) -->
            <div class="global-search-container" id="globalSearchContainer">
                <div class="container-fluid">
                    <div class="table-search-container">
                        <div class="form-group mb-0">
                            <input type="text" 
                                   id="globalTableSearch" 
                                   class="form-control table-search-input" 
                                   placeholder="Search...">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Your page content will be inserted here by individual pages -->