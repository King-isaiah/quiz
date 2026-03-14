<?php session_start();?>
<!doctype html>

<html class="no-js" lang="en">


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Login</title>
    <meta name="description" content="Test">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    
    <link rel="apple-touch-icon" href="apple-icon.png">
    <link rel="shortcut icon" href="../img/logo2.png">


    <link rel="stylesheet" href="vendors/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendors/font-awesome/css/font-awesome.min.css">
    

    <link rel="stylesheet" href="assets/css/style.css">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>



</head>

<body class="bg-dark">

    <!-- Spinner Overlay -->
    <div class="spinner-overlay" id="spinnerOverlay">
        <div class="spinner"></div>
    </div>

    <div class="sufee-login d-flex align-content-center flex-wrap">
        <div class="container">
            <div class="login-content">
                <div class="login-logo" style="color:white">
                   Admin Login
                </div>
                <div class="login-form form login">
                    <form name="form1" action="" method="post">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username"  class="form-control" placeholder="username" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                                
                        <button type="submit" name="submit1" class="btn btn-success btn-flat m-b-30 m-t-30">Sign in</button>
                       
                        <div class="alert alert-danger" id="errormsg" style="margin-top: 10px; display:none">
                            <!-- <strong>Invalid!</strong> Invalid Username or password. -->
                        </div>     
                    </form>
                </div>
            </div>
        </div>
    </div>


  
   
    <script src="js/adminlogin.js"></script>


</body>

</html>


