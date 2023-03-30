const new_password = document.getElementById("new_password");
const confirm_password = document.getElementById("confirm_password");

function validatePassword() {
    if (new_password.value != confirm_password.value) {
        confirm_password.setCustomValidity("Passwords don't match");
    } else {
        confirm_password.setCustomValidity("");
    }
}

new_password.onchange = validatePassword;
confirm_password.onkeyup = validatePassword;
