<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/signup.css" />
    <link type="image/x-icon" rel="icon" href="../img/logo2.png" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"
    />
    <style>
      /* Additional styles specific to forgot password page */
      .form.forgot-password {
        max-width: 450px;
      }
      .step {
        display: none;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
      }
      .step.active {
        display: block;
        opacity: 1;
        transform: translateY(0);
      }
      .instruction-text {
        margin: 15px 0;
        color: #666;
        font-size: 14px;
        line-height: 1.5;
      }
      .steps-indicator {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 25px;
      }
      .step-dot {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        font-weight: bold;
        margin: 0 10px;
        position: relative;
      }
      .step-dot.active {
        background: #333;
        color: white;
      }
      .step-dot.completed {
        background: #4CAF50;
        color: white;
      }
      .step-line {
        width: 40px;
        height: 2px;
        background: #ddd;
      }
      .step-line.active {
        background: #333;
      }
      .step-line.completed {
        background: #4CAF50;
      }
      .otp-inputs {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin: 20px 0;
      }
      .otp-inputs input {
        width: 50px;
        height: 50px;
        text-align: center;
        font-size: 24px;
        border: 2px solid #ddd;
        border-radius: 5px;
      }
      .otp-inputs input:focus {
        border-color: #333;
        outline: none;
      }
      .resend-otp {
        text-align: center;
        margin-top: 15px;
        font-size: 14px;
      }
      .resend-otp a {
        color: #333;
        font-weight: bold;
        text-decoration: none;
      }
      .resend-otp a:hover {
        text-decoration: underline;
      }
      .timer {
        color: #666;
        font-weight: normal;
      }
      .password-strength {
        height: 4px;
        background: #ddd;
        border-radius: 2px;
        margin-top: 5px;
        overflow: hidden;
      }
      .strength-meter {
        height: 100%;
        width: 0%;
        transition: width 0.3s ease;
      }
      .strength-weak { background: #ff4d4d; }
      .strength-fair { background: #ffa500; }
      .strength-good { background: #4CAF50; }
      .strength-strong { background: #008000; }
    </style>
  </head>
  <body>
    <div class="wrapper">
      <section class="form forgot-password">
        <header>Reset Your Password</header>
        
        <!-- Steps Indicator -->
        <div class="steps-indicator">
          <div class="step-dot active" data-step="1">1</div>
          <div class="step-line"></div>
          <div class="step-dot" data-step="2">2</div>
          <div class="step-line"></div>
          <div class="step-dot" data-step="3">3</div>
        </div>

        <form action="#" autocomplete="off" id="forgotPasswordForm">
          <!-- Step 1: Enter Email -->
          <div class="step active" id="step1">
            <div class="instruction-text">
              Enter your email address associated with your account. We'll send you a verification code.
            </div>
            
            <div class="field input">
              <label>Email Address</label>
              <input type="email" name="email" id="email" placeholder="Enter your email" required />
            </div>

            <div class="field button">
              <button type="button" class="next-step" data-next="step2">Send Verification Code</button>
            </div>
          </div>

          <!-- Step 2: Enter OTP -->
          <div class="step" id="step2">
            <div class="instruction-text">
              Enter the 6-digit verification code sent to your email.
            </div>
            
            <div class="otp-inputs">
              <input type="text" maxlength="1" class="otp-digit" data-index="1" />
              <input type="text" maxlength="1" class="otp-digit" data-index="2" />
              <input type="text" maxlength="1" class="otp-digit" data-index="3" />
              <input type="text" maxlength="1" class="otp-digit" data-index="4" />
              <input type="text" maxlength="1" class="otp-digit" data-index="5" />
              <input type="text" maxlength="1" class="otp-digit" data-index="6" />
            </div>
            <input type="hidden" name="otp" id="otp" />

            <div class="resend-otp">
              Didn't receive code? <a href="#" id="resendOtp">Resend</a>
              <span class="timer" id="timer">(00:60)</span>
            </div>

            <div class="field button">
              <button type="button" class="prev-step" data-prev="step1">Back</button>
              <button type="button" class="next-step" data-next="step3">Verify Code</button>
            </div>
          </div>

          <!-- Step 3: Reset Password -->
          <div class="step" id="step3">
            <div class="instruction-text">
              Create a new password for your account.
            </div>
            
            <div class="field input">
              <label>New Password</label>
              <input type="password" name="newPassword" id="newPassword" placeholder="Enter new password" />
              <i class="fas fa-eye"></i>
              <div class="password-strength">
                <div class="strength-meter" id="strengthMeter"></div>
              </div>
            </div>

            <div class="field input">
              <label>Confirm New Password</label>
              <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm new password" />
              <i class="fas fa-eye"></i>
              <div class="password-match" id="passwordMatch"></div>
            </div>

            <!-- Loader Section -->
            <div class="loader">
              <div class="spinner"></div>
              <div class="loader-text">Updating password...</div>
            </div>

            <div class="field button">
              <button type="button" class="prev-step" data-prev="step2">Back</button>
              <input type="submit" value="Reset Password" />
            </div>
          </div>

          <div class="error-txt"></div>
        </form>

        <div class="link">
          Remember your password? <a href="login.php">Login now</a>
        </div>
      </section>
    </div>
    <script src="js/forgetP.js"></script>
   
  </body>
</html>