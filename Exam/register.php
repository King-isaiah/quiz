<?php
    include "connection.php"
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Register Now</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css?family=Play:400,700" rel="stylesheet">
       <link rel="stylesheet" href="css1/bootstrap.min.css">
       <link rel="stylesheet" href="css1/font-awesome.min.css">
      <link rel="stylesheet" href="css1/owl.carousel.css">
    <link rel="stylesheet" href="css1/owl.theme.css">
    <link rel="stylesheet" href="css1/owl.transitions.css">
      <link rel="stylesheet" href="css1/animate.css">
      <link rel="stylesheet" href="css1/normalize.css">
      <link rel="stylesheet" href="css1/main.css">
      <link rel="stylesheet" href="style.css">
       <link rel="stylesheet" href="css1/responsive.css">
      <script src="js/vendor/modernizr-2.8.3.min.js"></script>
</head>

<body>
<div id="wrapper">
<div class="error-pagewrap">
		<div class="error-page-int">
			<div class="text-center custom-login">
				<h2>Register Now</h2>
				<h4>An email would be sent to this user to confirm succesful registration</h4>

			</div>
			<div class="content-error">
				<div class="hpanel">
                    <div class="panel-body">
                        <form action="" name="form1" method="post">
                            <div class="row">
                                <div style = "display:flex;">
                                    <div class="form-group col-lg-12">
                                        <label>FirstName</label>
                                        <input type="text" name="firstname" class="form-control">
                                    </div>
                                    <div class="form-group col-lg-12">
                                        <label>LastName</label>
                                        <input type="text"name="lastname" class="form-control">
                                    </div>
                                </div>
                                <div style = "display:flex;">
                                    <div class="form-group col-lg-12">
                                        <label>Username</label>
                                        <input type="text" name="username" class="form-control">
                                    </div>
                                    <div class="form-group col-lg-12">
                                        <label>Password</label>
                                        <input type="password" name="password" class="form-control">
                                    </div>
                                </div>
                                <div style = "display:flex;">
                                    <div class="form-group col-lg-12">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control">
                                    </div>
                                    <div class="form-group col-lg-12">
                                        <label>Contact</label>
                                        <input type="text" name="contact" class="form-control">
                                    </div>
                                </div>
                                
                                <select class="form-control allcompanys" name="gender" style="margin:2em; width :85%;">
                                    <option value="">Male</option>
                                    <option value="">Female</option>
                                </select>
                                
                              </div>
                            <div class="text-center">
                                <button type="submit" name="submit1" style="background-color: green;" class="btn btn-success loginbtn">Register</button>
                               
                            </div>

                            <!-- remember to use with the bilcms -->
                            <div class="alert alert-success" id="success" style="margin-top:10px; display:none">
                                <strong>Success!</strong> Account Registered successfully.
                            </div>
                            <a class="btn btn-default btn-block" style="background-color: green;" href="login.php">Login Page</a>
                            <div class="alert alert-danger" id="failure" style="margin-top:10px; display:none">
                                <strong>Already Exists!</strong> User already Registered.
                            </div>
                        </form>
                    </div>
                </div>
			</div>

		</div>   
    </div>
</div>
	
    
    <?php
        if (isset($_POST["submit1"])) {
            $count = 0;
            $email = $_POST['email'];

            // Email validation
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                ?>
                <script type="text/javascript">
                    document.getElementById("success").style.display = "none";
                    document.getElementById("failure").style.display = "block";
                    alert("Invalid email format. Please enter a valid email address.");
                </script>
                <?php
            } else {
                
                $res = mysqli_query($link, "SELECT * FROM registration WHERE username='$_POST[username]'") or die(mysqli_error($link));
                $count = mysqli_num_rows($res);

                if ($count > 0) {
                    ?>
                    <script type="text/javascript">
                        document.getElementById("success").style.display = "none";
                        document.getElementById("failure").style.display = "block";
                    </script>
                    <?php
                } else {
                    $password= $_POST['password'];
                    $hash = password_hash("$password", PASSWORD_BCRYPT);
                    mysqli_query($link, "INSERT INTO registration VALUES(NULL,
                    '$_POST[firstname]', '$_POST[lastname]', '$_POST[username]', '$hash',
                    '$email', '$_POST[contact]', '$_POST[gender]')") or die(mysqli_error($link));





                    ?>
                    
                    <script type="text/javascript">
                        document.getElementById("success").style.display = "block";
                        document.getElementById("failure").style.display = "none";
                        
                        // window.location = "login.php";
                    </script>
                    <?php
                }
                
                            // echo $_SESSION['email'];
                            echo $email;
                            if (isset($email)) {
                                $to = $email; // Retrieve the email address from the session
                                $subject = 'Bro i am testing if this email would work';
                                $message = 'Congratulations you have succesfully registered to the lumos study.';
                                $headers = 'From: eshiozemheafuwape@gmail.com' . "\r\n" .
                                        'Reply-To: eshiozemheafuwape@gmail.com' . "\r\n" .
                                        'X-Mailer: PHP/' . phpversion();
                            
                                // Send the email
                                if (mail($to, $subject, $message, $headers)) {
                                    echo 'Email sent successfully!';
                                } else {
                                    echo 'Email sending failed.';
                                }
                            } else {
                                echo 'No email address found in session.';
                            }
                        
            }
        }
    ?>

    <script src="js/vendor/jquery-1.12.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/wow.min.js"></script>
    <script src="js/jquery-price-slider.js"></script>
    <script src="js/jquery.meanmenu.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.sticky.js"></script>
    <script src="js/jquery.scrollUp.min.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>

</body>

</html>
<script>
    let wrapper= document.getElementById('wrapper')
    function displayRegister(){}
</script>