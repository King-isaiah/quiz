const form = document.querySelector(".login form");
const continueBtn = document.querySelector(".button input");
const errorText = form.querySelector(".error-txt");
const loader = document.querySelector(".loader");

form.onsubmit = (e) => {
  e.preventDefault();
};

continueBtn.onclick = () => {
  // Show loader and hide errors
  loader.style.display = "block";
  errorText.style.display = "none";
  continueBtn.disabled = true;
  
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/login.php", true);
  xhr.onload = () => {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        let data = xhr.response;
        console.log(data);
        
        // Hide loader
        loader.style.display = "none";
        continueBtn.disabled = false;
        
        if (data == "success") {
          // location.href = "/quiz/students/dashboard.php";
          // Get current domain and build full URL
          // var baseUrl = window.location.origin;
          // location.href = baseUrl + "/students/dashboard.php";
          location.href = " /students/dashboard.php";
        } else {
          errorText.textContent = data;
          errorText.style.display = "block";
        }
      }
    }
  };
  
  let formData = new FormData(form); 
  xhr.send(formData); 
};