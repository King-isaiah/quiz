

const form = document.querySelector(".signup form");
const continueBtn = document.querySelector(".button input");
const errorText = form.querySelector(".error-txt");

// Prevent the form from submitting by default
form.onsubmit = (e) => {
    e.preventDefault();
};

continueBtn.onclick = () => {
    // Create an AJAX request
    let xhr = new XMLHttpRequest(); 
    xhr.open("POST", "php/profileUpdate.php", true);
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // Parse JSON response from the backend
                let responseData = JSON.parse(xhr.response);
                console.log(responseData); 
                
                // Use Toastify for notifications based on the backend response
                Toastify({
                    text: responseData.message, // Message from response
                    duration: 3000,
                    newWindow: true,
                    close: true,
                    gravity: "top", 
                    position: "right", 
                    stopOnFocus: true, 
                    style: {
                        background: responseData.success ? "linear-gradient(to right, #4CAF50, #81C784)" : "linear-gradient(to right, #FF4C4C, #FF9E9E", // Green for success, red for error
                    },
                    onClick: function() {} // Optional click event
                }).showToast();

                // Redirect on successful update
                if (responseData.success) {
                    setTimeout(() => {
                        location.href = "/quiz/students/Account-Settings.php"; // Redirect after showing toast
                    }, 3000); // Wait for the toast to finish before redirecting
                }
                
            } else {
                // Handle HTTP errors, if any
                errorText.textContent = "An error occurred while processing your request.";
                errorText.style.display = "block";
            }
        }
    };

    // Send the form data through AJAX to PHP
    let formData = new FormData(form); // Creating new FormData object
    xhr.send(formData); // Sending the form data to PHP
};