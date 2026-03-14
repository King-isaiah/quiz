const form = document.querySelector(".signup form");
const continueBtn = document.querySelector(".button input");
const errorText = form.querySelector(".error-txt");

// Prevent the form from submitting by default
form.onsubmit = (e) => {
    e.preventDefault();
}

continueBtn.onclick = () => {
    // Create an AJAX request
    let xhr = new XMLHttpRequest(); 
    xhr.open("POST", "php/profileUpdate.php", true);
    
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            console.log("Raw response:", xhr.responseText); 
            
            if (xhr.status === 200) {
                try {
                    let responseData = JSON.parse(xhr.response);
                    console.log(responseData); 
                    
                   
                    Toastify({
                        text: responseData.message, 
                        duration: 3000,
                        newWindow: true,
                        close: true,
                        gravity: "top", 
                        position: "right", 
                        stopOnFocus: true, 
                        style: {
                            background: responseData.success ? "linear-gradient(to right, #4CAF50, #81C784)" : "linear-gradient(to right, #FF4C4C, #FF9E9E)", // Green for success, red for error
                        },
                        onClick: function() {} 
                    }).showToast();

                    // Redirect on successful update
                    if (responseData.success) {
                        setTimeout(() => {
                            location.href = "/quiz/students/Account-Settings.php"; 
                        }, 3000); 
                    }
                    
                } catch (error) {
                    console.error("JSON parse error:", error);
                    console.log("Actual response:", xhr.responseText);
                    
                    // Show error to user
                    Toastify({
                        text: "Server returned invalid response. Please try again.",
                        duration: 3000,
                        style: { 
                            background: "linear-gradient(to right, #FF4C4C, #FF9E9E)" 
                        },
                    }).showToast();
                }
            } else {                
                errorText.textContent = "An error occurred while processing your request.";
                errorText.style.display = "block";
            }
        }
    };

    let formData = new FormData(form); 
    xhr.send(formData); 
};