const registerForm = document.querySelector("form");

if (registerForm) {

    registerForm.addEventListener("submit", function(e) {

        let errors = [];

        const firstname = document.getElementById("firstname");

        const lastname = document.getElementById("lastname");

        const email = document.getElementById("email");

        const password = document.getElementById("password");

        const confirmPwd = document.getElementById("confirme-pwd");

        const phone = document.getElementById("phone");

        if (firstname && firstname.value.length < 2) {
            errors.push("Prénom invalide");
        }

        if (lastname && lastname.value.length < 2) {
            errors.push("Nom invalide");
        }
        
        if (email) {

            const email_pattern =
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!email_pattern.test(email.value)) {
                errors.push("Email invalide");
            }
        }

        if (password && password.value.length < 8) {
            errors.push("Le mot de passe doit faire 8 caractères");
        }

        if (password && confirmPwd && password.value !== confirmPwd.value) {
            errors.push("Les mots de passe ne correspondent pas");
        }

        if (phone) {
            const phone_pattern = /^[0-9+\s]+$/;

            if (!phone_pattern.test(phone.value)) {
                errors.push("Téléphone invalide");
            }
        }

        if (errors.length > 0) {

            e.preventDefault();

            alert(errors.join("\n"));
        }
    });
}


const togglePassword = document.getElementById("togglePassword");

const passwordInput = document.getElementById("password");

if (togglePassword && passwordInput) {

    togglePassword.addEventListener("click", () => {

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
        }
        else {
            passwordInput.type = "password";
        }
    });
}

const counter = document.getElementById("passwordCounter");

if (passwordInput && counter) {

    passwordInput.addEventListener("input", () => {

        counter.textContent = `${passwordInput.value.length} / 20`;
    });
}



const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");

const confirmPasswordInput = document.getElementById("confirme-pwd");

if (toggleConfirmPassword && confirmPasswordInput) {

    toggleConfirmPassword.addEventListener("click", () => {

        if (confirmPasswordInput.type === "password") {
            confirmPasswordInput.type = "text";
        }
        else {
            confirmPasswordInput.type = "password";
        }
    });
}

const confirmPasswordCounter = document.getElementById("confirmPassword_counter");

if (confirmPasswordInput && confirmPasswordCounter
) {

    confirmPasswordInput.addEventListener("input", () => {

    confirmPasswordCounter.textContent = `${confirmPasswordInput.value.length} / 20`;
    });
}


const loginForm = document.querySelector("form");

if (loginForm) {

    loginForm.addEventListener("submit", function(e) {

        let errors = [];

        const email = document.getElementById("email");
        const password = document.getElementById("password");

        const email_pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (email && !email_pattern.test(email.value)) {
            errors.push("Email invalide");
        }

        if (password && password.value.length === 0) {
            errors.push("Mot de passe requis");
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join("\n"));
        }
    });
}