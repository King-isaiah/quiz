"use strict";
const pswrdField = document.querySelector(".form input[type='password']");
const toggleBtn = document.querySelector(".form .field i");
// the eye icon{the view nd unview}
toggleBtn.onclick = function () {
  if (pswrdField.type == "password") {
    pswrdField.type = "text";
    toggleBtn.classList.add("active");
  } else {
    pswrdField.type = "password";
    toggleBtn.classList.remove("active");
  }
};
