// const form = document.querySelector(".signup form");
// const continueBtn = document.querySelector(".button input");
// const successText = form.querySelector(".succes-txt");
// const errorText = form.querySelector(".error-txt");
// const loader = document.querySelector(".loader");

// form.onsubmit = (e) => {
//   e.preventDefault();
// };
// continueBtn.onclick = () => {
    
//   loader.style.display = "block";
//   errorText.style.display = "none";
//   continueBtn.disabled = true;
//   //   lets start ajax
//   let xhr = new XMLHttpRequest(); 
//   xhr.open("POST", "php/signup.php", true);
//   xhr.onload = () => {
//     if (xhr.readyState === XMLHttpRequest.DONE) {
//       if (xhr.status === 200) {
//         let data = xhr.response;
//         console.log(data);

//          // Hide loader
//         loader.style.display = "none";
//         continueBtn.disabled = false;


//         if (data == "success") {
//           successText.textContent = data;
//           successText.style.display = "block";
//           // location.href = "/quiz/lumers/login.html";
//           location.href = "lumers/login.php";
//         } else {
//           errorText.textContent = data;
//           errorText.style.display = "block";
//         }
//       }
//     }
//   };
//   //   we have to send the form data through ajax to php
//   let formData = new FormData(form); 
//   xhr.send(formData); //sending the form data to php
// };



const form = document.querySelector(".signup form");
const continueBtn = document.querySelector(".button input");
const successText = form.querySelector(".succes-txt");
const errorText = form.querySelector(".error-txt");
const fullpageLoader = document.getElementById("fullpageLoader");

form.onsubmit = (e) => {
  e.preventDefault();
};

continueBtn.onclick = () => {
  // Show full page loader
  fullpageLoader.style.display = "flex";
  
  // Disable form interactions
  form.classList.add("form-disabled");
  continueBtn.disabled = true;
  
  // Hide any previous messages
  errorText.style.display = "none";
  successText.style.display = "none";

  // Start AJAX request
  let xhr = new XMLHttpRequest(); 
  xhr.open("POST", "php/signup.php", true);
  
  xhr.onload = () => {
    // Hide loader first
    fullpageLoader.style.display = "none";
    form.classList.remove("form-disabled");
    continueBtn.disabled = false;

    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        let data = xhr.response;
        console.log("Server Response:", data);

        if (data.trim() === "success") {
          successText.textContent = "Account created successfully!";
          successText.style.display = "block";
          successText.style.color = "#34721c";
          successText.style.background = "#d4edda";
          successText.style.border = "1px solid #c3e6cb";

          setTimeout(() => {
            var baseUrl = window.location.origin;
            location.href = baseUrl + "/lumers/login.php";
            // location.href = "lumers/login.php";
          }, 5000);
         
        } else { 
          errorText.textContent = data;
          errorText.style.display = "block";         
         
          
        }
      }
    }
  };

  // Handle network errors
  xhr.onerror = () => {
    fullpageLoader.style.display = "none";
    form.classList.remove("form-disabled");
    continueBtn.disabled = false;
    errorText.textContent = "Network error! Please check your connection.";
    errorText.style.display = "block";
  };

  // Handle timeout
  xhr.ontimeout = () => {
    fullpageLoader.style.display = "none";
    form.classList.remove("form-disabled");
    continueBtn.disabled = false;
    errorText.textContent = "Request timeout! Please Use a Stronger Connection ndtry again.";
    errorText.style.display = "block";
  };

  // Set timeout (10 seconds)
  xhr.timeout = 40000;
  
  // Send form data
  let formData = new FormData(form); 
  xhr.send(formData);
};