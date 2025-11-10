const form = document.querySelector(".signup form");
const continueBtn = document.querySelector(".button input");
const errorText = form.querySelector(".error-txt");



form.onsubmit = (e) => {
  e.preventDefault(); 
};

continueBtn.onclick = () => {
   // Hide any previous error messages
  errorText.style.display = "none";
    
    // Show spinner
  showSpinner();
    
    // Start AJAX
    let xhr = new XMLHttpRequest(); 
    xhr.open("POST", "php/addCategories.php", true);
    xhr.onload = () => {
        // Hide spinner when request completes
        hideSpinner();
        
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                let data = xhr.response;
                console.log(data);
                
                try {
                    // Try to parse as JSON first
                    const response = JSON.parse(data);
                    
                    if (response.success) {
                        showToast(response.message, 'success');
                        setTimeout(() => {
                            location.href = "/quiz/admin/exam_category.php";
                        }, 1500); // Redirect after 1.5 seconds to see the toast
                    } else {
                        showToast(response.message, 'error');
                        errorText.textContent = response.message;
                        errorText.style.display = "block";
                    }
                } catch (e) {
                    // If not JSON, handle as plain text
                    if (data.trim() == "success") {
                        showToast("Exam successfully added!", 'success');
                        setTimeout(() => {
                            location.href = "/quiz/admin/exam_category.php";
                        }, 1500);
                    } else {
                        showToast(data, 'error');
                        errorText.textContent = data;
                        errorText.style.display = "block";
                    }
                }
            } else {
                hideSpinner();
                showToast("Network error occurred. Please try again.", 'error');
            }
        }
    };
    
    xhr.onerror = () => {
        hideSpinner();
        showToast("Network error occurred. Please check your connection.", 'error');
    };
    
    // Handle timeout
    xhr.timeout = 30000; // 30 seconds timeout
    xhr.ontimeout = () => {
        hideSpinner();
        showToast("Request timeout. Please try again.", 'error');
    };
    
    let formData = new FormData(form); 
    xhr.send(formData); 
};





