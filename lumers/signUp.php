<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register Now</title>
    <link rel="stylesheet" href="css/signupU.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"
    />
    <link type="image/x-icon" rel="icon" href="../img/logo2.png" />
  </head>
  <body>
    <div class="wrapper">
      <!-- Loader Section - Initially hidden -->
      <div class="fullpageLoader" id="fullpageLoader">
        <div class="overlay-spinner"></div>
        <div class="overlay-text">Creating your account...</div>
      </div>
      
      <section class="form signup">
        <header>Sign Up For The LUMERS</header>
        <form action="#" enctype="multipart/form-data" autocomplete="off" id="signupForm">
          <div class="error-txt" id="errorText"></div>
          <div class="success-txt" id="successText"></div>
          <div class="name-details">
            <div class="field input">
              <label>First Name</label>
              <input type="text" name="fname" placeholder="First Name" required/>
            </div>
            <div class="field input">
              <label>Last Name</label>
              <input type="text" name="lname" placeholder="Last Name" required/>
            </div>
          </div>
          <div class="name-details">
            <div class="field input">
              <label>UserName</label>
              <input type="text" name="username" placeholder="Username" required/>
            </div>
            <div class="field input">
              <label>Email Address</label>
              <input type="email" name="email" placeholder="Enter your email" required/>
            </div>
          </div>
          <div class="name-details">
            <div class="field input">
              <label>Phone Number</label>
              <input type="tel" name="phone_number" placeholder="Enter your phone number" required/>
            </div> 
            <div class="field input">
              <label>Password</label>
              <input type="password" name="password" placeholder="Enter new password" required/>
              <i class="fas fa-eye"></i>
            </div>
          </div>
          <div class="name-details">
            <div class="field input">
              <label>Gender</label>
              <select name="gender" required>
                <option value="">Select A Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
              </select>
            </div>
            <div class="field image">
              <label>Select profile Image</label>
              <input type="file" name="image" accept="image/*" required/>
            </div>
          </div>
          <div class="field button">
            <input type="submit" value="Sign Up" id="submitBtn"/> 
          </div>
        </form>
        <div class="link">
          Already signed up?<a href="login.php">Login now</a>
        </div>
      </section>
    </div>

    <script src="js/pass-show-hide.js"></script>
    <script src="js/signup.js"></script>
  </body>
</html>