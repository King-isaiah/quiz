

const form = document.querySelector(".login form");
const continueBtn = document.querySelector(".btn");
const errorText = form.querySelector(".alert");
const spinnerOverlay = document.getElementById("spinnerOverlay");

// Function to show spinner
function showSpinner() {
    spinnerOverlay.style.display = 'flex';
    form.classList.add('form-disabled');
    continueBtn.disabled = true;
}

// Function to hide spinner
function hideSpinner() {
    spinnerOverlay.style.display = 'none';
    form.classList.remove('form-disabled');
    continueBtn.disabled = false;
}

form.onsubmit = (e) => {
    e.preventDefault();
};

continueBtn.onclick = () => {
    errorText.style.display = "none";
    showSpinner();
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "/admin/php/adminlogin.php", true);
    // xhr.open("POST", "php/adminlogin.php", true);
    
    xhr.onload = () => {
        hideSpinner();
        
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                let data = xhr.response;
                
                try {
                    const result = JSON.parse(data);
                    
                    if (result?.success) {
                        alert(result?.message);
                        // location.href = "/quiz/admin/exam_category.php";
                        location.href = "/admin/exam_category.php";
                    } else {
                        errorText.textContent = result?.message;
                        errorText.style.display = "block";
                    }
                } catch (e) {
                    errorText.textContent = data;
                    errorText.style.display = "block";
                }
            } else {
                errorText.textContent = "Network error occurred. Please try again.";
                errorText.style.display = "block";
            }
        }
    };
    
    xhr.onerror = () => {
        hideSpinner();
        errorText.textContent = "Network error occurred. Please check your connection.";
        errorText.style.display = "block";
    };
    
    let formData = new FormData(form); 
    xhr.send(formData);
};
