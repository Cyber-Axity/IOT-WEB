document.querySelectorAll(".eye").forEach(icon => {
  icon.addEventListener("click", () => {
    const input = icon.previousElementSibling; // assumes input comes before the icon
    
    if (input.type === "password") {
      input.type = "text"; // show password
      icon.classList.remove("bx-show");
      icon.classList.add("bx-hide"); // switch to open eye
    } else {
      input.type = "password"; // hide password
      icon.classList.remove("bx-hide");
      icon.classList.add("bx-show"); // switch to slashed eye
    }
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const alertBox = document.getElementById("error-alert");
  if (alertBox) {
    setTimeout(() => {
      alertBox.style.transition = "opacity 0.5s ease";
      alertBox.style.opacity = "0";

      setTimeout(() => alertBox.remove(), 500);
    }, 3000);
  }
});


