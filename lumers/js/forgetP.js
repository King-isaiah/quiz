// lumers/js/forgetP.js
document.addEventListener('DOMContentLoaded', function() {
    // Step navigation
    const steps = document.querySelectorAll('.step');
    const stepDots = document.querySelectorAll('.step-dot');
    const stepLines = document.querySelectorAll('.step-line');
    
    function goToStep(stepNumber) {
        // Hide all steps
        steps.forEach(step => step.classList.remove('active'));
        
        // Show target step
        document.getElementById(`step${stepNumber}`).classList.add('active');
        
        // Update step indicators
        stepDots.forEach((dot, index) => {
            dot.classList.remove('active', 'completed');
            if (index + 1 < stepNumber) {
                dot.classList.add('completed');
            } else if (index + 1 === stepNumber) {
                dot.classList.add('active');
            }
        });
        
        stepLines.forEach((line, index) => {
            line.classList.remove('active', 'completed');
            if (index + 1 < stepNumber) {
                line.classList.add('completed');
            }
        });
    }
    
    // Next step buttons
    document.querySelectorAll('.next-step').forEach(button => {
        button.addEventListener('click', function() {
            const nextStep = this.dataset.next.replace('step', '');
            const currentStep = document.querySelector('.step.active').id.replace('step', '');
            
            // Validate current step before proceeding
            if (validateStep(currentStep)) {
                goToStep(nextStep);
            }
        });
    });
    
    // Previous step buttons
    document.querySelectorAll('.prev-step').forEach(button => {
        button.addEventListener('click', function() {
            const prevStep = this.dataset.prev.replace('step', '');
            goToStep(prevStep);
        });
    });
    
    // OTP input handling
    const otpInputs = document.querySelectorAll('.otp-digit');
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            // Auto-focus next input
            if (this.value.length === 1 && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
            
            // Update hidden OTP field
            updateOTPField();
        });
        
        input.addEventListener('keydown', function(e) {
            // Handle backspace
            if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                otpInputs[index - 1].focus();
            }
        });
    });
    
    function updateOTPField() {
        let otp = '';
        otpInputs.forEach(input => otp += input.value);
        document.getElementById('otp').value = otp;
    }
    
    // Password strength indicator
    const passwordInput = document.getElementById('newPassword');
    const strengthMeter = document.getElementById('strengthMeter');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const passwordMatch = document.getElementById('passwordMatch');
    
    if (passwordInput && strengthMeter) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Calculate strength
            if (password.length >= 8) strength += 25;
            if (/[A-Z]/.test(password)) strength += 25;
            if (/[0-9]/.test(password)) strength += 25;
            if (/[^A-Za-z0-9]/.test(password)) strength += 25;
            
            // Update meter
            strengthMeter.style.width = strength + '%';
            strengthMeter.className = 'strength-meter';
            
            if (strength < 50) {
                strengthMeter.classList.add('strength-weak');
            } else if (strength < 75) {
                strengthMeter.classList.add('strength-fair');
            } else if (strength < 100) {
                strengthMeter.classList.add('strength-good');
            } else {
                strengthMeter.classList.add('strength-strong');
            }
        });
    }
    
    // Password confirmation check
    if (confirmPasswordInput && passwordMatch) {
        confirmPasswordInput.addEventListener('input', function() {
            const newPassword = passwordInput.value;
            const confirmPassword = this.value;
            
            if (confirmPassword === '') {
                passwordMatch.textContent = '';
                passwordMatch.style.color = '';
            } else if (newPassword === confirmPassword) {
                passwordMatch.textContent = '✓ Passwords match';
                passwordMatch.style.color = '#4CAF50';
            } else {
                passwordMatch.textContent = '✗ Passwords do not match';
                passwordMatch.style.color = '#ff4d4d';
            }
        });
    }
    
    // Resend OTP timer
    let timerInterval;
    let timeLeft = 60;
    
    function startTimer() {
        clearInterval(timerInterval);
        timeLeft = 60;
        
        const timerElement = document.getElementById('timer');
        const resendLink = document.getElementById('resendOtp');
        
        if (timerElement && resendLink) {
            resendLink.style.pointerEvents = 'none';
            resendLink.style.opacity = '0.5';
            
            timerInterval = setInterval(() => {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                
                timerElement.textContent = `(${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')})`;
                
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    resendLink.style.pointerEvents = 'auto';
                    resendLink.style.opacity = '1';
                    timerElement.textContent = '';
                }
                
                timeLeft--;
            }, 1000);
        }
    }
    
    // Form validation for each step
    function validateStep(stepNumber) {
        const errorDiv = document.querySelector('.error-txt');
        errorDiv.textContent = '';
        
        switch(stepNumber) {
            case '1':
                const email = document.getElementById('email').value;
                if (!email || !validateEmail(email)) {
                    errorDiv.textContent = 'Please enter a valid email address';
                    return false;
                }
                return true;
                
            case '2':
                const otp = document.getElementById('otp').value;
                if (otp.length !== 6) {
                    errorDiv.textContent = 'Please enter the complete 6-digit code';
                    return false;
                }
                return true;
                
            case '3':
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                
                if (newPassword.length < 8) {
                    errorDiv.textContent = 'Password must be at least 8 characters long';
                    return false;
                }
                
                if (newPassword !== confirmPassword) {
                    errorDiv.textContent = 'Passwords do not match';
                    return false;
                }
                
                return true;
        }
        
        return true;
    }
    
    // Email validation function
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    // Initialize timer
    startTimer();
    
    // Password show/hide toggle
    const passwordFields = document.querySelectorAll('.field.input');
    passwordFields.forEach(field => {
        const eyeIcon = field.querySelector('.fa-eye');
        const passwordInput = field.querySelector('input[type="password"]');
        
        if (eyeIcon && passwordInput) {
            eyeIcon.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    });
    
    // =============================================
    // API FUNCTIONS (YOUR EXISTING CODE)
    // =============================================
    
    async function sendOTP(email) {
        try {
            const response = await fetch('php/send-otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email: email })
            });
            
            return await response.json();
            
        } catch (error) {
            console.error('Error:', error);
            return {
                success: false,
                message: 'Network error. Please check your connection.'
            };
        }
    }

    async function verifyOTP(email, otp) {
        try {
            const response = await fetch('php/verify-otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    email: email, 
                    otp: otp 
                })
            });
            
            return await response.json();
            
        } catch (error) {
            console.error('Error:', error);
            return {
                success: false,
                message: 'Network error during verification'
            };
        }
    }

    async function resetPassword(email, password, confirmPassword, token) {
        try {
            const response = await fetch('includes/reset-password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    email: email,
                    password: password,
                    confirm_password: confirmPassword,
                    token: token
                })
            });
            
            return await response.json();
            
        } catch (error) {
            console.error('Error:', error);
            return {
                success: false,
                message: 'Network error during password reset'
            };
        }
    }

    // Step 1: Send OTP
    document.querySelector('#step1 .next-step').addEventListener('click', async function() {
        const email = document.getElementById('email').value;
        const errorDiv = document.querySelector('.error-txt');
        
        if (!validateEmail(email)) {
            errorDiv.textContent = 'Please enter a valid email address';
            return;
        }
        
        // Show loading
        const button = this;
        const originalText = button.textContent;
        button.textContent = 'Sending OTP...';
        button.disabled = true;
        errorDiv.textContent = '';
        
        // Send OTP
        const result = await sendOTP(email);
        
        if (result.success) {
            localStorage.setItem('resetEmail', email);
            goToStep(2);
            startTimer();
        } else {
            errorDiv.textContent = result.message || 'Failed to send OTP';
        }
        
        button.textContent = originalText;
        button.disabled = false;
    });

    // Step 2: Verify OTP
    document.querySelector('#step2 .next-step').addEventListener('click', async function() {
        const email = localStorage.getItem('resetEmail');
        const otp = document.getElementById('otp').value;
        const errorDiv = document.querySelector('.error-txt');
        
        if (!email) {
            errorDiv.textContent = 'Email not found. Please start over.';
            goToStep(1);
            return;
        }
        
        if (otp.length !== 6) {
            errorDiv.textContent = 'Please enter the complete 6-digit code';
            return;
        }
        
        // Show loading
        const button = this;
        const originalText = button.textContent;
        button.textContent = 'Verifying...';
        button.disabled = true;
        errorDiv.textContent = '';
        
        // Verify OTP
        const result = await verifyOTP(email, otp);
        
        if (result.success) {
            localStorage.setItem('resetToken', result.reset_token);
            goToStep(3);
        } else {
            errorDiv.textContent = result.message || 'Invalid OTP';
        }
        
        button.textContent = originalText;
        button.disabled = false;
    });

    // Step 3: Reset Password
    document.getElementById('forgotPasswordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = localStorage.getItem('resetEmail');
        const token = localStorage.getItem('resetToken');
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        const errorDiv = document.querySelector('.error-txt');
        
        // Validate
        if (newPassword.length < 8) {
            errorDiv.textContent = 'Password must be at least 8 characters';
            return;
        }
        
        if (newPassword !== confirmPassword) {
            errorDiv.textContent = 'Passwords do not match';
            return;
        }
        
        if (!email || !token) {
            errorDiv.textContent = 'Session expired. Please start over.';
            goToStep(1);
            return;
        }
        
        // Show loader
        const loader = document.querySelector('.loader');
        if (loader) loader.style.display = 'block';
        
        // Reset password
        const result = await resetPassword(email, newPassword, confirmPassword, token);
        
        // Hide loader
        if (loader) loader.style.display = 'none';
        
        if (result.success) {
            alert('✅ Password reset successful!\nYou can now login with your new password.');
            
            // Clear storage
            localStorage.removeItem('resetEmail');
            localStorage.removeItem('resetToken');
            
            // Redirect to login
            window.location.href = 'login.php';
        } else {
            errorDiv.textContent = result.message || 'Failed to reset password';
        }
    });

    // Resend OTP
    document.getElementById('resendOtp').addEventListener('click', async function(e) {
        e.preventDefault();
        
        const email = localStorage.getItem('resetEmail');
        if (!email) {
            alert('Please enter your email again');
            goToStep(1);
            return;
        }
        
        const errorDiv = document.querySelector('.error-txt');
        const button = this;
        const originalText = button.textContent;
        
        button.textContent = 'Resending...';
        errorDiv.textContent = '';
        
        const result = await sendOTP(email);
        
        if (result.success) {
            startTimer();
            errorDiv.textContent = '✅ New OTP sent!';
            errorDiv.style.color = '#28a745';
            setTimeout(() => errorDiv.textContent = '', 3000);
        } else {
            errorDiv.textContent = result.message || 'Failed to resend OTP';
        }
        
        button.textContent = originalText;
    });
});