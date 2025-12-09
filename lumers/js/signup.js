// Select elements
const form = document.querySelector(".signup form");
const submitBtn = document.querySelector("#submitBtn");
const successText = document.querySelector("#successText");
const errorText = document.querySelector("#errorText");
const fullpageLoader = document.querySelector("#fullpageLoader");

// Prevent default form submission
form.onsubmit = (e) => {
  e.preventDefault();
  submitForm();
};

// Form submission function
function submitForm() {
  // Only show loader when form is valid and ready to submit
  if (!form.checkValidity()) {
    // Trigger HTML5 validation
    form.reportValidity();
    return;
  }

  // Show loader AFTER form validation passes
  showLoader();
  
  // Disable the submit button
  submitBtn.disabled = true;
  
  // Hide any previous messages
  hideMessages();

  // Start AJAX request
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/signup.php", true);
  
  xhr.onload = () => {
    // Always hide loader when request completes
    hideLoader();
    submitBtn.disabled = false;

    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        let data = xhr.response.trim();
        console.log("Server Response:", data);

        if (data === "success") {
          showSuccess("Account created successfully! Redirecting to login...");
          
          // Redirect after 2 seconds
          setTimeout(() => {
            window.location.href = "login.php";
          }, 2000);
        } else {
          showError(data);
        }
      } else {
        showError("Server error occurred. Please try again.");
      }
    }
  };

  xhr.onerror = () => {
    hideLoader();
    submitBtn.disabled = false;
    showError("Network error! Please check your connection.");
  };

  xhr.ontimeout = () => {
    hideLoader();
    submitBtn.disabled = false;
    showError("Request timeout! Please try again.");
  };

  // Set timeout (40 seconds)
  xhr.timeout = 40000;
  
  // Send form data
  let formData = new FormData(form);
  xhr.send(formData);
}

// Helper functions
function showLoader() {
  fullpageLoader.style.display = "flex";
  form.classList.add("form-disabled");
}

function hideLoader() {
  fullpageLoader.style.display = "none";
  form.classList.remove("form-disabled");
}

function showError(message) {
  errorText.textContent = message;
  errorText.style.display = "block";
  successText.style.display = "none";
}

function showSuccess(message) {
  successText.textContent = message;
  successText.style.display = "block";
  errorText.style.display = "none";
}

function hideMessages() {
  errorText.style.display = "none";
  successText.style.display = "none";
}

// Password show/hide functionality (if you have it)
document.querySelectorAll('.field input[type="password"]').forEach(input => {
  const eyeIcon = input.nextElementSibling;
  if (eyeIcon && eyeIcon.classList.contains('fa-eye')) {
    eyeIcon.addEventListener('click', () => {
      if (input.type === 'password') {
        input.type = 'text';
        eyeIcon.classList.add('active');
      } else {
        input.type = 'password';
        eyeIcon.classList.remove('active');
      }
    });
  }
});