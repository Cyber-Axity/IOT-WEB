// const formContainer = document.querySelector('.form_container');
const spwShowHide = document.querySelectorAll('.spw');

// document.querySelectorAll('#signup').forEach(btn => {
//     btn.addEventListener("click", (e) => {
//         e.preventDefault();
//         formContainer.classList.add("active");
//     });
// });

// document.querySelectorAll('#signin').forEach(btn => {
//     btn.addEventListener("click", (e) => {
//         e.preventDefault();
//         formContainer.classList.remove("active");
//     });
// });

spwShowHide.forEach(icon => {
    icon.addEventListener("click", () => {
        let getPwInput = icon.parentElement.querySelector("input");
        if(getPwInput.type === "password"){
            getPwInput.type = "text";
            icon.classList.replace("bi-eye-slash-fill", "bi-eye-fill");
        }else{
            getPwInput.type = "password";
            icon.classList.replace("bi-eye-fill", "bi-eye-slash-fill");
        }
    });
});

// Remove error query from URL after 2 seconds
document.addEventListener('DOMContentLoaded', function () {
    const errorElement = document.querySelector('.error');
    if (errorElement) {
        setTimeout(function () {
            errorElement.style.display = 'none';
            // Clear error from URL without reloading
            const url = new URL(window.location);
            url.searchParams.delete('error');
            window.history.replaceState({}, document.title, url);
        }, 2000); // Clear error after 3 seconds
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    const errorElement = document.querySelector('.success');
    if (errorElement) {
        setTimeout(function () {
            errorElement.style.display = 'none';
            // Clear error from URL without reloading
            const url = new URL(window.location);
            url.searchParams.delete('success');
            window.history.replaceState({}, document.title, url);
        }, 4000); // Clear error after 3 seconds
    }
  });

